<div class="btn-group blocks">

    <!-- Activate & Deactivate -->
    @if ($volunteer->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$volunteer->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$volunteer->id}}" method="POST" action="{{ route('admin.volunteers.restore', $volunteer->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$volunteer->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$volunteer->id}}" method="POST" action="{{ route('admin.volunteers.forceDelete', $volunteer->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <!-- Edit -->
        <a class='btn btn-primary white_font editQA' href="{{ route('admin.volunteers.edit', $volunteer->id) }}"><i class="fas fa-edit"></i> Edit</a>

        <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$volunteer->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

        <form id="form_destroy_{{$volunteer->id}}" method="POST" action="{{ route('admin.volunteers.destroy', $volunteer->id) }}">
            @method('DELETE')
            @csrf
        </form>

    @endif
</div>
