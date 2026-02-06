@extends('template')

@section('content')
@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="bg-gray-900 px-20 py-16 rounded-lg mb-8 relative overflow-hidden">
    <span class="text-sm uppercase text-gray-700 bg-gray-400 rounded-full px-2 py-1"> Programación</span>
    <h1 class="text-3xl	text-white mt-4"> Blog </h1>
    <p class="text-sm text-gray-400 mt-2"> Proyecto de desarrollo web para profesionales</p>
    
    <img src=" {{ asset('images/dev.png') }}" class="absolute -right-20 -bottom-20  opacity-20 h-96" >
</div>

<div class="px-4">
    <h1 class="text-2xl mb-8 text-gray-900" style="margin-bottom: 16px"> Contenido tecnico </h1>

    <div class="grid grid-cols-1 gap-4 mb-4">
        @foreach($posts as $post)
        <a href=" {{ route('post', $post->atributo) }}" class="bg-gray-100 rounded-lg px-4 py-4">
            <p class="flex justify-between">
                <span class="uppercase text-gray-700 bg-gray-200 rounded-full px-2 py-1"> Tutorial</span>
                <span> {{$post->created_at->format('d/m/Y')}}</span>
            </p>

            <h2 class="text-lg text-gray-900 mt-2 "> {{ $post->name}}</h2>
            <h2 class="text-xs text-gray-900 mt-2 flex items-center gap-1 opacity-75 "> {{ $post->user->name}}</h2>
        </a>
        @endforeach
    </div>

    {{ $posts->links()}}

</div>

@endsection