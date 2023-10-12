<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="flex gap-8 min-h-screen justify-center items-center">
            <button onclick="importUsers(this)" class="py-2 px-3 rounded-lg bg-indigo-600 outline-0 font-medium text-white hover:bg-indigo-500">Импортировать пользователей</button>
            <p>Всего <span id="users_count" class="font-semibold">{{ $usersCount }}</span></p>
            <p>Добавлено <span id="added_users" class="font-semibold">0</span></p>
            <p>Обновлено <span id="updated_count" class="font-semibold">0</span></p>
        </div>
        <script>
           async function importUsers(button) {
               button.classList.add('opacity-25')
               button.disabled = true
                await fetch('/import/users', {
                    method: 'POST',
                    headers: {
                        "X-CSRF-Token": document.querySelector('meta[name=csrf-token]').getAttribute('content')
                    },
                }).then(response => {
                    return response.json();
                }).then(data => {
                    if(data.error) document.body.innerHTML += data.error
                    document.getElementById('users_count').innerText = data.count
                    document.getElementById('added_users').innerText = data.added_users
                    document.getElementById('updated_count').innerText = data.updated_count
                    button.classList.remove('opacity-25')
                    button.disabled = false
                }).catch(error => {
                    console.log(error)
                    button.classList.remove('opacity-25')
                    button.disabled = false
                });
            }
        </script>
    </body>
</html>
