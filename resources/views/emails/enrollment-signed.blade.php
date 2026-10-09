<x-mail-layout title="Sign Enroll">
    <div style="text-align:center;">
        <div style="font-size:15px;font-weight:bold;color:#6b7280;">Hello {{ $name }}</div>
        <div style="font-size:26px;font-weight:bold;color:#041245;text-transform:uppercase;margin-top:6px;">
            Document has been signed
        </div>
    </div>

    <div style="border-top:2px solid #041245;margin:22px 0 0;"></div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eceef3;">
        <tr>
            <td style="padding:20px;">
                <div style="font-size:15px;font-weight:bold;color:#041245;">Document name: Enrollment agreement</div>
                <div style="font-size:12px;color:#6b7280;margin-top:10px;">
                    Document ID: ({{ $documentId }})<br>
                    From TAP Security Services ({{ config('mail.contact') }})
                </div>

                <div style="border-top:1px solid #d1d5db;margin:14px 0;"></div>

                <div style="font-size:13px;color:#111827;">
                    Hi {{ $legalName }},<br><br>
                    <strong>All signees have signed this document.</strong><br>
                    Audit Trail Serial: {{ $documentId }}
                </div>

                <div style="border-top:1px solid #d1d5db;margin:14px 0;"></div>

                <a href="{{ $url }}" style="display:inline-block;background:#e01f26;color:#ffffff;font-size:13px;font-weight:bold;padding:12px 18px;text-decoration:none;">
                    View Signed Document
                </a>
            </td>
        </tr>
    </table>
</x-mail-layout>
