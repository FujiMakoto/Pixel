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

                {{-- Image Toolbar --}}
                <div class="btn-toolbar pull-right fade" role="toolbar">
                    <div class="btn-group">
                        {{-- Download Image --}}
                        <a href="{{ $image->getUrl(null, ['download' => 1]) }}" class="btn btn-default download" download="{{ $image->name }}">
                            <i class="fa fa-download fw"></i>
                        </a>

                        {{-- Image Options --}}
                        @if ( $image->canEdit() )
                            <button type="button" class="btn btn-default dropdown-toggle" id="image-options" data-toggle="dropdown">
                                <i class="fa fa-cog fw"></i>
                            </button>

                            <ul class="dropdown-menu image-options" role="menu" aria-labelledby="image-options">
                                <li role="presentation">
                                    <a href="#" class="delete" role="menuitem" tabindex="-1" data-delete-key="{{ $image->delete_key }}" data-delete-url="{{ route('images.destroy', ['sid' => $image->sid]) }}">
                                        <i class="fa fa-trash-o fa-fw"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
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