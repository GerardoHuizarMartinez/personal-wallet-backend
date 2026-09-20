<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Category;
use App\Services\ExpenseService;
use App\Mappers\ExpenseMapper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;



class ExpenseController
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {}


    public function list(Request $request)
    {

        $currentDate = Carbon::now();

        $startdate = $request->filled('start_date')
            ? Carbon::parse($request->query('start_date'))->startOfDay()
            : Carbon::create($currentDate->year, $currentDate->month, 1)->startOfDay();

        $enddate = $request->filled('end_date')
            ? Carbon::parse($request->query('end_date'))->endOfDay()
            : $currentDate->copy()->endOfDay();

        $list = Expense::select(
            'id',
            'product_name',
            'amount',
            'user_id',
            'category_id',
            'payment_method_id',
            'expense_date',
            'comments'
        )
            ->with([
                'category:id,name',
                'payment_method:id,name,label',
            ])
            ->whereBetween('expense_date', [$startdate, $enddate])
            ->where('user_id', 1)
            ->orderByDesc('id')
            ->get();

        // Y el summary se calcula sobre TODOS los registros, no solo la página
        $allList = Expense::whereBetween('expense_date', [$startdate, $enddate])
            ->where('user_id', 1)
            ->get(['amount']);

        $summaryPaymentMethod = $allList
            ->groupBy('financing_method')
            ->map(function ($items, $method) {
                return [
                    'method' => $method,
                    'total_count' => $items->count(),
                    'total_sum' => round((float) $items->sum('total_price'), 2),
                ];
            })
            ->values();

        return response()->json([
            'data' => $list,
            'grouped' => $summaryPaymentMethod,
            'current_date' => $currentDate,
            'total' => $list->count(),
            'status' => 200,
        ]);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'amount'            => 'required|numeric|min:0.01',
            'category_id'       => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'expense_date'      => 'required|date',
            'comments'          => 'nullable|string|max:250',
        ]);

        $expense = $this->expenseService->create($validated);

        return response()->json($expense, 201);
    }


    public function update($id, Request $request)
    {
        try {

            $validated = $request->validate([
                'product_name'      => 'required|string',
                'amount'            => 'required|numeric',
                'category_id'       => 'required|numeric',
                'payment_method_id' => 'required|numeric',
                'expense_date'      => 'required|date',
                'comments'          => 'nullable|string',
            ]);

            $expense = Expense::findOrFail($id);
            $expense->update($validated);
            $expense->load('category', 'payment_method'); // ← carga relaciones


            return response()->json(
                ExpenseMapper::toDashboard(collect([$expense]))->first()
            );
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id): JsonResponse
    {
        $expense = Expense::select(
            'id',
            'product_name',
            'amount',
            'user_id',
            'category_id',
            'payment_method_id',
            'expense_date',
            'comments'
        )
            ->with([
                'category:id,name',
                'payment_method:id,name,label',
            ])
            ->findOrFail($id);

        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {

            $expense = Expense::findOrFail($id);
            $expense->delete();

            return response()->json([
                'message' => 'Gasto eliminado correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadExcelWithExpensesByDate(Request $request)
    {
        ini_set('memory_limit', -1);

        try {

            $validated = $request->validate([
                'type' => 'required|in:income,expense',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $type = $validated['type'];
            $startDate = $validated['start_date'];
            $endDate = $validated['end_date'];

            $dateColumn = $type === 'income' ? 'income_date' : 'expense_date';
            $title = $type === 'income' ? 'Ingresos' : 'Gastos';
            $model = $type === 'income' ? Income::class : Expense::class;

            $records = $model::select('id', 'product_name', 'amount', 'category_id', 'payment_method_id', $dateColumn, 'comments')
                ->with([
                    'category:id,name',
                    'payment_method:id,name,label',
                ])
                ->whereBetween($dateColumn, [$startDate, $endDate])
                ->where('user_id', 1)
                ->orderBy($dateColumn)
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $recordsCount = $records->count();
            $lastRow = $recordsCount > 0 ? $recordsCount + 2 : 2;
            $totalRow = $lastRow + 1;

            $categoryNames = Category::where('type', $type)->pluck('name');

            $categoryPalette = [
                'E57373', 'F06292', 'BA68C8', '9575CD', '7986CB',
                '64B5F6', '4FC3F7', '4DD0E1', '4DB6AC', '81C784',
                'AED581', 'FFD54F', 'FFB74D', 'FF8A65', 'A1887F',
                '90A4AE', 'D32F2F', 'C2185B', '7B1FA2', '512DA8',
                '303F9F', '1976D2', '0288D1', '0097A7', '00796B',
                '388E3C', '689F38', 'F57C00', '5D4037',
            ];

            $categoryColors = [];
            foreach ($categoryNames as $index => $categoryName) {
                $categoryColors[$categoryName] = $categoryPalette[$index % count($categoryPalette)];
            }

            $getContrastFontColor = function (string $hex): string {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

                return $luminance > 0.6 ? '000000' : 'FFFFFF';
            };

            // ===== DISEÑO DEL SPREADSHEET =====

            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(13);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(13);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(20);

            $sheet->getDefaultRowDimension()->setRowHeight(20);

            $sheet->mergeCells('A1:F1');
            $sheet->getRowDimension(1)->setRowHeight(25);

            $sheet->getStyle('A1:F1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle('A2:F2')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            if ($recordsCount > 0) {
                $sheet->getStyle("A3:F{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A3:B{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("D3:F{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("B3:B{$lastRow}")->getFont()->setBold(true);

                $sheet->getStyle("C{$totalRow}:D{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle("D{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('"$"#,##0.00');

                if ($categoryNames->isNotEmpty()) {
                    $categoriesSheet = $spreadsheet->createSheet();
                    $categoriesSheet->setTitle('Listas');

                    foreach ($categoryNames as $index => $categoryName) {
                        $categoriesSheet->setCellValue('A' . ($index + 1), $categoryName);
                    }

                    $categoriesSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                    $spreadsheet->setActiveSheetIndex(0);

                    $categoryRangeName = 'ListaCategorias';
                    $spreadsheet->addNamedRange(new NamedRange(
                        $categoryRangeName,
                        $categoriesSheet,
                        '$A$1:$A$' . $categoryNames->count()
                    ));

                    $categoryValidation = new DataValidation();
                    $categoryValidation->setType(DataValidation::TYPE_LIST);
                    $categoryValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $categoryValidation->setAllowBlank(true);
                    $categoryValidation->setShowInputMessage(true);
                    $categoryValidation->setShowErrorMessage(true);
                    $categoryValidation->setShowDropDown(true);
                    $categoryValidation->setPromptTitle('Categoría');
                    $categoryValidation->setPrompt('Selecciona una categoría de la lista.');
                    $categoryValidation->setFormula1($categoryRangeName);

                    $conditionalStyles = [];
                    foreach ($categoryColors as $categoryName => $color) {
                        $conditional = new Conditional();
                        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
                        $conditional->setOperatorType(Conditional::OPERATOR_EQUAL);
                        $conditional->setConditions(['"' . $categoryName . '"']);
                        $conditional->getStyle()->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($color);
                        $conditional->getStyle()->getFont()
                            ->setBold(true)
                            ->setColor(new Color($getContrastFontColor($color)));
                        $conditionalStyles[] = $conditional;
                    }

                    $sheet->getStyle("F3:F{$lastRow}")->setConditionalStyles($conditionalStyles);

                    for ($validationRow = 3; $validationRow <= $lastRow; $validationRow++) {
                        $sheet->getCell("F{$validationRow}")->setDataValidation(clone $categoryValidation);
                    }
                }
            }

            // ===== LLENADO DE DATOS =====

            $sheet->setCellValue('A1', "{$title} detallados");
            $sheet->setCellValue('A2', '#');
            $sheet->setCellValue('B2', 'Fecha');
            $sheet->setCellValue('C2', 'Concepto');
            $sheet->setCellValue('D2', 'Precio');
            $sheet->setCellValue('E2', 'Método de pago');
            $sheet->setCellValue('F2', 'Categoría');

            $row = 3;
            $counter = 1;
            $total = 0;
            foreach ($records as $record) {
                $sheet->setCellValue("A{$row}", $counter);
                $sheet->setCellValue("B{$row}", Carbon::parse($record->{$dateColumn})->format('d-m-Y')); 
                $sheet->setCellValue("C{$row}", $record->product_name);
                $sheet->setCellValue("D{$row}", $record->amount);
                $sheet->setCellValue("E{$row}", $record->payment_method->name ?? '');
                $sheet->setCellValue("F{$row}", $record->category->name ?? 'no hay dato');
                $total += $record->amount;
                $row++;
                $counter++;
            }

            if ($recordsCount > 0) {
                $sheet->setCellValue("C{$totalRow}", 'Total');
                $sheet->setCellValue("D{$totalRow}", $total);
            }

            $startDateLabel = Carbon::parse($startDate)->format('d-m-Y');
            $endDateLabel = Carbon::parse($endDate)->format('d-m-Y');
            $name = "Reporte de {$title} del {$startDateLabel} al {$endDateLabel}.xlsx";
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => "500", 'data' => $e->getMessage()], 500);
        }
    }
}
