{{-- Plantilla común de los correos del alumno, con la estructura que mandó el cliente
     (#1831): cabecera azul con el logo, el contenido, la franja roja "Learn without
     limits" y el pie. Con tablas y estilos en línea, que es lo único que entienden
     todos los clientes de correo. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TAP Security' }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f2f4;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f2f4;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;background:#ffffff;">
                    <tr>
                        <td align="center" style="background:#041245;padding:24px;">
                            <img src="{{ url('/images/Logo-TAP.png') }}" alt="TAP Security" width="80" style="display:block;border:0;width:80px;height:auto;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 28px 8px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 28px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#b7161c;">
                                <tr>
                                    <td align="center" style="padding:28px 16px;">
                                        <img src="{{ url('/images/Emblema-blanco.png') }}" alt="" width="40" style="display:block;margin:0 auto 10px;border:0;width:40px;height:auto;">
                                        <div style="color:#ffffff;font-size:26px;font-weight:bold;line-height:1.1;letter-spacing:.5px;text-transform:uppercase;">
                                            Learn<br>Without<br>Limits
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:0 28px 28px;font-size:12px;color:#6b7280;line-height:1.6;">
                            <strong style="color:#111827;">&copy; {{ date('Y') }} TAP SECURITY. All rights reserved.</strong><br>
                            Learn about our
                            <a href="{{ rtrim(config('app.frontend_url'), '/') }}" style="color:#1e4ed8;">Privacy Policies and Terms and Conditions</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
