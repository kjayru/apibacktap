{{-- Miniatura reproducible de vídeo o audio, como en el listado del admin anterior (#1584).
     El tipo llega en $type ('video' o 'audio'); sin archivo se pinta un guion. --}}
@php
    $path = $getState();
    $url = filled($path) ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    $poster = $type === 'video' && filled($getRecord()->poster)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($getRecord()->poster)
        : null;
@endphp

<div class="px-3 py-2">
    @if (! $url)
        <span class="text-gray-400">-</span>
    @elseif ($type === 'video')
        <video src="{{ $url }}" @if ($poster) poster="{{ $poster }}" @endif
               width="200" height="120" controls preload="metadata"
               class="rounded-lg max-w-[200px]"></video>
    @else
        <audio src="{{ $url }}" controls preload="none" class="max-w-[200px]"></audio>
    @endif
</div>
