@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="{{ route('users.auth.doLogin') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

                        @if ($message = \Session::pull('message'))
                            <div class="form-group">
                                <div class="col-md-push-4 col-md-6">
                                    <p class="text-success">{{ $message }}</p>
                                </div>
                            </div>
                        @endif

                        @if ( config('services.github.client_id') )
                            <div class="form-group">
                                <div class="col-md-push-4 col-md-6">
                                    <a href="{{ route('users.auth.oauth', ['driver' => 'github']) }}" class="btn btn-block btn-social btn-github">
                                        <i class="fa fa-github"></i> Sign in with GitHub
                                    </a>
                                </div>
                            </div>
                        @endif

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input type="password" class="form-control" name="password">
                                </div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Login
								</button>

								<a href="{{ route('users.auth.recover') }}">Forgot Your Password?</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
