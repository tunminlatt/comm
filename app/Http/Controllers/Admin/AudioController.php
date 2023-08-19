<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use App\Helpers\Image;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Repositories\AudioRepository;
use App\Repositories\StationRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Repositories\VolunteerRepository;
use App\Http\Requests\Admin\StoreAudioRequest;
use App\Http\Resources\Admin\AudioDatatableResource;

class AudioController extends Controller
{
    public function __construct(
        AudioRepository $audioRepository,
        VolunteerRepository $volunteerRepository,
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->audioRepository = $audioRepository;
        $this->volunteerRepository = $volunteerRepository;
        $this->stationRepository = $stationRepository;
        $this->datatables = $datatables;
        $this->image = $image;
    }

    protected function supportLargeFileUpload()
    {
        // prepare variables
        $size = '2000M'; // 2GB
        $time = -1; // unlimited min

        // change setting
        ini_set('upload_max_filesize', $size);
        ini_set('post_max_size', $size);
        ini_set('max_input_time', $time);
        ini_set('max_execution_time', $time);
    }

    protected function packData($request)
    {
        return $request->only('title', 'station_id', 'duration', 'note', 'uploaded_by');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->trash == 'all') {
                $audios = $this->audioRepository->getAllForTable($request, ['volunteer', 'station'], false, true);
            } else {
                $audios = $this->audioRepository->getAllForTable($request, ['volunteer', 'station'], false);
            }
            $audioDatatableResource = AudioDatatableResource::collection($audios);

            return $this->datatables->of($audioDatatableResource)->addIndexColumn()->toJson();
        }

        // prepare variables
        $userTypeID = Auth::user()->user_type_id;
        $volunteers = $this->volunteerRepository->all([], false);
        $volunteers = $volunteers->filter(function ($value, $key) {
            return count($value->audios) > 0;
        });

        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];
        $stations = ($userTypeID == 1) ? $stations->filter(function ($value, $key) {
            return count($value->audios) > 0;
        }) : [];

        return view('admin.audios.index', ['userTypeID' => $userTypeID, 'volunteers' => $volunteers, 'stations' => $stations]);
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
        $volunteers = $this->volunteerRepository->all([], false);
        $volunteers = $volunteers->filter(function ($value, $key) {
            return $value->deleted_at == null;
        });

        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];
        $stations = ($userTypeID == 1) ? $stations->filter(function ($value, $key) {
            return $value->deleted_at == null;
        }) : [];

        return view('admin.audios.create', ['userStationID' => $userStationID, 'userTypeID' => $userTypeID, 'stations' => $stations, 'volunteers' => $volunteers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAudioRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            // create programme
            $this->supportLargeFileUpload();
            $audio = $this->audioRepository->create($payload);

            $audioID = $audio->id;

            // create banner
            $this->image->add('audios/'. $audioID .'/banner', [$request->banner]);

            // create recording
            $this->image->add('audios/'. $audioID .'/recording', [$request->recording]);

            DB::commit();
            return redirect()->route('admin.audios.index')->with('success', 'Audio Created Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.audios.index')->with('fail', 'Audio Creating Failed!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $audio = $this->audioRepository->show('id', $id, ['station', 'volunteer'], true);
        //dd($audio);
        $banners = $this->image->get('audios/'. $id .'/banner');
        $recordings = $this->image->get('audios/'. $id .'/recording');
        return view('admin.audios.show', ['audio' => $audio, 'banners' => $banners, 'recordings' => $recordings]);
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
            $this->audioRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.audios.index')->with('success', 'Audio Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.audios.index')->with('fail', 'Audio Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->audioRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.audios.index')->with('success', 'Audio Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.audios.index')->with('fail', 'Audio Activating Failed!');
        }
    }

    public function share($id)
    {
        DB::beginTransaction();

        try {
            $this->audioRepository->share($id);
            DB::commit();

            return redirect()->back()->with('success', "Audio Shared Successfully! <a href='/shares'> Check Share List</a>");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('fail', "Audio Sharing Failed!");
        }
    }

    public function private($id)
    {
        DB::beginTransaction();

        try {
            $this->audioRepository->private($id);
            DB::commit();

            return redirect()->back()->with('success', "Audio Private Successfully! <a href='/audios'> Check Audio List</a>");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('fail', 'Audio Private Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('audios/'. $id .'/banner');
            $this->image->delete('audios/'. $id .'/recording');
            $this->audioRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.audios.index')->with('success', 'Audio destory successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.audios.index', ['trash' => 'all'])->with('fail', 'Audio destory failed!');
        }
    }

    public function imageDownload($id)
    {
        $audio = $this->audioRepository->show('id', $id, ['station', 'volunteer'], true);
        $assetPath = $this->image->getByApi('audios/'. $id .'/banner');
        $filename = $audio->title.'.jpeg';
        if ($assetPath != []) {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header("Content-Type: application/octet-stream");
            return readfile($assetPath);
        }
    }

    public function audioDownload($id)
    {
        $audio = $this->audioRepository->show('id', $id, ['station', 'volunteer'], true);
        $assetPath = $this->image->getByApi('audios/'. $id .'/recording');
        $filename = $audio->title.'.mp3';
        if ($assetPath != []) {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
            return readfile($assetPath);
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
                    $this->image->delete('audios/'. $id .'/banner');
                    $this->image->delete('audios/'. $id .'/recording');
                    $this->audioRepository->destroy($id, 2);
                }
            } else {
                $deleteLable = 'delete';
                foreach ($ids as $id) {
                    $this->audioRepository->destroy($id);
                }
            }
            DB::commit();
            Session::flash('success', 'Selected audio(s) ' .$deleteLable. ' successfully!');

            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('fail', 'Selected audio(s) ' .$deleteLable. ' failed!');
            return 'fail';
        }
    }
}
