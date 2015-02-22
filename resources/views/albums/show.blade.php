@extends('app')

@section('header-text'){{ $album->name }}@endsection
@section('header-subtext')
    @unless(empty($album->description)){{ $album->description }}
    @else{{ $album->images()->count() }} images
    @endunless
@endsection

{{-- Color Scheme --}}
@section('color-scheme'){{ $album->getColorScheme() }}@stop

@section('content')
    <div class="container album-show-container">
        <div id="links" class="row">
            @foreach($album->images() as $image)
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="{{ route('images.download', ['sidFile' => $image->getSidFilename()]) }}" title="{{ $image->name }}" data-gallery="{{ $album->sid }}">
                        <img class="img-responsive" src="{{ route('images.download', ['sidFile' => $image->getSidFilename(), 'size' => 'thumbnail']) }}" alt="{{ $image->name }}">
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-use-bootstrap-modal="false">
        <!-- The container for the modal slides -->
        <div class="slides"></div>
        <!-- Controls for the borderless lightbox -->
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
        <!-- The modal dialog, which will be used to wrap the lightbox content -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body next"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left prev">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary next">
                            Next
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection