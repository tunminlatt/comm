<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AudioRepository;
use App\Repositories\VolunteerRepository;
use App\Http\Resources\API\v1\AudioDetailResource;
use App\Http\Resources\API\v1\AudioResource;
use App\Helpers\Responder;
use App\Helpers\Image;
use Validator;
use DB;

class AudioController extends Controller
{
    public function __construct(
        AudioRepository $audioRepository,
        VolunteerRepository $volunteerRepository,
        Image $image,
        Responder $responder
    ) {
        $this->audioRepository = $audioRepository;
        $this->volunteerRepository = $volunteerRepository;
        $this->image = $image;
        $this->responder = $responder;
    }

    protected function supportLargeFileUpload() {
        // prepare variables
        $size = '1000M'; // 1GB
        $time = -1; // 10 min

        // change setting
        ini_set('upload_max_filesize', $size);
        ini_set('post_max_size', $size);
        ini_set('max_input_time', $time);
        ini_set('max_execution_time', $time);
    }

    protected function packData($request) {
        return $request->only('title', 'duration', 'note');
    }

    /**
    * @SWG\Post(
    *     @SWG\Parameter(
    *         name="api-token",
    *         in="header",
    *         required=true,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="title",
    *         in="query",
    *         required=true,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="note",
    *         in="query",
    *         required=false,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="duration",
    *         in="query",
    *         required=true,
    *         type="string",
    *         description="Format millisecond"
    *     ),
    *     @SWG\Parameter(
    *         name="recorded_file",
    *         in="formData",
    *         required=true,
    *         type="file",
    *         description="Audio Recorded file Upload ( max : 1GB )"
    *     ),
    *     @SWG\Parameter(
    *         name="image",
    *         in="formData",
    *         required=false,
    *         type="file",
    *         description="Audio Photo Upload (max : 2MB )"
    *     ),
    *   path="/postRecordFile",
    *   summary="Recorded File Upload",
    *   tags={"Editor"},
    *   operationId="postRecordFile",
    *   @SWG\Response(response=200, description="Successful operation !"),
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

    public function postRecordFile(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:100',
            'note' => 'max:1000',
            'duration' => 'required',
            'recorded_file' => 'required|file|max:1000000',
        ];

        if ($request->has('image')) {
            $rules['image'] = 'file|image|max:2000';
        }

        $validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }

        DB::beginTransaction();

        try {
             // prepare variables
            $token = $request->header('api-token');
            $volunteer = $this->volunteerRepository->show('api_token', $token, [], false);

            $payload = $this->packData($request);
            $payload['uploaded_by'] = $volunteer->id;
            $payload['station_id'] = $volunteer->station_id;
            $secText = floor(($payload['duration'] / 1000) % 60);
            if(floor(($payload['duration'] / 1000) % 60) < 10){
                $secText = "0".floor(($payload['duration'] / 1000) % 60);
            }
            $payload['duration'] =  floor($payload['duration'] / 60000) . ":" . $secText;
            $audios = $this->audioRepository->show('title', $payload['title'], [], false, true);

            if($audios->count() > 0){
                $partTitle = $payload['title'] .' ('. ($audios->count() + 1) .')';
                $payload['title'] = $partTitle;
            }

            // create audio
            $this->supportLargeFileUpload();
            $audio = $this->audioRepository->create($payload);

            $audioID = $audio->id;

            if ($request->has('image')) {
                // create banner
                $this->image->add('audios/'. $audioID .'/banner', [$request->image]);
            }
            // create recording
            $this->image->add('audios/'. $audioID .'/recording', [$request->recorded_file]);

            DB::commit();

            return $this->responder->customResponse(200, 'Audio Uploaded Suceesful!');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    /**
    * @SWG\Get(
    *     @SWG\Parameter(
    *         name="api-token",
    *         in="header",
    *         required=true,
    *         type="string",
    *     ),
    *   path="/getUploadedRecord",
    *   summary="Get Uploaded Records",
    *   tags={"Editor"},
    *   operationId="getUploadedRecord",
    *   @SWG\Response(response=200, description="Successful operation !",
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
    public function getUploadedRecord(Request $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $token = $request->header('api-token');

            $volunteer = $this->volunteerRepository->show('api_token', $token, [], false);

            if ($volunteer->exists()) {
                // prepare variables
                $uploaded_by = $volunteer->id;

                $audios = $this->audioRepository->show('uploaded_by', $uploaded_by, ['station', 'volunteer'], true, true, true);
                DB::commit();

                return AudioResource::collection($audios);

            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}