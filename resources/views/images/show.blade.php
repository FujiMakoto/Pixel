@extends('app')

{{-- Color Scheme --}}
@section('color-scheme'){{ $image->getColorScheme() }}@stop

@section('styling')
    @include('images/_partials/accentuation')
@stop

{{-- Header Text --}}
@section('header-text'){{ $image->name }}@endsection
@section('header-subtext'){{ $image->md5sum }}@endsection

{{-- Page Content --}}
@section('content')
    <div class="container image-show-container">
        {{-- Image Preview --}}
        <div class="preview-container">
            <div class="image-preview">
                {{-- Preview Image --}}
                {!! HTML::image($image->getUrl($image::PREVIEW), $image->name, ['id' => 'preview']) !!}

                {{-- Toolbars --}}
                <div id="toolbar-container">
                    @include('images/_partials/toolbars')
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
            pixel.select["imagePreview"].centerOn();
        });
    </script>
@stop