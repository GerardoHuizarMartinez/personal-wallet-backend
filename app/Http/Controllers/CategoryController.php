<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Exception;

use function Laravel\Prompts\error;

class CategoryController extends Controller
{
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request): never
    {
        abort(404);
    }

    /**
     * Display the resource.
     */
    public function list()
    {
        try {
            $categories = Category::select("id", "name", "icon")->orderBy("name", "asc")->get();
            return response()->json(["categories" => $categories, "status" =>  200]);
        } catch (Exception $error) {
            response()->json(["error" => $error, "code" => 21365]);
        }
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
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
