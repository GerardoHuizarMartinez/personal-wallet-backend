<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Purchase;
use App\Models\Expense;

class PurchasesSeeder extends Seeder
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
            $total_price    = $worksheet->getCell(("C$row"))->getValue();
            $user_id = $worksheet->getCell("D$row")->getValue();
            $category_id  = $worksheet->getCell("E$row")->getValue();
            $financing_type_id = $worksheet->getCell("F$row")->getValue();
            $financing_method   = $worksheet->getCell("G$row")->getValue();
            $payment_method_id = $worksheet->getCell("H$row")->getValue();
            $comments = $worksheet->getCell("I$row")->getValue();
            $purchase_date = $worksheet->getCell("A$row")->getValue();

            if (is_numeric($purchase_date)) {
                $purchaseDate = Date::excelToDateTimeObject($purchase_date)->format('Y-m-d');
            } else {
                $purchaseDate = date('Y-m-d', strtotime($purchase_date));
            }

            $purchase = Purchase::create([
                'product_name' => $product_name,
                'total_price' => $total_price,
                'user_id' => $user_id,
                'category_id' => $category_id,
                'financing_type_id' => $financing_type_id,
                'financing_method' => $financing_method,
                'payment_method_id' => $payment_method_id,
                'comments' => $comments,
                'purchase_date' => $purchaseDate,
            ]);

            Expense::create([
                'user_id' => $purchase->user_id,
                'amount' => $purchase->total_price,
                'expense_date' => $purchase->purchase_date,
                'description' => $purchase->product_name,
                'purchase_id' => $purchase->id,
                'payment_method_id' => $payment_method_id,
            ]);
        }
    }
}
