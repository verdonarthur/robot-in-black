<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Robot In Black</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-mono antialiased">
<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center lg:pt-32">
    <div class="mx-auto max-w-4xl ">
        <h1 class="text-5xl font-medium tracking-tight text-slate-900 sm:text-7xl uppercase">Robot In Black</h1>
        <p>Create AI Agents based on your Documentation</p>
    </div>
    <div class="mt-5 flex justify-center">
        <ul class="list-disc text-left">
            <li>Document's contents are encrypted</li>
            <li>Personalise your agent's prompt</li>
            <li>Live edit your document</li>
        </ul>
    </div>
    <div class="mt-5 flex items-stretch justify-center">
        <a class="bg-black py-2 px-4 rounded text-white hover:bg-white hover:text-black mr-2"
           href="{{ \App\Filament\Console\Resources\AgentResource::getUrl() }}">Go to Console</a>
        <button class="bg-black py-2 px-4 text-white rounded  disabled:text-gray-500 disabled:bg-gray-100" disabled>
            Create an account
        </button>
    </div>
</main>
</body>
</html>
