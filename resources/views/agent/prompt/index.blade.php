<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RIB - {{ $agent->name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-mono antialiased">
<main>
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 pb-16 text-center lg:pt-32">
        <div class="mx-auto max-w-4xl ">
            <div class="search-card">
                <h1 class="text-5xl font-medium tracking-tight text-slate-900 sm:text-7xl uppercase">Agent {{ $agent->name }}</h1>
                <p class="mt-5 subtitle">
                    {{ $agent->chatOptions['searchSubtitle'] ?? 'What do you want to know ?'}}
                </p>
                <form id="searchForm" class="mt-5">
                    <textarea
                        class="w-full rounded border-black border p-4 min-h-52"
                        id="searchFormTextareaSearch"
                        name="search"
                        placeholder="{{ $agent->chatOptions['searchPlaceholder'] ?? 'How can I delete this page ?'}}"
                    ></textarea>

                    <button class="mt-5 bg-black py-2 px-4 rounded text-white hover:bg-white hover:text-black mr-2" type="submit">Search</button>
                </form>
            </div>
        </div>

        <div class="mt-5 result-container border-t font-sans text-left">
            <div class="mt-5 bg-gray-50 px-5 pt-3 min-h-80 w-full flex justify-center">
               <div class="result prose"></div>
            </div>
        </div>
    </main>

    <script type="module">
        const form = document.querySelector("form");
        const resultContainer = document.querySelector(
            ".result-container",
        );
        const result = document.querySelector(".result");

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            form.querySelector('button[type="submit"]').classList.add("hidden");

            const url = "{{ route('agent.prompt.prompt', $agent->id) }}";

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    search: document.querySelector("form").querySelector(
                        "textarea",
                    ).value,
                }),
            }).then(async (response) => {
                const json = await response.json();
                result.innerHTML = json.answer;

                form.querySelector('button[type="submit"]').classList.remove("hidden");
            });
        });
    </script>
</main>
</body>
</html>
