<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class pageController extends Controller
{

    public function home(Request $request){

        $search = $request->search;
        $post = Post::where('name', 'LIKE', "%{$search}%")->with('user')->latest()->paginate();
        return view('home', ['posts' => $post]);
    }
    
    public function post(Post $post){

        return view('post', ['puto'=> $post]);
    }
}
