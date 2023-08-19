<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VolunteerRepository;
use App\Repositories\StationRepository;
use App\Http\Requests\Admin\StoreVolunteerRequest;
use App\Http\Requests\Admin\UpdateVolunteerRequest;
use App\Http\Resources\Admin\VolunteerDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Image;
use DB;
use Hash;
use Auth;
use Illuminate\Support\Facades\Session;

class VolunteerController extends Controller
{
    public function __construct(
        VolunteerRepository $volunteerRepository,
        StationRepository $stationRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->volunteerRepository = $volunteerRepository;
        $this->stationRepository = $stationRepository;
        $this->datatables = $datatables;
        $this->image = $image;
    }

    protected function packData($request) {
        return $request->only('name', 'phone', 'address', 'station_id', 'email');
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
                $volunteers = $this->volunteerRepository->getAllForTable($request, ['station'], false, true);
            }else{
                $volunteers = $this->volunteerRepository->getAllForTable($request, ['station'], false);
            }
            $volunteerDatatableResource = VolunteerDatatableResource::collection($volunteers);

            return $this->datatables->of($volunteerDatatableResource)->addIndexColumn()->toJson();
        }

        return view('admin.volunteers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // prepare variables
        $user = Auth::user();
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;
        $stations = ($userTypeID == 1) ? $this->stationRepository->all([], false) : [];

        return view('admin.volunteers.create', ['userStationID' => $userStationID, 'userTypeID' => $userTypeID, 'stations' => $stations]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVolunteerRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            $payload['address'] = $request->address !== null ? $request->address : '';
            $payload['password'] = Hash::make($request->password);

            // create volunteer
            $volunteer = $this->volunteerRepository->create($payload);

            // create image
            if($request->image != null){
                $this->image->add('volunteers/'. $volunteer->id, [$request->image]);
            }
            DB::commit();

            session()->flash('success', 'Volunteer Created Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.volunteers.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.volunteers.index')->with('fail', 'Volunteer Creating Failed!');
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
        $volunteer = $this->volunteerRepository->show('id', $id, [], true);
        $images = $this->image->get('volunteers/'. $id);

        return view('admin.volunteers.edit', ['userStationID' => $userStationID, 'userTypeID' => $userTypeID, 'volunteer' => $volunteer, 'stations' => $stations, 'images' => $images]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVolunteerRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $this->packData($request);
            $payload['address'] = $request->address !== null ? $request->address : '';


            if ($request->new_password != null) {
                $payload['password'] = Hash::make($request->new_password);
            }

            // update volunteer
            $this->volunteerRepository->update($id, $payload, true);

            // remove old uploads
            $oldUploadsToDelete = json_decode($request->old_upload_to_delete[0]);
            if ($oldUploadsToDelete && count($oldUploadsToDelete) > 0) {
                $this->image->delete('volunteers/'. $id, $oldUploadsToDelete);
            }

            // add new uploads
            if ($request->hasFile('image')) {
                $this->image->add('volunteers/'. $id, [$request->image], true);
            }
            DB::commit();

            session()->flash('success', 'Volunteer Updated Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.volunteers.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.volunteers.index')->with('fail', 'Volunteer Updating Failed!');
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
            $this->volunteerRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.volunteers.index')->with('success', 'Volunteer Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.volunteers.index')->with('fail', 'Volunteer Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->volunteerRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.volunteers.index')->with('success', 'Volunteer Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.volunteers.index')->with('fail', 'Volunteer Activating Failed!');
        }
    }

    public function getVolunteersByStation(Request $request) {
        // prepare variables
        $volunteerWithStation = [];
        $stationID = $request->station_id;
        // get all volunteers
        $volunteers = $this->volunteerRepository->getByStaion($stationID);
        foreach ($volunteers as $volunteer) {
            $volunteerWithStation[] = [
                'id' => $volunteer->id,
                'name' => $volunteer->name,
            ];
        }
        return ['volunteerWithStation' => $volunteerWithStation];
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->image->delete('volunteers/'. $id);
            $this->volunteerRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.volunteers.index')->with('success', 'Volunteer and it related audio(s) destoryed Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.volunteers.index', ['trash' => 'all'])->with('fail', 'Volunteer destory failed! You need to destory all related audios to delete a volunteer.');
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
                    $this->image->delete('volunteers/'. $id);
                    $this->volunteerRepository->destroy($id, 2);
                }
            } else {
                $deleteLable = 'delete';
                foreach ($ids as $id) {
                    $this->volunteerRepository->destroy($id);
                }
            }
            DB::commit();
            Session::flash('success', 'Selected volunteer(s) and it related audio(s) ' .$deleteLable. ' successfully!');
            return 'success';
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('fail', 'Selected volunteer(s) ' .$deleteLable. ' failed! You need to destory all related audios to delete a volunteer.');
            return 'fail';
        }
    }

}