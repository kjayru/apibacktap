<x-mail-layout title="Course Failed">
    <div style="text-align:center;">
        <div style="font-size:15px;font-weight:bold;color:#6b7280;">Hello {{ $name }}</div>
        <div style="font-size:26px;font-weight:bold;color:#041245;text-transform:uppercase;margin-top:6px;">We're sorry</div>
        <div style="font-size:15px;font-weight:bold;color:#1e88e5;margin-top:6px;line-height:1.4;">
            Unfortunately your test result did not<br>meet the minimum passing grade.
        </div>
        <div style="font-size:12px;font-weight:bold;color:#9ca3af;margin-top:10px;line-height:1.6;">
            You have {{ $retakeDays }} days from the result to retake the course.<br>
            If you exceed {{ $retakeDays }} days, you will have to pay for the course again
        </div>
    </div>

    <div style="border-top:2px solid #041245;margin:22px 0 0;"></div>

    @include('emails.partials.course-card', [
        'course' => $course,
        'heading' => 'The course you took',
        'note' => 'COURSE COMPLETED',
    ])

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#041245;">
        <tr>
            <td align="center" style="padding:28px 20px;">
                <div style="color:#ffffff;font-size:18px;font-weight:bold;text-transform:uppercase;">
                    You have {{ $retakeDays }} days left to take the course again
                </div>
                <a href="{{ $url }}" style="display:inline-block;margin-top:18px;background:#e01f26;color:#ffffff;font-size:13px;font-weight:bold;padding:12px 18px;text-decoration:none;">
                    Start the course again
                </a>
            </td>
        </tr>
    </table>
</x-mail-layout>
