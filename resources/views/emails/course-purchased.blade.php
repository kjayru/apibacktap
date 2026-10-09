<x-mail-layout title="Congratulations">
    <div style="text-align:center;">
        <div style="font-size:15px;font-weight:bold;color:#6b7280;">Hello {{ $name }}</div>
        <div style="font-size:26px;font-weight:bold;color:#041245;text-transform:uppercase;margin-top:6px;">Congratulations!</div>
        <div style="font-size:15px;font-weight:bold;color:#1e88e5;margin-top:4px;">Payment Confirmed</div>
    </div>

    <div style="border-top:2px solid #041245;margin:22px 0 0;"></div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eceef3;">
        <tr>
            <td style="padding:20px;">
                <div style="font-size:15px;font-weight:bold;color:#041245;margin-bottom:14px;">You have purchased the course</div>

                <table role="presentation" cellpadding="0" cellspacing="0">
                    <tr>
                        @if ($course->banner)
                            <td valign="top" style="padding-right:16px;">
                                <img src="{{ url('/storage/' . ltrim($course->banner, '/')) }}" alt="{{ $course->titulo }}" width="130" style="display:block;border:0;width:130px;height:auto;">
                            </td>
                        @endif
                        <td valign="top">
                            <div style="font-size:16px;font-weight:bold;color:#041245;text-transform:uppercase;">{{ $course->titulo }}</div>
                            @if ($course->responsable)
                                <div style="font-size:12px;font-weight:bold;color:#041245;margin-top:2px;">A course with {{ $course->responsable }}</div>
                            @endif

                            <div style="font-size:12px;color:#374151;margin-top:12px;line-height:1.9;">
                                @if ($course->disponible)
                                    Available from {{ $course->disponible }}<br>
                                @endif
                                {{ $chapters }} Chapters<br>
                                @if ($course->audio)
                                    Audio: {{ $course->audio }}<br>
                                @endif
                                @if ($course->nivel)
                                    Level: {{ strtoupper($course->nivel) }}<br>
                                @endif
                                @if ($course->tiempovalido)
                                    Access: {{ $course->tiempovalido }} days to finish the course
                                @endif
                            </div>

                            <div style="font-size:24px;font-weight:bold;color:#041245;margin-top:14px;">
                                ${{ number_format((float) $amount, 0) }}
                                <span style="font-size:12px;">USD</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="text-align:center;padding:22px 0 6px;">
        <a href="{{ $url }}" style="display:inline-block;background:#e01f26;color:#ffffff;font-size:13px;font-weight:bold;padding:13px 22px;text-decoration:none;">
            Start course
        </a>
    </div>
</x-mail-layout>
