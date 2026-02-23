@extends('emails.layouts.base')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:24px;line-height:30px;color:#0f172a;">
        Redefinir senha
    </h1>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Recebemos uma solicitacao para redefinir a senha da sua conta.
    </p>

    <p style="margin:0 0 22px 0;font-size:15px;line-height:24px;color:#334155;">
        Para continuar, clique no botao abaixo. Por seguranca, este link expira em pouco tempo.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px 0;">
        <tr>
            <td>
                <a href="{{ $resetUrl }}"
                   style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 18px;border-radius:10px;">
                    Redefinir senha
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;line-height:20px;color:#64748b;">
        Se voce nao solicitou essa alteracao, ignore este e-mail.
    </p>
@endsection
