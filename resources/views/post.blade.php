@extends('template')

@section('content')

<div class="max-w-3xl mx-auto"> </div>

<h1 class="text-5xl mb-8"> {{$puto->name}} </h1>
<p class="leading-loose text-lg text-gray-700">
    {{$puto->body}}
</p>


@endsection
