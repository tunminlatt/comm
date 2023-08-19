<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ProgrammeRepository;
use App\Repositories\AudioRepository;
use App\Repositories\StationRepository;
use App\Http\Requests\Admin\StoreProgrammeRequest;
use App\Http\Requests\Admin\UpdateProgrammeRequest;
use App\Http\Resources\Admin\ProgrammeDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Image;
use DB;
use Auth;
use Storage;
use Illuminate\Support\Facades\Session;

class ProgrammeController extends Controller
{
    public function __construct(
        ProgrammeRepository $programmeRepository,
        StationRepository $stationRepository,
        AudioRepository $audioRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->stationRepository = $stationRepository;
        $this->audioRepository = $audioRepository;
        $this->datatables = $datatables;
        $this->image = $image;
    }

    protected function supportLargeFileUpload() {
        // prepare variables
        $size = '2000M'; // 2GB
        $time = -1; // unlimited min

        // change setting
        ini_set('upload_max_filesize', $size);
        ini_set('post_max_size', $size);
        ini_set('max_input_time', $time);
        ini_set('max_execution_time', $time);
    }

    protected function packData($request) {
        return $request->only('title', 'station_id', 'duration','description', 'type');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if($request->trash == 'all'){
                $programmes = $this->programmeRepository->getAllForTable($request, ['station', 'state'], false, true);
            }else{
                $programmes = $this->programmeRepository->getAllForTable($request, ['station', 'state'], false);
            }
            $programmeDatatableResource = ProgrammeDatatableResource::collection($programmes);

            return $this->datatables->of($programmeDatatableResource)->addIndexColumn()->toJson();
        }

        // prepare variables
        $userTypeID = Auth::user()->user_type_id;
        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];


        $stations = ($userTypeID == 1) ? $stations->filter(function ($value, $key) {
            return count($value->audios) > 0;
        }): [];

        return view('admin.programmes.index', ['userTypeID' => $userTypeID, 'stations' => $stations]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // prepare variables
        $user = Auth::user();
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;
        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];

        $audioID = '';
        $audio = $banners = $recordings = [];
        if ($request->has('audio_id')) {
            $audioID = $request->audio_id;
            $audio = $this->audioRepository->show('id', $audioID, [], true);
            $banners = $this->image->get('audios/'. $audioID .'/banner');
            $recordings = $this->image->get('audios/'. $audioID .'/recording');
        }

        return view('admin.programmes.create', ['userStationID' => $userStationID, 'userTypeID' => $userTypeID, 'stations' => $stations, 'audioID' => $audioID, 'audio' => $audio, 'banners' => $banners, 'recordings' => $recordings]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProgrammeRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $user = Auth::user();
            $userID = $user->id;
            $userTypeID = $user->user_type_id;
            // $audioID = $request->audio_id;

            $stateID = 1; //pending
            $approvedBy = null;
            if ($userTypeID == 1) {
                $stateID = 1; //accepted ?? Depend Change on User
                $approvedBy = $userID;
            }

            $payload = $this->packData($request);
            $payload['uploaded_by'] = $userID;
            $payload['state_id'] = $stateID;
            $payload['approved_by'] = $approvedBy;
            $payload['duration'] = '00:00';

            if($request->type === 'video' || $request->type === 'audio') {
                # Disable getting duration by ffprobe
                # $payload['duration'] = $this->image->getDuration($request->content[0]);
            }

            // create programme
            $this->supportLargeFileUpload();
            $programme = $this->programmeRepository->create($payload);
            $programmeID = $programme->id;

            // create images
            if($request->type === 'photo') {
                $this->image->add('programmes/'. $programmeID .'/photo', $request->content);
            }
            //create video
            if($request->type === 'video') {
                $output = $this->image->add('programmes/'. $programmeID .'/video', $request->content);
                $this->image->generateThumbnail($output, $programmeID);
            }
            //create recording
            if($request->type === 'audio') {
                $this->image->add('programmes/'. $programmeID .'/audio', $request->content);
            }
            //create file
            if($request->type === 'file') {
                $this->image->add('programmes/'. $programmeID .'/file', $request->content);
            }
            //create thumbnail if needed
            if($request->thumbNail != null) {
                $this->image->add('programmes/'. $programmeID .'/thumbnail', [$request->thumbNail]);
            }

            DB::commit();
            session()->flash('success', 'Programme Created Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.programmes.index'),
            ]);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Creating Failed!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // prepare variables
        $user = Auth::user();
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;
        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];
        $programme = $this->programmeRepository->show('id', $id, [], true);
        $contents = $this->image->get('programmes/'. $id . '/' . $programme->type);
        $thumbNail = $this->image->get('programmes/'. $id .'/thumbnail');

        return view('admin.programmes.edit',
            [
                'userStationID' => $userStationID,
                'userTypeID' => $userTypeID,
                'stations' => $stations,
                'programme' => $programme,
                'contents' => $contents,
                'thumbNail' => $thumbNail
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProgrammeRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $this->supportLargeFileUpload();
            $payload = $this->packData($request);

            // update programme
            $this->programmeRepository->update($id, $payload, true);

            // create images
            if($request->content && $request->new_type === 'photo') {
                $this->deleteOldContents($id);
                $this->programmeRepository->update($id, ['type' => 'photo'], true);
                $this->image->add('programmes/'. $id .'/photo', $request->content);
            }
            //create video
            if($request->content && $request->new_type === 'video') {
                $this->deleteOldContents($id);
                $this->programmeRepository->update($id, ['type' => 'video'], true);
                $this->image->add('programmes/'. $id .'/video', $request->content);
                $this->image->generateThumbnail($request->content, $id);
            }
            //create recording
            if($request->content && $request->new_type === 'audio') {
                $this->deleteOldContents($id);
                $this->programmeRepository->update($id, ['type' => 'audio'], true);
                $this->image->add('programmes/'. $id .'/audio', $request->content);
            }
            //create file
            if($request->content && $request->new_type === 'file') {
                $this->deleteOldContents($id);
                $this->programmeRepository->update($id, ['type' => 'file'], true);
                $this->image->add('programmes/'. $id .'/file', $request->content);
            }

            //create thumbnail if needed
            if($request->thumbNail != null) {
                $this->image->delete('programmes/'. $id .'/thumbnail');
                $this->image->add('programmes/'. $id .'/thumbnail', [$request->thumbNail]);
            }

            DB::commit();
            session()->flash('success', 'Programme Updated Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.programmes.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Updating Failed!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $this->programmeRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.programmes.index')->with('success', 'Programme Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->programmeRepository->restore($id);
            $payload['state_id'] = 1;
            $payload['schedule'] = null;
            $this->programmeRepository->update($id, $payload, true);

            DB::commit();

            return redirect()->route('admin.programmes.index')->with('success', 'Programme Restore Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Activating Failed!');
        }
    }

    public function approve(Request $request,$id)
    {
        $today = \Carbon\Carbon::now('Asia/Rangoon')->format('Y-m-d H:i');
        $schedule = \Carbon\Carbon::parse($request->schedule, 'Asia/Rangoon')->format('Y-m-d H:i');
        $scheduleType = (int)$request->scheduleType;

        DB::beginTransaction();

        try {
            if($today == $schedule || $today > $schedule){
                $payload = ['schedule' => \Carbon\Carbon::parse($request->schedule, 'Asia/Rangoon'), 'state_id' => 2];
            }else{
                $payload = ['schedule' => \Carbon\Carbon::parse($request->schedule, 'Asia/Rangoon'), 'state_id' => 1];
            }

            $this->programmeRepository->update($id, $payload, true);
            DB::commit();

            if($today == $schedule){
                $payload = ['state_id' => 2];
                return redirect()->route('admin.programmes.index')->with('success', 'Programme Approved Successfully!');
            }else{
                if($scheduleType == 1){
                    return redirect()->route('admin.programmes.index')->with('success', 'Programme Approved By Schedule Updated Successfully!');
                }else{
                    return redirect()->route('admin.programmes.index')->with('success', 'Programme Approved By Schedule Successfully!');
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Approving Failed!');
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();

        try {
            $payload = ['state_id' => 3];
            $this->programmeRepository->update($id, $payload, true);
            DB::commit();

            return redirect()->route('admin.programmes.index')->with('success', 'Programme Rejected Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index')->with('fail', 'Programme Rejecting Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('programmes/'. $id .'/banner');
            $this->image->delete('programmes/'. $id .'/recording');

            $this->programmeRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.programmes.index')->with('success', 'Programme destory successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.programmes.index', ['trash' => 'all'])->with('fail', 'Programme destory failed!');
        }
    }

    public function deleteSelected(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->ids;
            if ($request->trash == 'all') {
                $deleteLable = 'destory';
                foreach ($ids as $id) {
                    $this->image->delete('programmes/'. $id .'/banner');
                    $this->image->delete('programmes/'. $id .'/recording');
                    $this->programmeRepository->destroy($id, 2);
                }
            } else {
                $deleteLable = 'delete';
                foreach ($ids as $id) {
                    $this->programmeRepository->destroy($id);
                }
            }
            DB::commit();
            Session::flash('success', 'Selected programme(s) ' .$deleteLable. ' successfully!');

            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('fail', 'Selected programme(s) ' .$deleteLable. ' failed!');
            return 'fail';
        }
    }

    protected function deleteOldContents($id)
    {
        $this->image->delete('programmes/'. $id .'/photo');
        $this->image->delete('programmes/'. $id .'/video');
        $this->image->delete('programmes/'. $id .'/audio');
        $this->image->delete('programmes/'. $id .'/file');
    }
}