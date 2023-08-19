<?php

namespace App\Http\Controllers\API\v1;

use DB;
use Validator;
use App\Helpers\Responder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StationRepository;
use App\Repositories\ProgrammeRepository;
use App\Http\Resources\API\v1\StationResource;
use App\Http\Resources\API\v1\StationDetailResource;

class StationController extends Controller
{
    public function __construct(
        StationRepository $stationRepository,
        ProgrammeRepository $programmeRepository,
        Responder $responder
    ) {
        $this->stationRepository = $stationRepository;
        $this->programmeRepository = $programmeRepository;

        $this->responder = $responder;
    }

    /**
    * @SWG\Get(
    *   path="/getAllStations",
    *   summary="Get All Stations",
    *   tags={"Station"},
    *   operationId="getAllStations",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        {
                            "id": "f1035694-ece8-493f-a86d-ed09be6f5f73",
                            "title": "labore",
                            "image": null,
                            "contact": {
                                "phone": "7864736348878",
                                "facebook_link": "http://www.kuhlman.net/cupiditate-dolores-molestiae-porro-consequuntur-adipisci-officia.html",
                                "messenger_link": null,
                                "email_link": "kara.lehner@gmail.com"
                            }
                        }
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
    public function getAllStations(Request $request) {
        DB::beginTransaction();
        try {
            $stations = $this->stationRepository->all([], false, 100);
            DB::commit();

            if ($stations->count() > 0) {
                return $this->responder->paginateResponse(StationResource::collection($stations), $stations);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getAllPublicStations() {
        DB::beginTransaction();
        try {
            $stations = $this->stationRepository->privateOrPublicStations(1, [], false, 100); // 1 is public, 0 is private
            DB::commit();

            foreach ($stations as $station) {
                $station->updated_at = null;

                $latestProgramme = $this->programmeRepository->getLatestPublishedDate($station->id);

                if ($latestProgramme != null) {
                    $station->updated_at = $latestProgramme->schedule;
                }
            }

            if ($stations->count() > 0) {
                return $this->responder->paginateResponse(StationResource::collection($stations), $stations);
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
    *   path="/getStationDetail",
    *   summary="Get Station Detail",
    *   tags={"Station"},
    *   operationId="getStationDetail",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        "id": "1ccd1fa5-52cc-4c47-9aa2-c598afe7b0da",
                        "title": "ipsa",
                        "description": "Quia amet modi illo molestiae.",
                        "email": "huels.idell@gmail.com",
                        "phone": "6997585977406",
                        "facebook_link": "http://stamm.biz/dolores-qui-eveniet-est-autem-sunt-nemo.html",
                        "messenger_link": null,
                        "programmes": {
                            {
                                "id": "51ac728b-15fa-4ff9-8531-ed93ff139811",
                                "title": "Test 101010",
                                "duration": 155000,
                                "created_at": "February 24 2020, 8:26 pm",
                                "image": null,
                                "audio": null,
                                "description": "Test",
                                "station_title": "ipsa"
                            },
                            {
                                "id": "642f7671-86ee-476d-b266-4114562f94f8",
                                "title": "GGGETETETE",
                                "duration": 10000,
                                "created_at": "12 hours ago",
                                "image": null,
                                "audio": null,
                                "description": "Hello",
                                "station_title": "ipsa"
                            },
                            {
                                "id": "a53e9290-e560-4da3-9f9e-a403b784966d",
                                "title": "Aspernatur doloribus laboriosam dolore totam.",
                                "duration": 3084000,
                                "created_at": "February 24 2020, 8:26 pm",
                                "image": null,
                                "audio": null,
                                "description": "",
                                "station_title": "ipsa"
                            }
                        },
                        "image": null
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
    public function getStationDetail(Request $request) {
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
            $programmeFilter = function ($query) {
                $query->where('state_id', 2);
            };
            $station = $this->stationRepository->show('id', $id, ['programmes' => $programmeFilter], true, false);
            DB::commit();

            if ($station->exists()) {
                return new StationDetailResource($station);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}
