@extends('app')

@section('header-text') Upload images to album @endsection
@section('header-subtext') Drag and drop the images you wish to upload to this album below @endsection

@section('content')
    <div class="container upload-container">
        <div id="album-upload-container">
            <form action="{{ route('images.store') }}" class="dropzone" id="album-upload-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="album_id" value="{{ $album->id }}">
            </form>
        </div>
        <div class="form-group">
            <div class="text-center">
                <a href="{{ route('albums.show', ['sid' => $album->sid]) }}" class="btn btn-primary continue" style="margin-right: 15px;">
                    Continue to album overview
                </a>
            </div>
        </div>
    </div>
@stop

@section('upload_path'){{ link_to_route('images.store') }}@stop