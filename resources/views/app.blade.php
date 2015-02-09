<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>

	<link href="{{ elixir("css/all.css") }}" rel="stylesheet">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	@yield('styling')

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			{{-- Navigation --}}
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Laravel</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="/">Home</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="/auth/login">Login</a></li>
						<li><a href="/auth/register">Register</a></li>
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/auth/logout">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
			{{-- /Navigation --}}
		</div>

		{{-- Header --}}
		<div id="header-container">
			{{-- Primary --}}
			<div class="header @yield('color-scheme', 'purple')">
				<div class="container">
					<div class="row">
						<div style="margin-top: 35px;" class="col-md-12">
							<h4>Single Image Uploader</h4>
						</div>

						<div class="col-md-12 subtext">
							<span>Want to create an album instead?</span>
							<a href="sign-up.html">Click here</a>
						</div>
					</div>
				</div>
			</div>

			{{-- Secondary --}}
			<div class="header secondary fade">
				<div class="container">
					<div class="row">
						<div style="margin-top: 35px;" class="col-md-12">
							<h4>Single Image Uploader</h4>
						</div>

						<div class="col-md-12 subtext">
							<span>Want to create an album instead?</span>
							<a href="sign-up.html">Click here</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>

	@yield('content')

	{{-- Footer --}}
	<footer class="footer footer-inverse">
		<div class="container-fluid">
			<p class="footer-text">Place sticky footer content here.</p>
		</div>
	</footer>

	{{-- Scripts --}}
	<!--<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
	<!--<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>-->

	<script>
		var csrf_token  = "{{ csrf_token() }}";
		var upload_path = "@yield('upload_path', Request::url())";
		var home_path   = "{{ route('home') }}";
		var base_path   = "{{ url('') }}";
	</script>

	<script src="{{ elixir("js/all.js") }}"></script>

	@yield('scripts')

</body>
</html>
