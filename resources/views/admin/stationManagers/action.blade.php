<div class="btn-group blocks">
    <!-- Activate & Deactivate -->
    @if ($user->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$user->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$user->id}}" method="POST" action="{{ route('admin.stationManagers.restore', $user->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$user->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$user->id}}" method="POST" action="{{ route('admin.stationManagers.forceDelete', $user->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <!-- Edit -->
        <a class='btn btn-primary white_font editQA' href="{{ route('admin.stationManagers.edit', $user->id) }}"><i class="fas fa-edit"></i> Edit</a>

        @if ($user->id != 1)
            <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$user->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

            <form id="form_destroy_{{$user->id}}" method="POST" action="{{ route('admin.stationManagers.destroy', $user->id) }}">
                @method('DELETE')
                @csrf
            </form>
        @endif
    @endif
</div>