@extends('emails.layouts.base')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:24px;line-height:30px;color:#0f172a;">
        Bem-vindo, {{ $user->name }}!
    </h1>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Sua conta foi criada com sucesso no <strong>GeoContacts</strong>.
    </p>

    <p style="margin:0 0 22px 0;font-size:15px;line-height:24px;color:#334155;">
        Agora voce pode cadastrar contatos, buscar enderecos e visualizar tudo no mapa de forma organizada.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px 0;">
        <tr>
            <td>
                <a href="{{ config('app.frontend_url') }}"
                   style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 18px;border-radius:10px;">
                    Acessar plataforma
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;line-height:20px;color:#64748b;">
        Se voce nao criou essa conta, ignore este e-mail.
    </p>
@endsection
