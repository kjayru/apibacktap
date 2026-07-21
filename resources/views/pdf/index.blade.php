@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body{
            margin:0;
            padding:0;
        }
        .contenedor{

    width:100%;
    height:1000px;
    background:url('{{env('APP_URL')}}/images/Certificado-generico-02.png') no-repeat center center;
    background-size: contain;
}

        .certi_curso{
            font-size: 2rem;
            font-weight: bold;
            padding-top: 7rem;
            display: block;
            text-align: center;
        }
        .certi_nombre{
            font-size: 2rem;
            font-weight: bold;
            padding-top: 3.5rem;
            display: block;
            text-align: center;
        }
        .date_complete{
            font-size: 1.5rem;
            font-weight: bold;
            padding-top: 11.5rem;
            display: block;
            text-align: left;
            padding-left: 20rem;
        }

    </style>
</head>
<body>

<div class="contenedor">
    <div class="contenido">
        <div class="certi_curso">
            {{@$user_course->course->titulo}}
        </div>
        <div class="certi_nombre">
            {{@$user->profile->firstname}} {{@$user->profile->lastname}}  {{substr(@$user->profile->social_number, -6)}}
        </div>
        <div class="date_complete">
            {{$user_course->updated_at->format('Y-m-d')}}
        </div>
    </div>
</div>


</body>
</html>
