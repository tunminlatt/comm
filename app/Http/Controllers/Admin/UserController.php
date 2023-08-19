<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserDatatableResource;
use Yajra\Datatables\Datatables;
use App\Helpers\Seed;
use Hash;
use DB;
use App\Models\About;

class UserController extends Controller
{
    public function __construct(
        UserRepository $userRepository,
        Datatables $datatables
    ) {
        $this->userRepository = $userRepository;
        $this->datatables = $datatables;
        $this->userTypeID = 1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $filter = [
                'column' => 'user_type_id',
                'value' => $this->userTypeID,
            ];
            if($request->trash == 'all'){
                $users = $this->userRepository->getAllForTable($request, [], false, $filter, true);
            }else{
                $users = $this->userRepository->getAllForTable($request, [], false, $filter);
            }
            $userDatatableResource = UserDatatableResource::collection($users);

            return $this->datatables->of($userDatatableResource)->addIndexColumn()->toJson();
        }

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type_id' => $this->userTypeID,
                'email_verified_at' => Seed::generateCurrentDate(),
                'remember_token' => Seed::generateRememberToken(),
            ];

            // create admin
            $this->userRepository->create($payload);
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Admin Created Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')->with('fail', 'Admin Creating Failed!');
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
        $user = $this->userRepository->show('id', $id, [], true);
        return view('admin.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $request->only('name', 'email');

            if ($request->new_password != null) {
                $payload['password'] = Hash::make($request->new_password);
            }

            // update admin
            $this->userRepository->update($id, $payload, true);
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Admin Updated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')->with('fail', 'Admin Updating Failed!');
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

            return redirect()->route('admin.users.index')->with('success', 'Admin Deleted Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')->with('fail', 'Admin Deleting Failed!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->userRepository->restore($id);
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Admin Activated Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')->with('fail', 'Admin Activating Failed!');
        }
    }

    public function forceDelete($id)
    {
        DB::beginTransaction();

        try {
            $this->userRepository->destroy($id, 2);
            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Admin Destoryed Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index', ['trash' => 'all'])->with('fail', 'Admin Destorying Failed!');
        }
    }

    public function aboutGet()
    {
        $about = About::first();
        return ['description' => isset($about->description) ? $about->description : 'No description about detail.'];
    }

    public function aboutStore(Request $request)
    {
        $about = About::first();
        if(isset($about)){
            $about->description = $request->description;
            $about->save();
        }else{
            $about = new About;
            $about->description = $request->description;
            $about->save();
        }
        return 'About Changed Successfully!';
    }

}
