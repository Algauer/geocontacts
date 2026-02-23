@extends('emails.layouts.base')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:24px;line-height:30px;color:#0f172a;">
        Conta excluida temporariamente
    </h1>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Ola, <strong>{{ $name }}</strong>.
    </p>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Sua conta <strong>{{ $email }}</strong> foi excluida e entrou em periodo de graca.
        Voce pode restaurar tudo (incluindo seus contatos) por ate <strong>7 dias</strong>.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px 0;">
        <tr>
            <td>
                <a href="{{ $restoreUrl }}"
                   style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 18px;border-radius:10px;">
                    Restaurar minha conta
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;line-height:20px;color:#64748b;">
        Se a exclusao foi intencional, nenhuma acao adicional e necessaria.
    </p>
@endsection
