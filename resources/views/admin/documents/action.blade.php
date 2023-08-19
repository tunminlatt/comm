<div class="btn-group blocks">

    @if(\Storage::exists('documents/'. $document->id .'/file'))
        <a href="#" onclick="downloadFile('{{$file}}' , '{{$document->title}}.{{$extension}}')" class="btn btn-secondary"><i class="fas fa-download" style="font-size:23px;"></i> Download</button></a>
    @endif

    <!-- Show -->
    <a class='btn btn-primary white_font detailQA' href="{{ route('admin.documents.show', $document->id) }}"><i class="fas fa-list-alt"></i> Detail</a>

    <!-- Activate & Deactivate -->
    @if ($document->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$document->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$document->id}}" method="POST" action="{{ route('admin.documents.restore', $document->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$document->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$document->id}}" method="POST" action="{{ route('admin.documents.forceDelete', $document->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$document->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

        <form id="form_destroy_{{$document->id}}" method="POST" action="{{ route('admin.documents.destroy', $document->id) }}">
            @method('DELETE')
            @csrf
        </form>
    @endif
</div>