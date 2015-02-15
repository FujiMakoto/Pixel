@extends('app')

{{-- Color Scheme --}}
@section('color-scheme'){{ $image->getColorScheme() }}@stop

@section('styling')
    @include('images/_partials/accentuation')
@stop

{{-- Page Content --}}
@section('content')
    <div class="container image-show-container">
        {{-- Image Preview --}}
        <div class="preview-container">
            <div class="image-preview">
                {{-- Preview Image --}}
                {!! HTML::image($image->getUrl($image::PREVIEW), $image->name, ['id' => 'preview']) !!}
                <div id="image-toolbar">
                    @include('images/_partials/toolbar');
                </div>
            </div>
        </div>

        {{-- Image Details --}}
        <div id="image-details">
            @include('images/_partials/details')
        </div>
    </div>
@stop

{{-- Handle Delete Requests --}}
@if ( $deleteKey = Request::get('deleteKey') )
    @section('scripts')
        <script>
            $(window).load(function() {
                pixel.image.deleteResource("{{ route('images.destroy', ['sid' => $image->sid]) }}", "{{ $deleteKey }}");
            });
        </script>
    @stop
@endif

@section('scripts')
    <script>
        // Center the users viewport on the preview image
        $(window).load(function() {
            $(pixel.config["imagePreview"]).centerOn();
        });
    </script>
@stop