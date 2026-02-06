<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Colony;

class ColoniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputFileReader = public_path('Codigos_postales.xlsx');
        $reader = IOFactory::createReader('Xlsx');

        $spreadsheet = $reader->load($inputFileReader);
        $worksheet = $spreadsheet->getActiveSheet();

        for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {
            $zipcode = $worksheet->getCell("B$row")->getValue();
            $name    = $worksheet->getCell(("C$row"))->getValue();
            $settlement_type_code = $worksheet->getCell("D$row")->getValue();
            $settlement_type  = $worksheet->getCell("E$row")->getValue();
            $city_code = $worksheet->getCell("F$row")->getValue();
            $city   = $worksheet->getCell("G$row")->getValue();
            $state_code = $worksheet->getCell("H$row")->getValue();
            $state = $worksheet->getCell("I$row")->getValue();
            $office_code = $worksheet->getCell("J$row")->getValue();
            $zone   = $worksheet->getCell("K$row")->getValue();

            Colony::create([
                'zipcode' => $zipcode,
                'name' => $name,
                'settlement_type_code' => $settlement_type_code,
                'settlement_type' => $settlement_type,
                'city_code' => $city_code,
                'city' => $city,
                'state_code' => $state_code,
                'state' => $state,
                'office_code' => $office_code,
                'zone' => $zone,
            ]);
        }
    }
}
