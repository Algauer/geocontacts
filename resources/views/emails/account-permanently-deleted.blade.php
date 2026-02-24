@extends('emails.layouts.base')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:24px;line-height:30px;color:#0f172a;">
        Conta excluida permanentemente
    </h1>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Ola, <strong>{{ $name }}</strong>.
    </p>

    <p style="margin:0 0 14px 0;font-size:15px;line-height:24px;color:#334155;">
        Sua conta <strong>{{ $email }}</strong> e todos os seus dados foram removidos
        permanentemente da nossa base de dados.
    </p>

    <p style="margin:0;font-size:13px;line-height:20px;color:#64748b;">
        Caso deseje utilizar a plataforma novamente, sera necessario criar uma nova conta.
    </p>
@endsection
