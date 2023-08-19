<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StationRepository;
use App\Http\Requests\Admin\StoreStationRequest;
use App\Http\Requests\Admin\UpdateStationRequest;
use App\Http\Resources\Admin\StationDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Image;
use DB;

class StationController extends Controller
{
    public function __construct(
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->stationRepository = $stationRepository;
        $this->datatables = $datatables;
        $this->image = $image;

    }

    protected function packData($request) {
        return $request->only('title', 'description', 'phone', 'email', 'facebook_link', 'messenger_link', 'public_key', 'private_key');
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
                $stations = $this->stationRepository->getAllForTable($request, [], false, true);
            }else{
                $stations = $this->stationRepository->getAllForTable($request, [], false);
            }

            $stationDatatableResource = StationDatatableResource::collection($stations);

            return $this->datatables->of($stationDatatableResource)->addIndexColumn()->toJson();
        }

        return view('admin.stations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.stations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStationRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            if($request->has('is_public') && $request->is_public == 'on') {
                $payload['is_public'] = 1;
            } else {
                $payload['is_public'] = 0;
            }

            // create station
            $station = $this->stationRepository->create($payload);

            // create image
            $this->image->add('stations/'. $station->id, [$request->image]);
            if ($request->hasFile('profile_image')) {
                $this->image->add('stations/profile/'. $id, [$request->profile_image], true);
            }
            DB::commit();

            session()->flash('success', 'Station Created Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.stations.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stations.index')->with('fail', 'Station Creating Failed!');
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
        $station = $this->stationRepository->show('id', $id, [], true);
        $images = $this->image->get('stations/'. $id);
        $profileImages = $this->image->get('stations/profile/'. $id);

        return view('admin.stations.edit', ['station' => $station, 'images' => $images, 'profileImages' => $profileImages]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStationRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            if($request->has('is_public') && $request->is_public == 'on') {
                $payload['is_public'] = 1;
            } else {
                $payload['is_public'] = 0;
            }

            // update station
            $this->stationRepository->update($id, $payload, true);

            // remove old uploads
            $oldUploadsToDelete = json_decode($request->old_upload_to_delete[0]);
            if ($oldUploadsToDelete && count($oldUploadsToDelete) > 0) {
                $this->image->delete('stations/'. $id, $oldUploadsToDelete);
            }

            // add new uploads
            if ($request->hasFile('image')) {
                $this->image->add('stations/'. $id, [$request->image], true);
            }
            if ($request->hasFile('profile_image')) {
                $this->image->delete('stations/profile/'. $id);
                $this->image->add('stations/profile/'. $id, [$request->profile_image], true);
            }
            DB::commit();

            session()->flash('success', 'Station Updated Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.stations.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stations.index')->with('fail', 'Station Updating Failed!');
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
            $this->stationRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.stations.index')->with('success', 'Station Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stations.index')->with('fail', 'Station Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->stationRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.stations.index')->with('success', 'Station Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stations.index')->with('fail', 'Station Activating Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('stations/'. $id);
            $this->stationRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.stations.index')->with('success', 'Station Destoryed Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stations.index', ['trash' => 'all'])->with('fail', 'Station Destorying Failed! You need to destory all related audios, programs and volunteers to delete a station.');
        }
    }

}
