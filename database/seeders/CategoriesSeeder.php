<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputFileReader = public_path('Categories.xlsx');
        $reader = IOFactory::createReader('Xlsx');

        $spreadsheet = $reader->load($inputFileReader);
        $worksheet = $spreadsheet->getActiveSheet();

        for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {

            $name = $worksheet->getCell("B$row")->getValue();
            $icon = $worksheet->getCell("C$row")->getValue();
            $dateExcel = $worksheet->getCell("D$row")->getValue();

            if (is_numeric($dateExcel)) {
                $created_at = Carbon::instance(Date::excelToDateTimeObject($dateExcel));
            } else {
                $dateExcel = str_replace(['p. m.', 'a. m.'], ['PM', 'AM'], $dateExcel);
                $created_at = Carbon::createFromFormat('d/m/Y h:i:s A', $dateExcel);
            }

            Category::create([
                'name' => $name,
                'icon' => $icon,
                'created_at' => $created_at->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
