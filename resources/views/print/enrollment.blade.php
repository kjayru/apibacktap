@extends('print.layout', ['title' => 'Enrollment · ' . $user->name])

@section('content')
    <h1>Enrollment agreement</h1>
    <p class="muted">{{ $user->name }} {{ $user->profile?->lastname }} · {{ $user->email }}</p>

    {{-- El mismo documento que muestra el panel, para no tener dos textos distintos. --}}
    @include('filament.user-courses.enrollment', ['sign' => $sign])
@endsection
