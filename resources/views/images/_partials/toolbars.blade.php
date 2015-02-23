{{-- Image Toolbar --}}
<div id="image-toolbar">
    <div class="btn-toolbar pull-right fade" role="toolbar">
        <div class="btn-group">
            {{-- Download Image --}}
            <a href="{{ $image->getUrl(null, ['download' => 1]) }}" class="btn btn-default download" download="{{ $image->name }}">
                <i class="fa fa-download fw"></i>
            </a>

            {{-- Image Options --}}
            @if ( $image->canEdit() )
                <button type="button" class="btn btn-default dropdown-toggle" id="image-options" data-toggle="dropdown">
                    <i class="fa fa-pencil fw"></i>
                </button>

                <ul class="dropdown-menu image-options" role="menu" aria-labelledby="image-options">
                    <li role="presentation">
                        <a href="#" class="crop" role="menuitem" tabindex="-1" data-true-width="{{ $image->width }}" data-true-height="{{ $image->height }}">
                            <i class="fa fa-crop fa-fw"></i> Crop
                        </a>
                    </li>
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

{{-- Cropping Toolbar --}}
<div id="crop-toolbar" class="hide">
    <button type="button" class="btn cancel">
        <span class="fa fa-times"></span> Cancel
    </button>

    <button type="button" class="btn btn-success submit" data-crop-url="{{ route('images.crop', ['sid' => $image->sid]) }}">
        <span class="fa fa-check"></span> Crop image
    </button>
</div>