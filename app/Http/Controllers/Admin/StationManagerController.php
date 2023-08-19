<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\StationRepository;
use App\Http\Requests\Admin\StoreStationManagerRequest;
use App\Http\Requests\Admin\UpdateStationManagerRequest;
use App\Http\Resources\Admin\StationManagerDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Seed;
use Hash;
use DB;
use Auth;

class StationManagerController extends Controller
{
    public function __construct(
        UserRepository $userRepository,
        StationRepository $stationRepository,
        Datatables $datatables
    ) {
        $this->userRepository = $userRepository;
        $this->stationRepository = $stationRepository;
        $this->datatables = $datatables;
        $this->userTypeID = 2;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->user_type_id == 2){
            return redirect('/');
        }
        if (request()->ajax()) {
            $filter = [
                'column' => 'user_type_id',
                'value' => $this->userTypeID,
            ];
            if($request->trash == 'all'){
                $users = $this->userRepository->getAllForTable($request, ['station'], false, $filter, true);
            }else{
                $users = $this->userRepository->getAllForTable($request, ['station'], false, $filter);
            }
            $stationManagerDatatableResource = StationManagerDatatableResource::collection($users);

            return $this->datatables->of($stationManagerDatatableResource)->addIndexColumn()->toJson();
        }

        return view('admin.stationManagers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stations = $this->stationRepository->all([], false);
        return view('admin.stationManagers.create', ['stations' => $stations]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStationManagerRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type_id' => $this->userTypeID,
                'station_id' => $request->station_id,
                'email_verified_at' => Seed::generateCurrentDate(),
                'remember_token' => Seed::generateRememberToken(),
            ];

            // create admin
            $this->userRepository->create($payload);
            DB::commit();

            return redirect()->route('admin.stationManagers.index')->with('success', 'Station Manager Created Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stationManagers.index')->with('fail', 'Station Manager Creating Failed!');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userTypeID = Auth::user()->user_type_id;
        $user = $this->userRepository->show('id', $id, [], true);
        $stations = $this->stationRepository->all([], false);

        return view('admin.stationManagers.edit', ['userTypeID' => $userTypeID, 'user' => $user, 'stations' => $stations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStationManagerRequest $request, $id)
    {

        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $request->only('name', 'email');

            if ($request->new_password != null) {
                $payload['password'] = Hash::make($request->new_password);
            }

            if ($request->has('station_id')) {
                $payload['station_id'] = $request->station_id;
            }
            // update admin
            $this->userRepository->update($id, $payload, true);
            DB::commit();

            if(Auth::user()->user_type_id == 2){
                return redirect()->route('admin.programmes.index')->with('success', 'Station Manager Updated Successfully!');
            }else{
                return redirect()->route('admin.stationManagers.index')->with('success', 'Station Manager Updated Successfully!');
            }

        } catch (\Exception $e) {
            DB::rollback();

            if(Auth::user()->user_type_id == 2){
                return redirect()->route('admin.programmes.index')->with('fail', 'Station Manager Updating Failed!');
            }else{
                return redirect()->route('admin.stationManagers.index')->with('fail', 'Station Manager Updating Failed!');
            }

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
            $this->userRepository->destroy($id);
            DB::commit();

            return redirect()->route('admin.stationManagers.index')->with('success', 'Station Manager Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stationManagers.index')->with('fail', 'Station Manager Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->userRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.stationManagers.index')->with('success', 'Station Manager Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stationManagers.index')->with('fail', 'Station Manager Activating Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->userRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.stationManagers.index')->with('success', 'Station Manager Destoryed Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.stationManagers.index', ['trash' => 'all'])->with('fail', 'Station Manager Destorying Failed!');
        }
    }

}
