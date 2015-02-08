@extends('app')

@section('content')
    <div class="container image-show-container">
        {{-- Image Preview --}}
        <div class="preview-container">
            <div class="image-preview">
                {!! HTML::image(route('images.download', [
                    'size' => 'preview',
                    'sidFile' => $image->getSidFilename($image::PREVIEW)]),
                    $image->name,
                    ['id' => 'preview']) !!}
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