<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ProgrammeRepository;
use App\Http\Resources\API\v1\ProgrammeDetailResource;
use App\Http\Resources\API\v1\ProgrammeResource;
use App\Helpers\Responder;
use DB;
use Validator;

class ProgrammeController extends Controller
{
    public function __construct(
        ProgrammeRepository $programmeRepository,
        Responder $responder
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->responder = $responder;
    }

    /**
    * @SWG\Get(
    *   path="/getAllProgrammes",
    *   summary="Get All Programmes",
    *   tags={"Programme"},
    *   operationId="getAllProgrammes",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        {
                            "id": "2384c529-f0c4-445e-9f51-f5997b78e2e4",
                            "title": "Est earum eveniet ullam minima.",
                            "duration": 2925000,
                            "created_at": "December 6 2019, 4:28 am",
                            "image": null,
                            "audio": null,
                            "note": "Enim sunt est nemo quis voluptas et.",
                            "station_title": "ut"
                        }
                    },
                     "links": {
                        "first": "/getAllProgrammes?page=1",
                        "last": "/getAllProgrammes?page=1",
                        "prev": null,
                        "next": null
                    },
                    "meta": {
                        "current_page": 1,
                        "from": 1,
                        "last_page": 1,
                        "path": "/getAllProgrammes",
                        "per_page": 20,
                        "to": 1,
                        "total": 1
                    }
                },
    *        },
    *    ),
    *   @SWG\Response(response=204, description="No Content !"),
    *   @SWG\Response(response=500, description="Internal Server Error !",
    *       examples={"application/json":
    *           {
                    "status": 500,
                    "message": "Something wrong!"
                }
    *        },
    *    ),
    * )
    *
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getAllProgrammes(Request $request) {
        DB::beginTransaction();
        try {
             // prepare variables
            $id = $request->station_id;
            $column = 'station_id';

            if ($id) {
                $programmes = $this->programmeRepository->getProgramByValue($column, $id, [], true, 20, true);
            } else {
                $programmes = $this->programmeRepository->getProgramByValue('state_id', 2,[], true, 20, true);
            }
            DB::commit();
            if ($programmes->count() > 0) {
                return ProgrammeResource::collection($programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getAllProgrammesByStation($station, $type)
    {
        DB::beginTransaction();
        try {
            $programmes = $this->programmeRepository->getProgramByStation($station, $type, [], false, 20, true);

            DB::commit();
            if ($programmes->count() > 0) {
                return ProgrammeResource::collection($programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    /**
    * @SWG\Get(
    *     @SWG\Parameter(
    *         name="id",
    *         in="query",
    *         required=true,
    *         type="string",
    *     ),
    *   path="/getProgrammeDetail",
    *   summary="Get Programme Detail",
    *   tags={"Programme"},
    *   operationId="getProgrammeDetail",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        "id": "642f7671-86ee-476d-b266-4114562f94f8",
                        "title": "GGGETETETE",
                        "duration": 10000,
                        "created_at": "11 hours ago",
                        "image": null,
                        "audio": null,
                        "description": "Hello",
                        "station_title": "ipsa"
                    }
                },
    *        },
    *    ),
    *   @SWG\Response(response=204, description="No Content !"),
    *   @SWG\Response(response=400, description="Invalid request !",
    *       examples={"application/json":
    *           {
                    "status": 400,
                    "message": "Custome Error Message!"
                }
    *        },
    *    ),
    *   @SWG\Response(response=500, description="Internal Server Error !",
    *       examples={"application/json":
    *           {
                    "status": 500,
                    "message": "Something wrong!"
                }
    *        },
    *    ),
    * )
    *
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getProgrammeDetail(Request $request) {
        $rules = [
	        'id'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }
        DB::beginTransaction();

        try {
            // prepare variables
            $id = $request->id;

            $programme = $this->programmeRepository->show('id', $id, [], true);
            DB::commit();

            if ($programme->exists()) {
                return new ProgrammeDetailResource($programme);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function search()
    {
        $rules = [
	        'key_word'  => 'required',
	        'start_date'  => 'required',
	        'end_date'  => 'required',
        ];

        $validator = Validator::make(request()->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }

        $stationIdsString = request()->station_ids;

        if($stationIdsString) {
            $stationIds = explode(',', $stationIdsString);
        } else {
            $stationIds = [];
        }

        DB::beginTransaction();
        try {

            $programmes = $this->programmeRepository->searchProgramme($stationIds, [], false, 20, true);
            DB::commit();
            if ($programmes->count() > 0) {
                // return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
                return ProgrammeResource::collection($programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}