<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site_title }} - 2FA Doğrulama</title>
    @if($site_favicon)
    <link rel="shortcut icon" type="image/png" href="{{ asset('storage/' . $site_favicon) }}" />
    @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/branding/sys-panel-favicon.svg') }}" />
    @endif
    <link href={{asset("css/style.css")}} rel="stylesheet">
    <style>
        .login-page {
            min-height: 100vh;
            background:
                linear-gradient(rgba(8, 28, 36, 0.62), rgba(8, 28, 36, 0.62)),
                url('{{ asset('images/branding/login-bg.svg') }}') center center / cover no-repeat fixed;
        }

        .login-page .authincation-content {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(17, 81, 96, 0.16);
        }
    </style>
</head>

<body class="vh-100 login-page">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="{{ route('dashboard') }}">
                                            <img src="{{ $site_logo ? asset('storage/' . $site_logo) : asset('images/branding/sys-panel-logo.svg') }}" alt="{{ $site_title }} logo" width="220">
                                        </a>
                                    </div>

                                    <h4 class="text-center mb-2">2FA DOĞRULAMA</h4>
                                    <p class="text-center text-muted mb-4">Authenticator uygulamanızdaki kodu girin veya kurtarma kodu kullanın.</p>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form action="{{ route('two-factor.verify') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>6 Haneli Kod</strong></label>
                                            <input type="text" name="code" class="form-control" placeholder="000000" inputmode="numeric" autocomplete="one-time-code">
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Kurtarma Kodu</strong></label>
                                            <input type="text" name="recovery_code" class="form-control" placeholder="ABCDE-FGHIJ">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">DOĞRULA</button>
                                        </div>
                                    </form>

                                    <form action="{{ route('logout') }}" method="POST" class="mt-3 text-center">
                                        @csrf
                                        <button type="submit" class="btn btn-link">Farklı hesapla giriş yap</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src={{asset("vendor/global/global.min.js")}}></script>
    <script src={{asset("js/custom.js")}}></script>
    <script src={{asset("js/dlabnav-init.js")}}></script>
</body>
</html>
