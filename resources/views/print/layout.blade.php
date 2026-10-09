{{-- Hoja para imprimir: sin el panel alrededor, con márgenes de página y el diálogo de
     impresión abierto al cargar (#1735, #1737, #1740). --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        @page { size: letter; margin: 14mm; }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        .sheet { max-width: 860px; margin: 24px auto; padding: 32px; background: #fff; }
        .toolbar { max-width: 860px; margin: 16px auto 0; text-align: right; }
        .toolbar button {
            padding: 8px 18px;
            border: 0;
            border-radius: 999px;
            background: #e01f26;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        h1 { margin: 0 0 4px; font-size: 22px; }
        h2 { margin: 24px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; font-size: 15px; text-transform: uppercase; letter-spacing: .04em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; text-align: left; vertical-align: top; border-bottom: 1px solid #f3f4f6; }
        th { width: 230px; font-weight: 600; color: #374151; }
        .muted { color: #6b7280; }

        @media print {
            body { background: #fff; }
            .sheet { margin: 0; padding: 0; max-width: none; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="toolbar"><button type="button" onclick="window.print()">Print</button></div>
    <div class="sheet">@yield('content')</div>
</body>
</html>
