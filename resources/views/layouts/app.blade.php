
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Tabungan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body>
    @include('layouts.header')

    <div style="display: flex; min-height: 100vh;">
        @include('layouts.sidebar')

        <main style="flex: 1; padding: 30px;">
            @yield('content')
        </main>
    </div>

    @include('layouts.footer')
</body>
</html>
