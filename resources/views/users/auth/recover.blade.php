@extends('app')

@section('header-text') Account recovery @endsection
@section('header-subtext') Fill in your accounts registered e-mail address below @endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Reset Password</div>
				<div class="panel-body">
					@if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif

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

					<form class="form-horizontal" role="form" method="POST" action="{{ route('users.auth.doRecover') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

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
                            <div class="col-md-push-4 col-md-6">
                                {!! Recaptcha::render() !!}
                            </div>
                        </div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Send Password Reset Link
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
