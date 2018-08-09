<head>
	<meta charset="UTF-8">
	<meta name="keywords" content="Unimed Fama, Webvendas Unimed Fama, plano de saúde, online">
	<meta name="theme-color" content="#dfdfdf">
	<meta name="robots" content="follow, index">
	<meta name="author" content="Lucas Rodrigues Brelaz">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="js/html5shiv.min.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/css/estilo.css">
	<link rel="stylesheet" type="text/css" href="/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="/css/progress-bar.css">
	<link rel="stylesheet" type="text/css" href="/css/bootstrap-datepicker3.standalone.min.css">
	@if(\Session::get('mobile'))
		<link rel="stylesheet" type="text/css" href="/css/app-mobile.css">
	@endif
	<!-- FAVICON -->
	<link rel="icon" href="img/favicon_unimed.png">
	<title>{{ $titulo }}</title>
</head>
@if(\Session::get('mobile'))

<style media="screen">
	body{
		padding-top: 100px !important;
	}
</style>

@endif
