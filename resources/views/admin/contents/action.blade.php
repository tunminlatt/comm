<div class="btn-group blocks">
    <!-- Download -->
    <a class='btn btn-primary white_font detailQA' href="{{ route('admin.contents.file', $content->id) }}"><i class="fas fa-download"></i> Download</a>

    <!-- Show -->
    <a class='btn btn-primary white_font detailQA' href="{{ route('admin.contents.show', $content->id) }}"><i class="fas fa-list-alt"></i> Detail</a>

    <!-- Share -->
    @if (!$content->deleted_at && $content->status == 'private')
        <button id="sharing" class='btn btn-info shareQA' audio_id="{{ $content->id }}"><i class="fas fa-share-alt" style="font-size:23px;"></i> Share</button>
        <form id="form_sharing_{{$content->id}}" method="POST" action="{{ route('admin.contents.share', $content->id) }}">
            @method('POST')
            @csrf
        </form>
    @endif

<!-- Private -->
    @if (!$content->deleted_at && $content->status == 'public')
        <button id="private" class='btn btn-default privateQA' audio_id="{{ $content->id }}"><i class="fa fa-share-alt" id="icon-container" style="font-size:23px;"><i class="fa fa-ban" id="icon-nested"></i> </i> <span style="padding-left:10px;">Close Share</span></button>
        <form id="form_private_{{$content->id}}" method="POST" action="{{ route('admin.contents.private', $content->id) }}">
            @method('POST')
            @csrf
        </form>
    @endif

<!-- Submit -->
<!-- <a class='btn btn-success white_font' href="{{ route('admin.programmes.create', ['audio_id' => $content->id]) }}"><i class="fas fa-upload"></i>  Submit</a> -->

    <!-- Activate & Deactivate -->
    @if ($content->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$content->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$content->id}}" method="POST" action="{{ route('admin.contents.restore', $content->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$content->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$content->id}}" method="POST" action="{{ route('admin.contents.forceDelete', $content->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        @if ( $content->station->id == config('app.share_station') && Auth::user()->user_type_id == 1)
            <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$content->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

            <form id="form_destroy_{{$content->id}}" method="POST" action="{{ route('admin.contents.destroy', $content->id) }}">
                @method('DELETE')
                @csrf
            </form>
        @elseif ( $content->station->id != config('app.share_station'))
            <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$content->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

            <form id="form_destroy_{{$content->id}}" method="POST" action="{{ route('admin.contents.destroy', $content->id) }}">
                @method('DELETE')
                @csrf
            </form>
        @endif
    @endif
</div>
