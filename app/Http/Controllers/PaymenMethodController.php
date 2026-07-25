<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymenMethodController
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        try {
            $methods = PaymentMethod::select('id', 'name', 'label', 'icon')->orderBy("id")->get();
            return response()->json(["payment_method" => $methods, "status" =>  200]);
        } catch (\Exception $error) {
            return response()->json(["error" => $error->getMessage(), "code" => 200]);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
}
