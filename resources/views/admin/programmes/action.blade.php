<div class="btn-group blocks">
    <!-- Activate & Deactivate -->
    @if ($programme->deleted_at)
        <button type="submit" class="btn btn-warning activateQA" form="form_restore_{{$programme->id}}"><i class="fas fa-recycle"></i> <b>Restore</b></button>

        <form id="form_restore_{{$programme->id}}" method="POST" action="{{ route('admin.programmes.restore', $programme->id) }}">
            @method('PUT')
            @csrf
        </form>

        <button type="button" id="forceDelete" class="btn btn-danger forceDeleteQA" delete_id="{{$programme->id}}"><i class="fas fa-trash"></i> <b>Destory</b></button>

        <form id="form_forceDelete_{{$programme->id}}" method="POST" action="{{ route('admin.programmes.forceDelete', $programme->id) }}">
            @method('PUT')
            @csrf
        </form>
    @else
        <!-- Approve & Reject -->
        @if ($programme->state_id == 1 || $programme->state_id == 3)
            @if($programme->schedule == null)
            <button type="button" class="btn btn-dark approveQA" onclick="form_approve('{{$programme->id}}' , null)"><i class="fas fa-check-circle"></i> Approve</button>
            @else
            <button type="button" class="btn btn-warning approveQA text-white" onclick="form_approve('{{$programme->id}}', '{{date('d F Y H:i', strtotime($programme->schedule))}}')"><i class="fas fa-clock"></i><b> Scheduled</b></button>
            @endif
            <form id="form_approve_{{$programme->id}}" method="POST" action="{{ route('admin.programmes.approve', $programme->id) }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="scheduleType" id="scheduleType_{{$programme->id}}">
                <input type="hidden" name="schedule" id="schedule_{{$programme->id}}">
            </form>
        @endif

        @if ($programme->state_id == 2)
            <button type="button" class="btn btn-success rejectQA"><i class="fa fa-cloud" aria-hidden="true"></i> Uploaded</button>
        @endif
        <!-- Edit -->
        <a class='btn btn-primary white_font editQA' href="{{ route('admin.programmes.edit', $programme->id) }}"><i class="fas fa-edit"></i> Edit</a>

        <button id="deactivate" class="btn btn-danger deactivateQA" delete_id="{{$programme->id}}"><i class="fas fa-times"></i> <b>Delete</b></button>

        <form id="form_destroy_{{$programme->id}}" method="POST" action="{{ route('admin.programmes.destroy', $programme->id) }}">
            @method('DELETE')
            @csrf
        </form>
    @endif
</div>