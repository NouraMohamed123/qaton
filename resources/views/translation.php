<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body style="font-family: 'Cairo', sans-serif;">
    @foreach ($translation as $key=> $value)
    <p>{{$key}} : {{$value}}</p>
    @endforeach
</body>
</html>
