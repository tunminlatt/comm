<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use App\Helpers\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Repositories\ContentRepository;
use App\Repositories\StationRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreAudioRequest;
use App\Http\Resources\Admin\ContentDatatableResource;
use App\Models\Content;

class ContentController extends Controller
{
    public function __construct(
        ContentRepository $contentRepository,
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->contentRepository = $contentRepository;
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
        return $request->only('title','station_id', 'uploaded_by');
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
                $contents = $this->contentRepository->getAllForTable($request, ['station'], false, true);
            } else {
                $contents = $this->contentRepository->getAllForTable($request, ['station'], false);
            }
            $contentDatatableResource = ContentDatatableResource::collection($contents);

            return $this->datatables->of($contentDatatableResource)->addIndexColumn()->toJson();
        }

        // prepare variables
        $userTypeID = Auth::user()->user_type_id;

        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];
        $stations = ($userTypeID == 1) ? $stations->filter(function ($value, $key) {
            return count($value->audios) > 0;
        }) : [];

        return view('admin.contents.index', ['userTypeID' => $userTypeID, 'stations' => $stations]);
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

            // create video
            $this->image->add('videos/'. $audioID . '/video', [$request->video]);

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
        $content = $this->contentRepository->show('id', $id, ['station'], true);

        $availableContactMethods = config('contact.contact-methods');
        if($content->contact_method) {
            $contactMethod = $availableContactMethods[$content->contact_method]['name'];
        } else {
            $contactMethod = null;
        }
        $file = $this->image->get('contents/'. $id . '/content');
        $meta = $this->image->get('contents/'. $id . '/meta');

        return view('admin.contents.show', ['files' => $file, 'metas' => $meta, 'content' => $content, 'contactMethod' => $contactMethod]);
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
            $this->contentRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.contents.index')->with('success', 'Content Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.contents.index')->with('fail', 'Content Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->contentRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.contents.index')->with('success', 'Content Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.contents.index')->with('fail', 'Content Activating Failed!');
        }
    }

    public function share($id)
    {
        DB::beginTransaction();

        try {
            $this->contentRepository->share($id);
            DB::commit();

            return redirect()->back()->with('success', "Audio Shared Successfully! <a href='/shares'> Check Share List</a>");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('fail', "Content Sharing Failed!");
        }
    }

    public function private($id)
    {
        DB::beginTransaction();

        try {
            $this->contentRepository->private($id);
            DB::commit();

            return redirect()->back()->with('success', "Content Private Successfully! <a href='/audios'> Check Content List</a>");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('fail', 'Audio Private Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('contents/'. $id .'/content');
            $this->image->delete('contents/'. $id .'/meta');
            $this->contentRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.contents.index')->with('success', 'Content destory successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.contents.index', ['trash' => 'all'])->with('fail', 'Content destory failed!');
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
                    $this->image->delete('contents/'. $id .'/content');
                    $this->image->delete('contents/'. $id .'/meta');
                    $this->contentRepository->destroy($id, 2);
                }
            } else {
                $deleteLable = 'delete';
                foreach ($ids as $id) {
                    $this->contentRepository->destroy($id);
                }
            }
            DB::commit();
            Session::flash('success', 'Selected content(s) ' .$deleteLable. ' successfully!');

            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('fail', 'Selected content(s) ' .$deleteLable. ' failed!');
            return 'fail';
        }
    }

    public function contentDownload($id)
    {
        $content = Content::findOrFail($id);
        $encrypted_file = Storage::files('contents/'. $id .'/content')[0];
        $decrypted_file = $encrypted_file;

        // TODO Descryption Routine
        /*
        $rsa = new \Pikirasa\RSA($content->station->public_key, $content->station->private_key);
        $aes_key = base64_decode($rsa->decrypt(Storage::get(Storage::files('contents/'. $id .'/meta')[0])));
        $encrypter = new Encrypter($aes_key, 'AES-256-CBC');
        $decrypted_file = $encrypter->decrypt(Storage::get($encrypted_file));
        */

        $file = '/tmp/' . Str::uuid();
        Storage::put($file, Storage::get($decrypted_file));

        return response()->download(Storage::path($file), $content->title)->deleteFileAfterSend(true);

    }
}
