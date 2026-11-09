<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ImportController
{
    private const MAX_ROWS = 1000;

    // Última fila de la plantilla con validaciones (fila 1 = encabezados).
    private const LAST_ROW = 1001;

    // Métodos de pago (label) que no se ofrecen ni se aceptan en la carga masiva.
    private const EXCLUDED_METHODS = ['credit-card'];

    // Columna G (sin encabezado, visible pero discreta en la plantilla): timestamp opcional para created_at.
    private const TIMESTAMP_COLUMN = 6;

    private const HEADERS = ['Concepto', 'Monto', 'Categoría', 'Método de pago', 'Fecha (AAAA-MM-DD)', 'Comentarios (opcional)'];

    /**
     * Descarga la plantilla de Excel (vacía, con listas y validaciones) para la carga masiva.
     */
    public function template(string $type)
    {
        $isIncome = $type === 'income';

        $categories = Category::where('type', $type)->orderBy('name')->pluck('name')->all();
        $methods = PaymentMethod::whereNotIn('label', self::EXCLUDED_METHODS)->orderBy('id')->pluck('name')->all();

        $spreadsheet = new Spreadsheet();

        // ===== Hoja 1: plantilla =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Plantilla');
        $sheet->fromArray(self::HEADERS, null, 'A1');

        // La fecha es una celda de fecha real: Excel la muestra siempre como AAAA-MM-DD.
        $last = self::LAST_ROW;
        $sheet->getStyle("E2:E{$last}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $sheet->getStyle("B2:B{$last}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Listas desplegables estrictas (no aceptan valores fuera del catálogo).
        // Referencia directa a la hoja de catálogos (más compatible que un rango con nombre:
        // Excel web/Mac, Google Sheets y LibreOffice no siempre resuelven los nombres).
        $categoriesRange = 'Catalogos!$A$1:$A$' . max(count($categories), 1);
        $methodsRange = 'Catalogos!$B$1:$B$' . max(count($methods), 1);
        $this->addListValidation($sheet, 'C', $categoriesRange, 'Categoría', $this->optionsPrompt($categories));
        $this->addListValidation($sheet, 'D', $methodsRange, 'Método de pago', $this->optionsPrompt($methods));

        // Monto: número mayor a 0.
        $amountRule = $sheet->getCell('B2')->getDataValidation();
        $amountRule->setType(DataValidation::TYPE_DECIMAL)
            ->setOperator(DataValidation::OPERATOR_GREATERTHAN)
            ->setFormula1('0')
            ->setAllowBlank(false)
            ->setShowErrorMessage(true)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setErrorTitle('Monto inválido')
            ->setError('Escribe un número mayor a 0.')
            ->setShowInputMessage(true)
            ->setPromptTitle('Monto')
            ->setPrompt('Mayor a 0');
        $sheet->setDataValidation("B2:B{$last}", $amountRule);

        // Fecha: solo fechas válidas, hoy o anteriores; Excel las formatea como AAAA-MM-DD.
        $dateRule = $sheet->getCell('E2')->getDataValidation();
        $dateRule->setType(DataValidation::TYPE_DATE)
            ->setOperator(DataValidation::OPERATOR_BETWEEN)
            ->setFormula1('36526') // 2000-01-01
            ->setFormula2('TODAY()') // no se permiten fechas futuras
            ->setAllowBlank(false)
            ->setShowErrorMessage(true)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setErrorTitle('Fecha inválida')
            ->setError('Escribe una fecha válida, de hoy o anterior.')
            ->setShowInputMessage(true)
            ->setPromptTitle('Fecha')
            ->setPrompt('Ej. 2026-11-08');
        $sheet->setDataValidation("E2:E{$last}", $dateRule);

        // Comentarios: opcional, máximo 250 caracteres.
        $commentRule = $sheet->getCell('F2')->getDataValidation();
        $commentRule->setType(DataValidation::TYPE_TEXTLENGTH)
            ->setOperator(DataValidation::OPERATOR_LESSTHANOREQUAL)
            ->setFormula1('250')
            ->setAllowBlank(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setErrorTitle('Comentario muy largo')
            ->setError('Máximo 250 caracteres.')
            ->setShowInputMessage(true)
            ->setPromptTitle('Opcional')
            ->setPrompt('Puedes dejarlo vacío');
        $sheet->setDataValidation("F2:F{$last}", $commentRule);

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E9E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        // Encabezado de la columna opcional en gris para distinguirla de las obligatorias.
        $sheet->getStyle('F1')->getFill()->getStartColor()->setRGB('7F8C9B');
        $sheet->getRowDimension(1)->setRowHeight(24);
        foreach (['A' => 34, 'B' => 14, 'C' => 26, 'D' => 22, 'E' => 22, 'F' => 36] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->freezePane('A2');

        // Columna G sin encabezado, sin avisos y más ancha que las demás (visible; el ancho es la pista para devs):
        // si trae un timestamp válido se usa como created_at.
        $sheet->getStyle("G2:G{$last}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        $sheet->getColumnDimension('G')->setWidth(40);
        // El formato también se aplica a toda la columna, por si se llenan filas más allá de LAST_ROW.
        $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
        // Solo acepta fecha y hora válidas (no futuras); sin mensaje de ayuda a propósito.
        $timestampRule = $sheet->getCell('G2')->getDataValidation();
        $timestampRule->setType(DataValidation::TYPE_DATE)
            ->setOperator(DataValidation::OPERATOR_BETWEEN)
            ->setFormula1('36526') // 2000-01-01
            ->setFormula2('NOW()')
            ->setAllowBlank(true)
            ->setShowErrorMessage(true)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setErrorTitle('Valor inválido')
            ->setError('Escribe una fecha y hora válidas: AAAA-MM-DD hh:mm:ss.');
        $sheet->setDataValidation("G2:G{$last}", $timestampRule);

        // ===== Hoja oculta con los valores permitidos (alimenta los desplegables) =====
        $catalog = $spreadsheet->createSheet();
        $catalog->setTitle('Catalogos');
        foreach ($categories as $i => $name) {
            $catalog->setCellValue('A' . ($i + 1), $name);
        }
        foreach ($methods as $i => $name) {
            $catalog->setCellValue('B' . ($i + 1), $name);
        }
        $catalog->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // ===== Hoja de instrucciones =====
        $help = $spreadsheet->createSheet();
        $help->setTitle('Instrucciones');
        $lines = [
            'Cómo llenar la plantilla de ' . ($isIncome ? 'ingresos' : 'gastos'),
            '',
            '• Llena la hoja "Plantilla" desde la fila 2. Todas las columnas son obligatorias, excepto Comentarios.',
            '• Categoría y Método de pago se eligen de la lista desplegable.',
            '• Máximo ' . self::MAX_ROWS . ' filas por archivo.',
        ];
        foreach ($lines as $i => $line) {
            $help->setCellValue('A' . ($i + 1), $line);
        }
        $help->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $help->getColumnDimension('A')->setWidth(110);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'plantilla_' . ($isIncome ? 'ingresos' : 'gastos') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }

    /**
     * Carga masiva: valida todas las filas y guarda solo si todas son correctas.
     */
    public function import(Request $request, string $type)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt|max:2048',
        ], [
            'file.required' => 'Selecciona un archivo .xlsx o .csv.',
            'file.mimes' => 'El archivo debe ser .xlsx o .csv.',
            'file.max' => 'El archivo no debe pesar más de 2 MB.',
        ]);

        $isIncome = $type === 'income';

        try {
            $upload = $request->file('file');
            $path = $upload->getRealPath();

            if (strtolower($upload->getClientOriginalExtension()) === 'csv') {
                $reader = new Csv();
                // Excel en Windows suele guardar CSV en Windows-1252 en lugar de UTF-8.
                if (!mb_check_encoding((string) file_get_contents($path), 'UTF-8')) {
                    $reader->setInputEncoding('CP1252');
                }
                $sheet = $reader->load($path)->getSheet(0);
            } else {
                $sheet = IOFactory::load($path)->getSheet(0);
            }
            $rows = $sheet->toArray(null, true, false, false);
        } catch (Throwable $e) {
            return response()->json(['message' => 'No se pudo leer el archivo. Usa la plantilla descargable.'], 422);
        }

        array_shift($rows); // encabezados

        // Ignora filas completamente vacías conservando el número de fila real de Excel.
        $dataRows = [];
        foreach ($rows as $index => $row) {
            $row = array_slice($row, 0, self::TIMESTAMP_COLUMN + 1); // A:F + timestamp oculto (G)
            // El timestamp por sí solo no cuenta como fila con datos.
            if (collect(array_slice($row, 0, count(self::HEADERS)))->every(fn ($v) => $v === null || trim((string) $v) === '')) {
                continue;
            }
            $dataRows[$index + 2] = $row;
        }

        if (count($dataRows) === 0) {
            return response()->json(['message' => 'El archivo no contiene registros para importar.'], 422);
        }
        if (count($dataRows) > self::MAX_ROWS) {
            return response()->json(['message' => 'El archivo excede el máximo de ' . self::MAX_ROWS . ' filas.'], 422);
        }

        $categories = Category::where('type', $type)->get()
            ->mapWithKeys(fn ($c) => [$this->normalize($c->name) => $c->id]);
        $methods = PaymentMethod::whereNotIn('label', self::EXCLUDED_METHODS)->get();
        $methodsByKey = [];
        foreach ($methods as $m) {
            $methodsByKey[$this->normalize($m->name)] = $m->id;
            $methodsByKey[$this->normalize($m->label)] = $m->id;
        }

        $records = [];
        $errors = [];
        $dateColumn = $isIncome ? 'income_date' : 'expense_date';

        foreach ($dataRows as $rowNumber => $row) {
            [$name, $amount, $category, $method, $date, $comments, $timestamp] = array_pad($row, self::TIMESTAMP_COLUMN + 1, null);
            $rowErrors = [];

            $name = trim((string) $name);
            if ($name === '') {
                $rowErrors[] = 'El concepto es obligatorio';
            } elseif (mb_strlen($name) > 255) {
                $rowErrors[] = 'El concepto excede 255 caracteres';
            }

            if (is_string($amount)) {
                $amount = trim(str_replace(['$', ' '], '', $amount));
                // "45,50" (coma decimal) vs "1,250.50" (coma de miles)
                $amountClean = preg_match('/^\d+,\d{1,2}$/', $amount) ? str_replace(',', '.', $amount) : str_replace(',', '', $amount);
            } else {
                $amountClean = $amount;
            }
            if (!is_numeric($amountClean) || (float) $amountClean < 0.01) {
                $rowErrors[] = 'El monto debe ser un número mayor a 0';
            }

            $categoryId = $categories[$this->normalize((string) $category)] ?? null;
            if (!$categoryId) {
                $rowErrors[] = 'La categoría "' . trim((string) $category) . '" no existe';
            }

            $methodId = $methodsByKey[$this->normalize((string) $method)] ?? null;
            if (!$methodId) {
                $rowErrors[] = 'El método de pago "' . trim((string) $method) . '" no existe';
            }

            $parsedDate = $this->parseDate($date);
            if (!$parsedDate) {
                $rowErrors[] = 'La fecha debe tener el formato AAAA-MM-DD';
            } elseif ($parsedDate > now()->toDateString()) {
                $rowErrors[] = 'La fecha no puede ser futura';
            }

            $comments = trim((string) $comments);
            if (mb_strlen($comments) > 250) {
                $rowErrors[] = 'Los comentarios exceden 250 caracteres';
            }

            $createdAt = $this->parseTimestamp($timestamp);
            if ($createdAt && $createdAt->greaterThan(now())) {
                $rowErrors[] = 'El timestamp no puede ser una fecha futura';
            }
            $createdAt ??= now();

            if ($rowErrors) {
                $errors[] = ['row' => $rowNumber, 'messages' => $rowErrors];
                continue;
            }

            $records[] = [
                'product_name' => $name,
                'amount' => round((float) $amountClean, 2),
                'category_id' => $categoryId,
                'payment_method_id' => $methodId,
                $dateColumn => $parsedDate,
                'comments' => $comments !== '' ? $comments : null,
                'user_id' => 1, // temporal hasta tener auth
                'source' => 'import',
                'created_at' => $createdAt,
                'updated_at' => now(),
            ];
        }

        if ($errors) {
            return response()->json([
                'message' => 'Se encontraron errores en el archivo. No se importó ningún registro.',
                'errors' => $errors,
            ], 422);
        }

        DB::transaction(function () use ($records, $isIncome) {
            $model = $isIncome ? Income::class : Expense::class;
            foreach (array_chunk($records, 200) as $chunk) {
                $model::insert($chunk);
            }
        });

        return response()->json([
            'message' => count($records) . ($isIncome ? ' ingresos importados' : ' gastos importados') . ' correctamente.',
            'imported' => count($records),
        ], 201);
    }

    private function addListValidation(Worksheet $sheet, string $col, string $range, string $title, string $message): void
    {
        $rule = $sheet->getCell("{$col}2")->getDataValidation();
        $rule->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(false)
            ->setShowDropDown(true) // PhpSpreadsheet invierte el valor: true = muestra la flecha
            ->setShowErrorMessage(true)
            ->setErrorTitle('Valor no válido')
            ->setError('Elige una opción de la lista.')
            ->setShowInputMessage(true)
            ->setPromptTitle($title)
            ->setPrompt($message)
            ->setFormula1($range);
        $sheet->setDataValidation("{$col}2:{$col}" . self::LAST_ROW, $rule);
    }

    /** Aviso corto al seleccionar la celda (Excel limita a 255 caracteres). */
    private function optionsPrompt(array $values): string
    {
        $text = 'Elige: ' . implode(', ', $values);

        return mb_strlen($text) > 250 ? mb_substr($text, 0, 247) . '...' : $text;
    }

    private function normalize(string $value): string
    {
        return Str::of($value)->trim()->lower()->ascii()->toString();
    }

    /** Timestamp válido de la columna oculta; null si está vacío o no se puede interpretar. */
    private function parseTimestamp(mixed $value): ?Carbon
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value, config('app.timezone')));
            }

            return Carbon::parse(trim((string) $value), config('app.timezone'));
        } catch (Throwable $e) {
            return null;
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
            }

            $text = trim((string) $value);
            if (preg_match('#^\d{1,2}/\d{1,2}/\d{4}$#', $text)) {
                return Carbon::createFromFormat('d/m/Y', $text)->format('Y-m-d');
            }

            return Carbon::createFromFormat('Y-m-d', $text)->format('Y-m-d');
        } catch (Throwable $e) {
            return null;
        }
    }
}
