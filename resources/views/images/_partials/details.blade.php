{{-- Share Links --}}
<div class="col-md-8 col-sm-12">
    <div class="input-group margin-bottom-sm">
        <span class="input-group-addon"><i class="fa fa-external-link fa-fw"></i></span>
        <input class="form-control select-on-focus" readonly type="text" placeholder="Page link" value="{{ route('images.show', ['sid' => $image->sid]) }}">
    </div>

    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-image fa-fw"></i></span>
        <input class="form-control select-on-focus" readonly type="text" placeholder="Image link" value="{{ route('images.shortDownload', ['sidFile' => $image->sid.'.'.$image->type]) }}">
    </div>
    <hr>

    <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-trash-o fa-fw"></i></span>
        <input class="form-control" type="text" placeholder="Delete link">
    </div>
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
                <span class="badge">Guest</span>
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