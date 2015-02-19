@extends('app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Account Activation</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('users.auth.doActivate') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <div class="col-md-push-4 col-md-6">
                                    Please check your e-mail for activation instructions
                                </div>
                            </div>

                            <div class="form-group @if($errors->has('email')) has-error @endif">
                                <label class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                        <input type="email" class="form-control" name="email" value="{{ \Session::get('activation_email') }}">
                                    </div>
                                    @if($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Activation Code</label>
                                <div class="col-md-6 @if($errors->has('code')) has-error @endif">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                        <input type="text" class="form-control" name="code">
                                    </div>
                                    @if($errors->has('code')) <p class="help-block has-error">{{ $errors->first('code') }}</p> @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                        Complete Registration
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
