<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{!! $subject !!}</title>
</head>
<body>
    <p>
       greating.. 
    </p>
    <h1>{!! $user->name !!}</h1>
    <p>{!! $body !!}</p>
    @if (!empty($url))
        <a href="{{$url}}"> click here to verify </a>
    @elseif(!empty($otp))
        <h4>{{$otp}}</h4>
    @else
    @endif
    <p>thanks</p>
</body>
</html>