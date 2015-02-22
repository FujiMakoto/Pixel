<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pixel</title>

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
				{!! link_to_route('home', 'Pixel', [], ['class' => 'navbar-brand']) !!}
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li>{!! link_to_route('images.create', 'Image') !!}</li>
							<li>{!! link_to_route('albums.create', 'Album') !!}</li>
						</ul>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li><a href="{{ route('users.auth.login') }}">Login</a></li>
						<li><a href="{{ route('users.auth.register') }}">Register</a></li>
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								{{ Auth::user()->name }} <span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="{{ route('users.auth.logout') }}">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>

		{{-- Header --}}
		<div id="header-container">
			{{-- Primary --}}
			<div class="header @yield('color-scheme', 'purple')">
				<div class="container">
					<div class="row">
						<div style="margin-top: 35px;" class="col-md-12">
							<h4 id="header-text">@yield('header-text', config('app.name'))</h4>
						</div>

						<div class="col-md-12 subtext">
							<span id="header-subtext">@yield('header-subtext')</span>
						</div>
					</div>
				</div>
			</div>

			{{-- Secondary --}}
			<div class="header secondary fade">
				<div class="container">
					<div class="row">
						<div style="margin-top: 35px;" class="col-md-12">
							<h4 id="secondary-header-text">@yield('header-text', config('app.name'))</h4>
						</div>

						<div class="col-md-12 subtext">
							<span id="secondary-header-subtext">@yield('header-subtext')</span>
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
			<p class="footer-text text-center">
                Pixel is an open source image hosting application, built using the
                {!! link_to('http://laravel.com', 'Laravel', ['target' => '_blank']) !!} PHP framework. Code licensed under
                {!! link_to('https://github.com/FujiMakoto/Pixel/blob/master/LICENSE', 'MIT', ['target' => '_blank']) !!} License.
            </p>
		</div>
	</footer>

	{{-- Scripts --}}
	<!--<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
	<!--<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>-->

	<script>
		/**
		 * Application configuration
		 */
		var pixel_config = {

			csrf_token:  "{{ csrf_token() }}",
			upload_path: "@yield('upload_path', Request::url())",
			home_path:   "{{ route('home') }}",
			base_path:   "{{ url('') }}",
			max_size:    "{{ config('image.upload.max_size') }}"

		}
	</script>
	<script src="{{ elixir("js/all.js") }}"></script>

	@yield('scripts')

</body>
</html>
