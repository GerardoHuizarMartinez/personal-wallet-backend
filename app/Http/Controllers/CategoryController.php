<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;


class CategoryController
{

    public function list()
    {

        try {
            $categories = Category::select("id", "name", "icon", "label" )->orderBy("id")->get();
            return response()->json(["category" => $categories, "status" =>  200]);
        } catch (Exception $error) {
            response()->json(["error" => $error, "code" => 200]);
        }
    }


    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
