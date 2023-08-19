<div class="btn-group blocks">
    <!-- Activate & Deactivate -->
    @if ($station->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$station->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$station->id}}" method="POST" action="{{ route('admin.stations.restore', $station->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$station->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$station->id}}" method="POST" action="{{ route('admin.stations.forceDelete', $station->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <!-- Edit -->
        <a class='btn btn-primary white_font editQA' href="{{ route('admin.stations.edit', $station->id) }}"><i class="fas fa-edit"></i> Edit</a>

        <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$station->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

        <form id="form_destroy_{{$station->id}}" method="POST" action="{{ route('admin.stations.destroy', $station->id) }}">
            @method('DELETE')
            @csrf
        </form>
    @endif
</div>
