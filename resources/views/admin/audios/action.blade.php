<div class="btn-group blocks">
    <!-- Download -->
    <div class="dropdown">
        <button role="button" type="button" class="btn btn-secondary dropdown" id="dropdownMenu{{$audio->id}}" data-toggle="dropdown">
            <i class="fas fa-download" style="font-size:23px;"></i> Download
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu{{$audio->id}}" style="margin-top:0px;">
            @if(\Storage::exists('audios/'. $audio->id .'/recording'))
                <a href="#" onclick="downloadFile('{{$recording}}', '{{$audio->title}}.mp3')"><button class="dropdown-item" type="button">Audio</button></a>
            @endif
            @if(\Storage::exists('audios/'. $audio->id .'/banner'))
                <a href="#" onclick="downloadFile('{{$image}}', '{{$audio->title}}.jpeg')"><button class="dropdown-item" type="button">Image</button></a>
            @endif
            <input type="hidden" id="note-val{{$audio->id}}" value="{{ $audio->note }}">
            <input type="hidden" id="title-val{{$audio->id}}" value="{{ $audio->title }}">
            @if(isset($audio->note))
                @if($audio->note !== " ")
                <a href="JavaScript:void(0);" onclick="onClickNote('{{$audio->id}}')" id="dwn-note"><button class="dropdown-item" type="button">Note</button></a>
                @endif
            @endif
        </div>
    </div>

    <!-- Show -->
    <a class='btn btn-primary white_font detailQA' href="{{ route('admin.audios.show', $audio->id) }}"><i class="fas fa-list-alt"></i> Detail</a>

    <!-- Share -->
    @if (!$audio->deleted_at && $audio->status == 'private')
    <button id="sharing" class='btn btn-info shareQA' audio_id="{{ $audio->id }}"><i class="fas fa-share-alt" style="font-size:23px;"></i> Share</button>
    <form id="form_sharing_{{$audio->id}}" method="POST" action="{{ route('admin.audios.share', $audio->id) }}">
        @method('POST')
        @csrf
    </form>
    @endif

    <!-- Private -->
    @if (!$audio->deleted_at && $audio->status == 'public')
    <button id="private" class='btn btn-default privateQA' audio_id="{{ $audio->id }}"><i class="fa fa-share-alt" id="icon-container" style="font-size:23px;"><i class="fa fa-ban" id="icon-nested"></i> </i> <span style="padding-left:10px;">Close Share</span></button>
    <form id="form_private_{{$audio->id}}" method="POST" action="{{ route('admin.audios.private', $audio->id) }}">
        @method('POST')
        @csrf
    </form>
    @endif

    <!-- Submit -->
    <!-- <a class='btn btn-success white_font' href="{{ route('admin.programmes.create', ['audio_id' => $audio->id]) }}"><i class="fas fa-upload"></i>  Submit</a> -->

    <!-- Activate & Deactivate -->
    @if ($audio->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$audio->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$audio->id}}" method="POST" action="{{ route('admin.audios.restore', $audio->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$audio->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$audio->id}}" method="POST" action="{{ route('admin.audios.forceDelete', $audio->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$audio->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

        <form id="form_destroy_{{$audio->id}}" method="POST" action="{{ route('admin.audios.destroy', $audio->id) }}">
            @method('DELETE')
            @csrf
        </form>
    @endif
</div>