@php
use Carbon\Carbon;
use Illuminate\Support\Str;
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
            height:1850px;
            background:url('{{env('APP_URL')}}{{$certificado}}') no-repeat center center;
            background-size: cover;
        }
        .contenido{
            position: relative;
        }

        .certi_curso{
            font-size: 2rem;
            font-weight: bold;
            padding-top: 7rem;
            display: block;
            text-align: center;
        }
        .certi_nombre{
            font-size: 1.2rem;
            font-weight: bold;
            top: 15rem;
            display: block;
            left:1.5rem;
            position: absolute;
        }
        .certi_nombre2{
            font-size: 1.2rem;
            font-weight: bold;
            top: 15rem;
            display: block;
            left:14.5rem;
            position: absolute;
        }
        .certi_nombre3{
            font-size: 1.2rem;
            font-weight: bold;
            top: 15rem;
            display: block;
            left:28.5rem;
            position: absolute;
        }

        .certi_licence{
            font-size: 1.2rem;
            font-weight: bold;
            top: 17.2rem;
            display: block;
            left:27.5rem;
            position: absolute;
        }
        .date_complete{
            font-size: 1rem;
            font-weight: bold;
            top: 31rem;
            display: block;
            text-align: left;
            left: 16rem;
            position: absolute;
        }

        .bussines_name{
            position: absolute;
            font-size: 1rem;
            font-weight: bold;
            top: 26.5rem;
            display: block;
            text-align: left;
            left: 10rem;
        }
        .bussines_licencia{
            position: absolute;

            font-size: 1rem;
            font-weight: bold;
            top: 26.5rem;
            display: block;
            text-align: left;
            right: 7.5rem;
        }
        .instructor_licencia{
            position: absolute;
            font-size: 1rem;
            font-weight: bold;
            top: 23.95rem;
            display: block;
            text-align: left;
            right: 2.5rem;
        }

        .bussines_instuctor{
            position: absolute;

            font-size: 1rem;
            font-weight: bold;
            top: 28rem;
            display: block;
            text-align: left;
            left: 16rem;
        }
        .bussines_representate{
            position: absolute;
            font-size: 1rem;
            font-weight: bold;
            top: 29.5rem;
            display: block;
            text-align: left;
            left: 16rem;
        }

        .bussines_online{
            position: absolute;
            font-size: 1rem;
            font-weight: bold;
            top:32.4rem;
            display: block;
            text-align: left;
            left: 18.6rem;
        }
        .bussines_signature{
            height: 60px;
            top: 34.27rem;
            position: absolute;
            left: 18rem;
        }
        .bussines_signature img{
            width: 100%;
            max-width: 100%;
            height: 60px;

        }
        .bussines_signature2{
            height: 60px;
            top: 36.84rem;
            position: absolute;
            left: 18rem;
        }
        .bussines_signature2 img{
            width: 100%;
            max-width: 100%;
            height: 60px;

        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="contenido">

        <div class="certi_nombre">
            {{ucfirst(@$user->profile->lastname)}}
        </div>
        <div class="certi_nombre2">
            {{ucfirst(@$user->profile->firstname)}}
        </div>
        <div class="certi_nombre3">
            {{ucfirst(Str::substr(@$user->profile->middlename,0,1))}}
        </div>
        <div class="certi_licence">
            {{substr(@$user->profile->social_number, -4)}}
        </div>
</div>
    <div class="person">
        <div class="date_complete">
            {{$user_course->updated_at->format('Y-m-d')}}
        </div>
        <div class="bussines_name">
            TAP Security Academy
        </div>
        <div class="bussines_licencia">
            F11634201
        </div>



        <div class="bussines_instuctor">
            Oscar Gonzalez
        </div>
        <div class="bussines_representate">
            Oscar Gonzalez
        </div>
        <div class="bussines_online">
            X
        </div>
        <div class="bussines_signature">
            <img src="{{env('APP_URL')}}/images/firma2.png" style="width:100%">
        </div>
        <div class="bussines_signature2">
            <img src="{{env('APP_URL')}}/images/firma2.png" style="width:100%">
        </div>
    </div>



</body>
</html>
