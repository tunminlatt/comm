<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ShareRepository;
use App\Repositories\VolunteerRepository;
use App\Repositories\StationRepository;
use App\Http\Resources\Admin\ShareDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Image;
use DB;
use Auth;

class ShareController extends Controller
{
    public function __construct(
        ShareRepository $shareRepository,
        VolunteerRepository $volunteerRepository,
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->shareRepository = $shareRepository;
        $this->volunteerRepository = $volunteerRepository;
        $this->stationRepository = $stationRepository;
        $this->datatables = $datatables;
        $this->image = $image;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $shares = $this->shareRepository->getAllForTable($request, 'public', false);
            $shareDatatableResource = ShareDatatableResource::collection($shares);

            return $this->datatables->of($shareDatatableResource)->addIndexColumn()->toJson();
        }

        // prepare variables
        $userTypeID = Auth::user()->user_type_id;
        $shareVolunteers = $this->shareRepository->volunteer('public', false);
        $shareStations = $this->shareRepository->station('public', false);
        $volunteers = $this->volunteerRepository->share($shareVolunteers, false);
        $stations = ($userTypeID == 1) ? $this->stationRepository->share($shareStations, false) : [];

        return view('admin.shares.index', ['userTypeID' => $userTypeID, 'volunteers' => $volunteers, 'stations' => $stations]);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $audio = $this->shareRepository->show('id', $id, 'public', false);
        $banners = $this->image->get('audios/'. $id .'/banner');
        $recordings = $this->image->get('audios/'. $id .'/recording');
        return view('admin.shares.show', ['audio' => $audio, 'banners' => $banners, 'recordings' => $recordings]);
    }
}
