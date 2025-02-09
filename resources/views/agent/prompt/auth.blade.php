<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RIB - Protected</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-mono antialiased">
<main>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center lg:pt-32">
        <form method="POST" action="{{ route('agent.auth-check', ['agent' => $agent]) }}" class="">
            @csrf
            <label class="">Enter Password:</label>
            <input type="password" name="password" required class="border mt-5 py-2 px-4">
            <button type="submit" class="mt-5 bg-black py-2 px-4 rounded text-white hover:bg-white hover:text-black mr-2">Submit</button>
        </form>
        @if ($errors->any())
            <p style="color: red;">{{ $errors->first() }}</p>
        @endif
    </div>
</main>
</body>
</html>
