{{-- Ficha del curso dentro del correo: imagen, título y quién lo imparte. --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eceef3;">
    <tr>
        <td style="padding:20px;">
            @isset($heading)
                <div style="font-size:15px;font-weight:bold;color:#041245;margin-bottom:14px;">{{ $heading }}</div>
            @endisset

            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    @if ($course->banner)
                        <td valign="top" style="padding-right:16px;">
                            <img src="{{ url('/storage/' . ltrim($course->banner, '/')) }}" alt="{{ $course->titulo }}" width="110" style="display:block;border:0;width:110px;height:auto;">
                        </td>
                    @endif
                    <td valign="top">
                        <div style="font-size:16px;font-weight:bold;color:#041245;text-transform:uppercase;">{{ $course->titulo }}</div>
                        @if ($course->responsable)
                            <div style="font-size:12px;font-weight:bold;color:#041245;margin-top:2px;">A course with {{ $course->responsable }}</div>
                        @endif
                        @isset($note)
                            <div style="font-size:14px;font-weight:bold;color:#041245;margin-top:12px;">{{ $note }}</div>
                        @endisset
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
