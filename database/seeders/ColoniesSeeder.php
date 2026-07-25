<?php

namespace Database\Seeders;

use App\Models\Colony;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private int $startRow = 0;
    private int $endRow = 0;

    public function setRows(int $startRow, int $chunkSize): void
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return $row === 1 || ($row >= $this->startRow && $row <= $this->endRow);
    }
}

class ColoniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $inputFileReader = public_path('Codigos_postales.xlsx');
    //     $reader = IOFactory::createReader('Xlsx');
    //     $reader->setReadDataOnly(true); // 👈 no carga estilos, ahorra mucha memoria

    //     $spreadsheet = $reader->load($inputFileReader);
    //     $worksheet = $spreadsheet->getActiveSheet();

    //     $highestRow = $worksheet->getHighestRow();

    //     DB::disableQueryLog(); // 👈 evita que Laravel guarde cada query en memoria

    //         for ($row = 2; $row <= $worksheet->getHighestRow(); $row++) {
    //         $zipcode = $worksheet->getCell("B$row")->getValue();
    //         $name    = $worksheet->getCell(("C$row"))->getValue();
    //         $settlement_type_code = $worksheet->getCell("D$row")->getValue();
    //         $settlement_type  = $worksheet->getCell("E$row")->getValue();
    //         $city_code = $worksheet->getCell("F$row")->getValue();
    //         $city   = $worksheet->getCell("G$row")->getValue();
    //         $state_code = $worksheet->getCell("H$row")->getValue();
    //         $state = $worksheet->getCell("I$row")->getValue();
    //         $office_code = $worksheet->getCell("J$row")->getValue();
    //         $zone   = $worksheet->getCell("K$row")->getValue();

    //         }

    //         Colony::create([
    //             'zipcode' => $zipcode,
    //             'name' => $name,
    //             'settlement_type_code' => $settlement_type_code,
    //             'settlement_type' => $settlement_type,
    //             'city_code' => $city_code,
    //             'city' => $city,
    //             'state_code' => $state_code,
    //             'state' => $state,
    //             'office_code' => $office_code,
    //             'zone' => $zone,
    //         ]);


    //     $this->command->info('✅ Colonias insertadas correctamente.');
    // }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // public function run(): void
    // {
    //     $file = public_path('Codigos_postales.csv');
    //     $handle = fopen($file, 'r');

    //     $chunk = [];
    //     $chunkSize = 1000;
    //     $firstRow = true;

    //     while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //         // salta el header
    //         if ($firstRow) {
    //             $firstRow = false;
    //             continue;
    //         }

    //         $chunk[] = [
    //             'zipcode'              => $row[1],
    //             'name'                 => $row[2],
    //             'settlement_type_code' => $row[3],
    //             'settlement_type'      => $row[4],
    //             'city_code'            => $row[5],
    //             'city'                 => $row[6],
    //             'state_code'           => $row[7],
    //             'state'                => $row[8],
    //             'office_code'          => $row[9],
    //             'zone'                 => $row[10],
    //             'created_at'           => now(),
    //             'updated_at'           => now(),
    //         ];

    //         if (count($chunk) === $chunkSize) {
    //             DB::table('colonies')->insert($chunk);
    //             $chunk = [];
    //         }
    //     }

    //     if (!empty($chunk)) {
    //         DB::table('colonies')->insert($chunk);
    //     }

    //     fclose($handle);
    //     $this->command->info('✅ Colonias insertadas correctamente.');
    // }


    /*
    convertir el Excel a CSV pero desde PHP, no desde Excel, para evitar que Excel corrompa los datos:   

    PRIMERO
    php -d memory_limit=1G artisan tinker

    SEGUNDO
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load(public_path('Codigos_postales.xlsx'));
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
    $writer->setDelimiter(',');
    $writer->setEnclosure('"');
    $writer->setUseBOM(true);
    $writer->save(public_path('Codigos_postales.csv'));
    echo "Listo";
 */
    public function run(): void
    {
        $file = public_path('Codigos_postales.csv');
        $handle = fopen($file, 'r');

        // saltar BOM si existe
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $chunk = [];
        $chunkSize = 1000;
        $firstRow = true;

        while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $chunk[] = [
                'zipcode'              => $row[1],
                'name'                 => $row[2],
                'settlement_type_code' => $row[3],
                'settlement_type'      => $row[4],
                'city_code'            => $row[5],
                'city'                 => $row[6],
                'state_code'           => $row[7],
                'state'                => $row[8],
                'office_code'          => $row[9],
                'zone'                 => $row[10],
                'created_at'           => now(),
                'updated_at'           => now(),
            ];

            if (count($chunk) === $chunkSize) {
                DB::table('colonies')->insert($chunk);
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            DB::table('colonies')->insert($chunk);
        }

        fclose($handle);
        $this->command->info('✅ Colonias insertadas correctamente.');
    }
}
