<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>
<body>
    <aside class="sidebar">
        @yield('sidebar')
    </aside>

    <main class="content">
        @yield('content')
    </main>
    
</body>
</html>