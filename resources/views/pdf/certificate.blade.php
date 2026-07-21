<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: sans-serif;
            color: #010b40;
        }
        .frame {
            border: 12px solid #031059;
            padding: 30px;
            box-sizing: border-box;
            text-align: center;
        }
        .eyebrow {
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #e01f26;
            font-size: 14px;
            margin-top: 40px;
        }
        h1 {
            font-size: 42px;
            margin: 10px 0 30px;
        }
        .certify {
            font-size: 14px;
            color: #444;
        }
        .name {
            font-size: 32px;
            margin: 20px 0;
            border-bottom: 2px solid #031059;
            display: inline-block;
            padding: 0 30px 8px;
        }
        .course {
            font-size: 20px;
            margin: 20px 0 40px;
            color: #031059;
        }
        .meta {
            margin-top: 60px;
            font-size: 12px;
            color: #666;
        }
        .cert-image {
            max-height: 90px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="eyebrow">Certificate of Completion</div>
        <h1>TAP Security Academy</h1>
        <p class="certify">This certifies that</p>
        <div class="name">{{ $userName }}</div>
        <p class="certify">has successfully completed the course</p>
        <div class="course">{{ $courseTitle }}</div>

        @if ($certificationImage)
            <img class="cert-image" src="{{ $certificationImage }}">
        @endif

        <div class="meta">
            Issued on {{ $issuedAt }} &bull; Certificate ID: {{ $certificateId }}
        </div>
    </div>
</body>
</html>
