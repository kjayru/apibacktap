<x-mail-layout title="Congratulations">
    <div style="text-align:center;">
        <div style="font-size:15px;font-weight:bold;color:#6b7280;">Hello {{ $name }}</div>
        <div style="margin:14px 0 10px;">
            <span style="display:inline-block;width:38px;height:38px;line-height:38px;border:2px solid #1e88e5;border-radius:50%;color:#1e88e5;font-size:20px;">&#9733;</span>
        </div>
        <div style="font-size:26px;font-weight:bold;color:#041245;text-transform:uppercase;">Congratulations!</div>
        <div style="font-size:15px;font-weight:bold;color:#1e88e5;margin-top:4px;">You have passed the course.</div>
    </div>

    <div style="border-top:2px solid #1e88e5;margin:22px 0 0;"></div>

    @include('emails.partials.course-card', ['course' => $course, 'note' => 'COURSE COMPLETED'])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#041245;">
        <tr>
            <td align="center" style="padding:28px 20px;">
                <div style="color:#ffffff;font-size:22px;font-weight:bold;text-transform:uppercase;">Your certificate is ready.</div>
                <div style="color:#ffffff;font-size:12px;margin-top:6px;">We appreciate using our courses for your benefit.</div>
                <a href="{{ $url }}" style="display:inline-block;margin-top:18px;background:#e01f26;color:#ffffff;font-size:13px;font-weight:bold;padding:12px 18px;text-decoration:none;">
                    Download certificate
                </a>
            </td>
        </tr>
    </table>
</x-mail-layout>
