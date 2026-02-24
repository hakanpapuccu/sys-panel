<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:title" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:description" content="Fillow : Fillow Saas Admin  Bootstrap 5 Template" />
	<meta property="og:image" content="https://fillow.dexignlab.com/xhtml/social-image.png" />
	<meta name="format-detection" content="telephone=no">
	
		<!-- PAGE TITLE HERE -->
		<title>{{ $site_title }} - Giriş</title>
		
		<!-- FAVICONS ICON -->
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
										<a href="{{ route('login') }}">
											<img src="{{ $site_logo ? asset('storage/' . $site_logo) : asset('images/branding/sys-panel-logo.svg') }}" alt="{{ $site_title }} logo" width="240">
										</a>
									</div>
                                    <h4 class="text-center mb-4">GİRİŞ YAP</h4>
                                    <form action="{{route('login')}}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>E-Posta</strong></label>
                                            <input id="email" type="email" name="email" class="form-control" placeholder="hello@example.com" required autofocus>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Parola</strong></label>
                                            <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="row d-flex justify-content-between mt-4 mb-2">
                                            <div class="mb-3">
                                               <div class="form-check custom-checkbox ms-1">
														<input type="checkbox" class="form-check-input" id="basic_checkbox_1" name="remember">
														<label class="form-check-label" for="basic_checkbox_1">Beni Hatırla</label>
													</div>
                                            </div>
                                            
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">GİRİŞ YAP</button>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src={{asset("vendor/global/global.min.js")}}></script>
    <script src={{asset("js/custom.js")}}></script>
    <script src={{asset("js/dlabnav-init.js")}}></script>
	<script src={{asset("js/styleSwitcher.js")}}></script>
</body>
</html>
