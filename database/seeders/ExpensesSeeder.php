<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Expense;

class ExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputFileReader = public_path('carga_masiva_ingresos_egresos.xlsx');
        $reader = IOFactory::createReader('Xlsx');

        $spreadsheet = $reader->load($inputFileReader);
        $worksheet = $spreadsheet->getSheet(0);

        for ($row = 2; $row < $worksheet->getHighestRow(); $row++) {

            $product_name = $worksheet->getCell("B$row")->getValue();
            $amount    = $worksheet->getCell(("C$row"))->getValue();
            $user_id = $worksheet->getCell("D$row")->getValue();
            $category_id  = $worksheet->getCell("E$row")->getValue();
            $payment_method_id = $worksheet->getCell("H$row")->getValue();
            $comments = $worksheet->getCell("I$row")->getValue();
            $expense_date = $worksheet->getCell("A$row")->getValue();

            if (is_numeric($expense_date)) {
                $purchaseDate = Date::excelToDateTimeObject($expense_date)->format('Y-m-d');
            } else {
                $purchaseDate = date('Y-m-d', strtotime($expense_date));
            }

           Expense::create([
                'product_name' => $product_name,
                'amount' => $amount,
                'user_id' => $user_id,
                'category_id' => $category_id,
                'payment_method_id' => $payment_method_id,
                'expense_date' => $purchaseDate,
                'comments' => $comments,
            ]);
        }
    }
}
