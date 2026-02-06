<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;


class PostController extends Controller
{

   //Listado de la información
   public function index()
   {
      return view('posts.index', [
         'posts' => Post::latest()->paginate()
      ]);
   }

   //Formulario para crear el post
   public function create(Post $post)
   {
      return view('posts.create', ['post' => $post]);
   }

   //Guardado y creación del post
   public function store(Request $request)
   {
      $request->validate([
         'name' => 'required',
         'atributo' => 'required|unique:posts,atributo',
         'body' => 'required'
      ],[
            'name.required'=>'Este campo es requerido',
            'atributo.required'=>'El campo debe ser unico',
            'body.required'=>'Se necesita mínimo un párrafo',
        ]);

      $post = $request->user()->posts()->create([
         'name' => $request->name,
         'atributo' =>$request -> atributo,
         'body' => $request->body,
      ]);
      return redirect()->route('posts.edit', $post);
   }


   //Formulario para editar el post
   public function edit(Post $post)
   {
      error_log("Que recibe: " . json_encode($post));

      return view('posts.edit', ['post' => $post]);
   }

   //Actualización del post en la DB
   public function update(Request $request, Post $post)
   {
      $request->validate([
         'name' => 'required',
         'atributo' => 'required|unique:posts,atributo,' . $post->id,
         'body' => 'required'
      ]);

      $post->update([
         'name' => $request->name,
         'atributo' => $request->atributo,
         'body' => $request->body,
      ]);
      return redirect()->route('posts.edit', $post);
   }


   public function destroy(Post $post)
   {
      $post->delete();
      return back();
   }
}
