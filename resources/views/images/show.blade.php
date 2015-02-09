@extends('app')

{{-- Color Scheme --}}
@section('color-scheme'){{ $image->getColorScheme() }}@stop

@section('content')
    <div class="container image-show-container">
        {{-- Image Preview --}}
        <div class="preview-container">
            <div class="image-preview">
                {{-- Preview Image --}}
                {!! HTML::image($image->getUrl($image::PREVIEW), $image->name, ['id' => 'preview']) !!}
            </div>
        </div>

        {{-- Image Details --}}
        @include('images/_partials/details')
    </div>
@stop

{{-- Handle Delete Requests --}}
@if ( $deleteKey = Request::get('deleteKey') )
    @section('scripts')
        <script>
            $(window).load(function() {
                deleteResource({
                    deleteUrl: "{{ route('images.destroy', ['sid' => $image->sid]) }}",
                    deleteKey: "{{ $deleteKey }}"
                })
            });
        </script>
    @stop
@endif