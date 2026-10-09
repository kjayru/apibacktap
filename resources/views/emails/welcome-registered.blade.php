<x-mail-layout title="Welcome TAP Security">
    <div style="text-align:center;">
        <div style="font-size:15px;font-weight:bold;color:#6b7280;">Hello {{ $name }}</div>
        <div style="margin:16px 0 12px;">
            <span style="display:inline-block;width:44px;height:44px;line-height:44px;background:#16a34a;border-radius:50%;color:#ffffff;font-size:22px;">&#10004;</span>
        </div>
        <div style="font-size:24px;font-weight:bold;color:#041245;text-transform:uppercase;">Your registration was successful</div>
        <div style="font-size:14px;color:#374151;margin-top:8px;">With your account, you can access the courses you buy.</div>
    </div>

    <div style="border-top:2px solid #041245;margin:24px 0 0;"></div>

    @if ($courses->isNotEmpty())
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eceef3;">
            <tr>
                <td style="padding:20px;">
                    <div style="font-size:15px;font-weight:bold;color:#041245;margin-bottom:14px;">Newest courses</div>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            @foreach ($courses as $course)
                                <td valign="top" width="33%" style="padding-right:10px;">
                                    @if ($course->banner)
                                        <img src="{{ url('/storage/' . ltrim($course->banner, '/')) }}" alt="{{ $course->titulo }}" width="150" style="display:block;border:0;width:100%;max-width:150px;height:auto;">
                                    @endif
                                    <div style="font-size:12px;font-weight:bold;color:#041245;margin-top:8px;">{{ $course->titulo }}</div>
                                    @if ($course->resumen)
                                        <div style="font-size:11px;color:#4b5563;margin-top:4px;line-height:1.5;">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($course->resumen), 90) }}
                                        </div>
                                    @endif
                                    <a href="{{ rtrim(config('app.frontend_url'), '/') }}/courses/{{ $course->slug }}" style="display:inline-block;margin-top:10px;background:#e01f26;color:#ffffff;font-size:11px;font-weight:bold;padding:8px 12px;text-decoration:none;">
                                        Go to course
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif
</x-mail-layout>
