@extends('app')

@section('content')
    <div class="container upload-container">
        <div class="preview-container">
            <div class="image-preview">
                {!! HTML::image(route('images.download', ['size' => 'preview', 'sidFile' => $image->sid.'.'.$image->type]), $image->name, ['id' => 'preview']) !!}
            </div>
        </div>
    </div>
@stop