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
                    <a href="#" class="delete" role="menuitem" tabindex="-1" data-delete-key="{{ $image->delete_key }}" data-delete-url="{{ route('images.destroy', ['sid' => $image->sid]) }}">
                        <i class="fa fa-trash-o fa-fw"></i> Delete
                    </a>
                </li>
            </ul>
        @endif
    </div>
</div>