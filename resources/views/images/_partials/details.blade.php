{{-- Share Links --}}
<div class="col-md-8 col-sm-12">
    <div class="input-group margin-bottom-sm">
        <span class="input-group-addon"><i class="fa fa-external-link fa-fw"></i></span>
        <input class="form-control accented select-on-focus copy-on-dblclick" readonly type="text"
               value="{{ route('images.shortShow', ['sid' => $image->sid]) }}">
    </div>

    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-image fa-fw"></i></span>
        <input class="form-control accented select-on-focus copy-on-dblclick" readonly type="text"
               value="{{ route('images.shortDownload', ['sidFile' => $image->getSidFilename()]) }}">
    </div>

    @if ($image->canEdit())
        <hr>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-trash-o fa-fw"></i></span>
            <input class="form-control accented select-on-focus reveal-on-focus copy-on-dblclick" readonly type="text"
                   value="Click to reveal the image deletion link"
                   data-reveal-text="{{ route('images.show', ['sid' => $image->sid, 'deleteKey' => $image->delete_key]) }}">
        </div>
    @endif
</div>

{{-- Image Details --}}
<div class="col-md-4 col-sm-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Image info</h3>
        </div>
        <ul class="list-group">
            <li class="list-group-item">
                <span class="badge">{{ $image->created_at->diffForHumans() }}</span>
                Uploaded
            </li>

            <li class="list-group-item">
                <span class="badge">{{ $image->views }}</span>
                Views
            </li>

            <li class="list-group-item">
                <span class="badge">@if($image->user) {{ $image->user["name"] }} @else Guest @endif</span>
                Created by
            </li>

            <li class="list-group-item">
                <span class="badge">{{ $image->width }}x{{ $image->height }}</span>
                Dimensions
            </li>

            <li class="list-group-item">
                <span class="badge">{{ $image->getFileSize() }}</span>
                Filesize
            </li>
        </ul>
    </div>
</div>