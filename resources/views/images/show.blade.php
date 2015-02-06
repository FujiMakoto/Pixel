@extends('app')

@section('content')
    <div class="container image-show-container">
        {{-- Image Preview --}}
        <div class="preview-container">
            <div class="image-preview">
                {!! HTML::image(route('images.download', ['size' => 'preview', 'sidFile' => $image->sid.'.'.$image->type]), $image->name, ['id' => 'preview']) !!}
            </div>
        </div>

        {{-- Image Details --}}
        @include('images/_partials/details')
    </div>
@stop