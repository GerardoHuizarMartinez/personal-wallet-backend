@extends('template')

@section('content')
<h1> Listado </h1>

@foreach ($prueba as $uwu)
    <div style="display: flex; flex-direction: column;">
        <strong> {{$uwu->id}}</strong>
        <a href="{{ route('post', $uwu->atributo) }}"> {{$uwu->name}} esta bien <strong> {{$uwu->atributo}} </strong> </a>
        <br>
        <span> {{ $uwu->user->name}} </span>
    </div>
@endforeach

{{ $prueba->links() }}

@endsection
