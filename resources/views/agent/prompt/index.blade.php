<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIB - {{ $agent->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet"
    >
    <style>
        * {
            box-sizing: border-box;
        }

        html, * {
            font-family: "Rubik", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        html {
            font-size: 62.5%;
        }

        body {
            margin: 0;
            padding: 0;
        }

        main {
            width: 100%;
            min-height: 100vh;
            padding-block: 5rem;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            background-color: #eae1ff;
            background-image: linear-gradient(
                var(--angle),
                #ffd6ac 0%,
                #ffffff 70%
            );
        }

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            mix-blend-mode: hard-light;
            opacity: 0.5;
        }

        @property --angleone {
            syntax: "<angle>";
            initial-value: 0deg;
            inherits: false;
        }

        @property --angletwo {
            syntax: "<angle>";
            initial-value: 90deg;
            inherits: false;
        }

        @property --anglethree {
            syntax: "<angle>";
            initial-value: 180deg;
            inherits: false;
        }

        @property --anglefour {
            syntax: "<angle>";
            initial-value: 270deg;
            inherits: false;
        }

        .bg--1 {
            animation: 10s spinun cubic-bezier(0.42, 0, 0.58, 1) infinite;
            background-image: linear-gradient(
                var(--angleone),
                #ff8100 0%,
                #ffffff 100%
            );
        }

        .bg--2 {
            animation: 6s spin2 cubic-bezier(0.42, 0, 0.58, 1) infinite;
            background-image: linear-gradient(
                var(--angletwo),
                #5900ff 0%,
                #ffffff 100%
            );
        }

        .bg--3 {
            animation: 8s spin3 cubic-bezier(0.42, 0, 0.58, 1) infinite;
            background-image: linear-gradient(
                var(--anglethree),
                #ff6e00 0%,
                #ffffff 100%
            );
        }

        .bg--4 {
            animation: 12s spin4 cubic-bezier(0.42, 0, 0.58, 1) infinite;
            background-image: linear-gradient(
                var(--anglefour),
                #007fff 0%,
                #ffffff 100%
            );
        }

        @keyframes spin {
            0% {
                --angle: 0deg;
            }
            25% {
                --angle: 90deg;
            }
            50% {
                --angle: 220deg;
            }
            75% {
                --angle: 300deg;
            }
            100% {
                --angle: 360deg;
            }
        }

        @keyframes spinun {
            0% {
                --angleone: 90deg;
            }
            25% {
                --angleone: 160deg;
            }
            50% {
                --angleone: 220deg;
            }
            75% {
                --angleone: 320deg;
            }
            100% {
                --angleone: 360deg;
            }
        }

        @keyframes spin2 {
            0% {
                --angletwo: 0deg;
            }
            25% {
                --angletwo: 95deg;
            }
            50% {
                --angletwo: 140deg;
            }
            75% {
                --angletwo: 240deg;
            }
            100% {
                --angletwo: 360deg;
            }
        }

        @keyframes spin3 {
            0% {
                --anglethree: 0deg;
            }
            25% {
                --anglethree: 60deg;
            }
            50% {
                --anglethree: 135deg;
            }
            75% {
                --anglethree: 210deg;
            }
            100% {
                --anglethree: 360deg;
            }
        }

        @keyframes spin4 {
            0% {
                --anglefour: 0deg;
            }
            25% {
                --anglefour: 120deg;
            }
            50% {
                --anglefour: 220deg;
            }
            75% {
                --anglefour: 275deg;
            }
            100% {
                --anglefour: 360deg;
            }
        }

        .search-card-wrapper {
            position: relative;
            width: 50%;
            display: block;
        }

        .search-card {
            display: block;
            position: relative;
            padding: 40px;
            z-index: 10;
            border-radius: 14px;
            background-color: #fff;
            outline: 1px solid #d2d2d2;
            box-shadow: 0px 0px 51px -23px #00000099;
        }

        h1 {
            font-size: 4rem;
            margin: 0;
        }

        .subtitle {
            margin-top: 1.2rem;
            font-size: 2rem;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        textarea {
            width: 100%;
            height: 100px;
            appearance: none;
            border: 1px solid rgb(204, 196, 210);
            padding: 2rem;
            font-size: 1.6rem;
            resize: none;
        }

        textarea:focus {
            outline: none;
            border: 1px solid #6c6be3;
        }

        button {
            font-size: 1.5rem;
            font-weight: 300;
            padding: 1.4rem;
            border: none;
            background-color: #6c6be3;
            color: #fff;
            cursor: pointer;
            width: 100%;
            text-transform: uppercase;
            margin-top: 1.4rem;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover, button:focus {
            background-color: #3f3dac;
        }

        .result-container {
            width: 50%;
            margin-top: 5rem;
            padding: 4rem;
            background-color: #fff;
            border-radius: 1.4rem;
            z-index: 1;
            outline: 1px solid #d2d2d2;
            box-shadow: 0px 0px 51px -23px #00000099;
        }

        .result-container p {
            font-size: 1.6rem;
            font-weight: 300;
            line-height: 1.8;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<main>
    <div class="bg bg--1"></div>
    <div class="bg bg--2"></div>
    <div class="bg bg--3"></div>
    <div class="bg bg--4"></div>
    <div class="search-card-wrapper">
        <div class="search-card">
            <h1>{{ $agent->name }}</h1>
            <p class="subtitle">What do you want to know ?</p>
            <form>
                <textarea
                    name=""
                    placeholder="{{ $agent->chatOptions['searchPlaceholder'] ?? 'How can I delete this page ?'}}"
                    id=""
                ></textarea>

                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="result-container hidden">
        <p class="result">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit possimus
            deleniti error necessitatibus vitae nam rerum sed aliquam itaque
            laudantium praesentium, a voluptates illo culpa est. Ad maxime nostrum
            inventore?
        </p>
    </div>

    <script type="module">
        const form = document.querySelector("form");
        const resultContainer = document.querySelector(
            ".result-container",
        );
        const result = document.querySelector(".result");

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            resultContainer.classList.add("hidden");
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

                resultContainer.classList.remove("hidden");
                form.querySelector('button[type="submit"]').classList.remove("hidden");
            });
        });
    </script>
</main>
</body>
</html>
