<?php

namespace App\Microservices;

use App\UanValue;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PurchaseMS  
{
    public static function storeUan($request)
    {
      

        DB::beginTransaction();

        try {
         
            DB::commit();
            return response()->json(["status" => '200', 'message' => 'El valor de UAN se ha creado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["status" => '500', 'message' => 'Hubo un error al crear el valor de la UAN'], 500);
        }
    }




}