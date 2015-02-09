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
                        @if ($canEdit)
                            <button type="button" class="btn btn-default dropdown-toggle" id="image-options" data-toggle="dropdown">
                                <i class="fa fa-cog fw"></i>
                            </button>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="image-options">
                                <li role="presentation">
                                    <a href="#" role="menuitem" tabindex="-1"><i class="fa fa-trash-o fa-fw"></i> Delete</a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
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