<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;">
                    <tr>
                        <td style="padding:0 0 12px 0;text-align:center;">
                            <span style="display:inline-block;background:#2563eb;color:#ffffff;border-radius:999px;padding:8px 14px;font-weight:700;font-size:13px;letter-spacing:0.3px;">
                                GeoContacts
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:28px;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 10px 0 10px;text-align:center;color:#64748b;font-size:12px;line-height:18px;">
                            {{ config('app.name') }}<br>
                            Este e-mail foi enviado automaticamente. Nao responda esta mensagem.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
