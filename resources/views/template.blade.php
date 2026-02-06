<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto web</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body>

    <div class="container px-4 mx-auto">
        <header class="flex justify-between items-center py-4">
            <div class="flex items-center flex-grow gap-4">
                <a href="{{ route('home')}}">
                    <img src=" {{ asset('images/elefante.png') }} " class="h-12" alt="">
                </a>

                <form action=" {{ route('home') }}"  method="GET" class="flex-grow">
                    <input type="text" class=" border rounded-lg border-gray-300 py-2 px-5 w-1/2"name="search" placeholder="Buscar" value= " {{request('search')}}">
                </form>
            </div>

            @auth
            <a href="{{ route('dashboard')}}">Dashboard</a>
            @else
            <a href="{{ route('login')}}">Login</a>
            @endauth
        </header>

        <div
            style="background: linear-gradient(to right, 
            rgba(200,200,200,0) 0%,
            rgba(200,200,200,1) 30%,
            rgba(200,200,200,1) 40%,
            rgba(200,200,200,0) 100%);
            height:2px; margin-bottom: 16px; opacity:0.6">
        </div>

        @yield('content')

        <p class="py-16">
            <img src=" {{ asset('images/elefante.png') }} " class="h-12 mx-auto" alt="">
        </p>
    </div>

</body>

</html>