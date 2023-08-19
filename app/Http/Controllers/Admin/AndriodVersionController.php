<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AndriodVersionRepository;
use App\Http\Resources\Admin\AndriodVersionDatatableResource;
use App\Http\Requests\Admin\UpdateAndriodVersionRequest;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use DB;
use App\Helpers\Image;
use Illuminate\Support\Facades\Storage;

class AndriodVersionController extends Controller
{
    public function __construct(
        AndriodVersionRepository $andriodVersionRepository,
        Datatables $datatables,
        Image $image
    ) {
        $this->andriodVersionRepository = $andriodVersionRepository;
        $this->datatables = $datatables;
        $this->userTypeID = 1;
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
            $andriodVersions = $this->andriodVersionRepository->all(null, false, 0);
            $andriodVersionDatatableResource = AndriodVersionDatatableResource::collection($andriodVersions);
            return $this->datatables->of($andriodVersionDatatableResource)->addIndexColumn()->toJson();
        }
        return view('admin.andriodVersions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $andriodVersion = $this->andriodVersionRepository->show('id', $id, [], true);
        $apk = $this->image->get('andriodVersion');
        return view('admin.andriodVersions.edit', ['andriodVersion' => $andriodVersion, 'apk' => $apk]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAndriodVersionRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $require_force_update = $request->require_force_update == 'on' ? 1 : 0;
            $payload = [
                'latest_version_code' => $request->latest_version_code,
                'require_force_update' => $require_force_update,
                'min_version_code' => $request->min_version_code,
                'play_store_link' => $request->play_store_link,
            ];

            // update station
            $this->andriodVersionRepository->update($id, $payload, true);

            // add new uploads
            if ($request->hasFile('self_hosted_link')) {
                $file = $request->file('self_hosted_link');
               // $fileName = $file->getClientOriginalName();
                Storage::putFileAs('andriodVersion', $file, 'yyat.apk');
            }

            DB::commit();

            session()->flash('success', 'Andriod Version Updated Successfully!');
            return response()->json([
                    'status'   => 'success',
                    'redirectUrl' => route('admin.andriodVersions.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect()->route('admin.andriodVersions.index')->with('fail', 'Andriod Version Updating Failed!');
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
        //
    }
}
