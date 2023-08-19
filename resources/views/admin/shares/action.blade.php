<div class="btn-group blocks">
      <!-- Download -->
      <div class="dropdown">
        <button role="button" type="button" class="btn btn-secondary dropdown" id="dropdownMenu{{$audio->id}}" data-toggle="dropdown">
            <i class="fas fa-download" style="font-size:23px;"></i> Download
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenu{{$audio->id}}" style="margin-top:0px;">
            @if(\Storage::exists('audios/'. $audio->id .'/recording'))
                <a href="{{route('admin.audios.audio', $audio->id)}}" target="_blank" download><button class="dropdown-item" type="button">Audio</button></a>
            @endif
            @if(\Storage::exists('audios/'. $audio->id .'/banner'))
                <a href="{{route('admin.audios.image', $audio->id)}}" target="_blank" download><button class="dropdown-item" type="button">Image</button></a>
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
    <a class='btn btn-primary white_font detailQA' href="{{ route('admin.shares.show', $audio->id) }}"><i class="fas fa-list-alt"></i> Detail</a>
    <!-- Private -->
    @if (auth()->user()->user_type_id == 1 && !$audio->deleted_at && $audio->status == 'public')
    <button id="private" class='btn btn-default privateQA' audio_id="{{ $audio->id }}"><i class="fa fa-share-alt" id="icon-container" style="font-size:23px;"><i class="fa fa-ban" id="icon-nested"></i> </i> <span style="padding-left:10px;">Close Share</span></button>
    <form id="form_private_{{$audio->id}}" method="POST" action="{{ route('admin.audios.private', $audio->id) }}">
        @method('POST')
        @csrf
    </form>
    @endif
</div>