@extends('app')

@section('header-text') Create a new album @endsection
@section('header-subtext') Please describe your new album below @endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Create Album</div>
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

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('albums.store') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            @if ($message = \Session::pull('message'))
                                <div class="form-group">
                                    <div class="col-md-push-4 col-md-6">
                                        <p class="text-success">{{ $message }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="col-md-4 control-label">Album Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Album Description</label>
                                <div class="col-md-6">
                                    <textarea cols="3" class="form-control" name="description" value="{{ old('description') }}"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                        Create and continue to upload
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
