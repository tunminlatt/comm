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
use App\Repositories\DocumentRepository;
use App\Repositories\StationRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Repositories\VolunteerRepository;
use App\Http\Requests\Admin\StoreDocumentRequest;
use App\Http\Resources\Admin\DocumentDatatableResource;

class DocumentController extends Controller
{
    public function __construct(
        DocumentRepository $documentRepository,
        VolunteerRepository $volunteerRepository,
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->documentRepository = $documentRepository;
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
        return $request->only('title', 'station_id', 'note', 'uploaded_by');
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
                $documents = $this->documentRepository->getAllForTable($request, ['volunteer', 'station'], false, true);
            } else {
                $documents = $this->documentRepository->getAllForTable($request, ['volunteer', 'station'], false);
            }
            $documentDatatableResource = DocumentDatatableResource::collection($documents);

            return $this->datatables->of($documentDatatableResource)->addIndexColumn()->toJson();
        }

        // prepare variables
        $userTypeID = Auth::user()->user_type_id;
        $volunteers = $this->volunteerRepository->all([], false);
        $volunteers = $volunteers->filter(function ($value, $key) {
            return count($value->documents) > 0;
        });

        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];
        $stations = ($userTypeID == 1) ? $stations->filter(function ($value, $key) {
            return count($value->documents) > 0;
        }) : [];

        return view('admin.documents.index', ['userTypeID' => $userTypeID, 'volunteers' => $volunteers, 'stations' => $stations]);
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

        return view('admin.documents.create', ['userStationID' => $userStationID, 'userTypeID' => $userTypeID, 'stations' => $stations, 'volunteers' => $volunteers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDocumentRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            // create programme
            $this->supportLargeFileUpload();
            $document = $this->documentRepository->create($payload);

            $documentID = $document->id;

            $this->image->add('documents/'. $documentID .'/file', [$request->file]);

            DB::commit();
            return redirect()->route('admin.documents.index')->with('success', 'Document Created Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.documents.index')->with('fail', 'Document Creating Failed!');
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
        $document = $this->documentRepository->show('id', $id, ['station', 'volunteer'], true);
        //dd($document);
        $file = $this->image->getByAPi('documents/'. $id .'/file');
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return view('admin.documents.show', ['document' => $document, 'file' => $file, 'extension' => $extension]);
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
            $this->documentRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.documents.index')->with('success', 'Document Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.documents.index')->with('fail', 'Document Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->documentRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.documents.index')->with('success', 'Document Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.documents.index')->with('fail', 'Document Activating Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('documents/'. $id .'/file');
            $this->documentRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.documents.index')->with('success', 'Document destory successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.documents.index', ['trash' => 'all'])->with('fail', 'Document destory failed!');
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
                    $this->image->delete('documents/'. $id .'/file');
                    $this->documentRepository->destroy($id, 2);
                }
            } else {
                $deleteLable = 'delete';
                foreach ($ids as $id) {
                    $this->documentRepository->destroy($id);
                }
            }
            DB::commit();
            Session::flash('success', 'Selected document(s) ' .$deleteLable. ' successfully!');

            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('fail', 'Selected document(s) ' .$deleteLable. ' failed!');
            return 'fail';
        }
    }
}
