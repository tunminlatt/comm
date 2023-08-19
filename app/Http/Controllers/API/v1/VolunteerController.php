<?php

namespace App\Http\Controllers\API\v1;

use DB;
use Validator;
use App\Helpers\Image;
use App\Models\Station;
use App\Helpers\Responder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\VolunteerRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Repositories\VolunteerRepository;
use App\Http\Resources\API\v1\VolunteerResource;

class VolunteerController extends Controller
{

    private $apiToken;

    public function __construct(
        VolunteerRepository $volunteerRepository,
        Responder $responder,
        Image $image
    ) {
        $this->apiToken = uniqid(base64_encode(Str::random(60)));
        $this->volunteerRepository = $volunteerRepository;
        $this->responder = $responder;
        $this->image = $image;
    }

    /**
    * @SWG\Post(
    *     @SWG\Parameter(
    *         name="phone",
    *         in="query",
    *         required=true,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="password",
    *         in="query",
    *         required=true,
    *         type="string",
    *     ),
    *   path="/loginVolunteer",
    *   summary="Volunteer Login",
    *   tags={"Volunteer"},
    *   operationId="login",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        "id": "2490462d-cc46-47d2-9be7-4d9fad82125d",
                        "name": "Thureiu Loki",
                        "phone": "0972581032",
                        "address": "",
                        "image": null,
                        "station_title": "Messenger Link",
                        "created_at": "January 16 2020, 2:19 pm",
                        "api-token": "bjFwd2xSREFUcjZzOXdidUNXb2NlbkRXVmFXeEl0NlB5YVJ0RlQzMWlORXhsQkJaQlNZckM5UTc4WlpL5e543405933d1"
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
    public function login(Request $request) {

        if(is_numeric($request->get('phone'))){
            $customRules = 'required|numeric|digits_between:1,20';
            $searchColumn = 'phone';
        } elseif(filter_var($request->get('phone'), FILTER_VALIDATE_EMAIL)) {
            $customRules = 'required|email';
            $searchColumn = 'email';
        } else {
            $customRules = 'required|numeric|digits_between:1,20';
            $searchColumn = 'phone';
        }
        $rules = [
            'phone' => $customRules,
            'password' => 'required|string|min:8|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }

        DB::beginTransaction();

        try {
            // prepare variables
            $phone = $request->phone;
            // Fetch Volunteer
            $volunteer = $this->volunteerRepository->show($searchColumn, $phone, [], false);

            if ($volunteer->exists()) {
                    // Verify the password
                    if (Hash::check($request->password, $volunteer->password)) {
                    // Update Token
                    $payload = ['api_token' => $this->apiToken];
                    // prepare variables
                    $id = $volunteer->id;
                    // update volunteer
                    $this->volunteerRepository->update($id, $payload, false);

                    $volunteer = $this->volunteerRepository->show('api_token', $this->apiToken, [], false);

                    DB::commit();

                    return new VolunteerResource($volunteer);

                    } else {
                        return $this->responder->customResponse(401, 'Your phone number or password is incorrect!');
                    }

            } else {
                return $this->responder->customResponse(401, 'Your phone number or password is incorrect!');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    /**
    * @SWG\Post(
    *     @SWG\Parameter(
    *         name="api-token",
    *         in="header",
    *         required=true,
    *         type="string",
    *     ),
    *   path="/logoutVolunteer",
    *   summary="Volunteer logout",
    *   tags={"Volunteer"},
    *   operationId="logout",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *           {
                    "status": 200,
                    "message": "Volunteer Logged Out!"
                }
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
    public function logout(Request $request) {

        DB::beginTransaction();

        try {
            // prepare variables
            $token = $request->header('api-token');

            $volunteer = $this->volunteerRepository->show('api_token', $token, [], false);

            if ($volunteer->exists()) {

                // Update Token
                $payload = ['api_token' => null];
                // prepare variables
                $id = $volunteer->id;
                // update volunteer
                $this->volunteerRepository->update($id, $payload, false);
                DB::commit();

                return $this->responder->customResponse(200, 'Volunteer Logged Out!');
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    /**
    * @SWG\POST(
    *     @SWG\Parameter(
    *         name="api-token",
    *         in="header",
    *         required=true,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="name",
    *         in="query",
    *         required=true,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="address",
    *         in="query",
    *         required=false,
    *         type="string",
    *     ),
    *     @SWG\Parameter(
    *         name="image",
    *         in="formData",
    *         required=false,
    *         type="file",
    *         description="Volunteer Photo Upload ( max : 2MB )"
    *     ),
    *     @SWG\Parameter(
    *         name="image_status",
    *         in="query",
    *         required=true,
    *         type="string",
    *         default=0,
    *         description="1 for new upload & 0 for old image"
    *     ),
    *   path="/updateVolunteer",
    *   summary="Volunteer Profile Update",
    *   tags={"Volunteer"},
    *   operationId="update",
    *   @SWG\Response(response=200, description="Successful message !",
    *       examples={"application/json":
    *          {"data":
                    {
                        "id": "2490462d-cc46-47d2-9be7-4d9fad82125d",
                        "name": "Thureiu Loki",
                        "phone": "0972581032",
                        "address": "",
                        "image": null,
                        "station_title": "Messenger Link",
                        "created_at": "January 16 2020, 2:19 pm",
                        "api-token": "bjFwd2xSREFUcjZzOXdidUNXb2NlbkRXVmFXeEl0NlB5YVJ0RlQzMWlORXhsQkJaQlNZckM5UTc4WlpL5e543405933d1"
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
    public function update(Request $request) {
        $rules = [
            'name' => 'required|string|max:100',
            'address' => 'max:1000'
        ];

        if ($request->image_status == 1) {
            $rules['image'] = 'required|file|image|max:2000';
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

            if ($volunteer->exists()) {
                // prepare variables
                $id = $volunteer->id;
                $payload = $this->packData($request);
                $double = '"';
                $payload['name'] = str_replace($double, "", $payload["name"]);
                $payload['address'] = $request->address !== null ? str_replace($double, "", $payload["address"]) : '';
                // update volunteer
                $this->volunteerRepository->update($id, $payload, false);

                // remove old uploads
                if ($request->image_status == 1) {
                    $this->image->delete('volunteers/'. $id);
                    $this->image->add('volunteers/'. $id, [$request->image], true);
                }

                DB::commit();

                $volunteer = $this->volunteerRepository->show('api_token', $token, [], false);

                return new VolunteerResource($volunteer);

            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }

    }

    public function request(Request $request)
    {
        $rules = [
            'station_id' => 'required',
            'volunteer_name' => 'required|string|max:100',
            'volunteer_phone' => 'required|numeric|digits_between:1,20',
        ];

        if ($request->message) {
            $rules['message'] = 'required|string|max:500';
        }
        $validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }

        DB::beginTransaction();

        try {
            $station = Station::find($request->station_id);
            Mail::to($station->email)->send(new VolunteerRequest($request, $station));

            return $this->responder->customResponse(200, 'Request email sent Successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    protected function packData($request) {
        return $request->only('name','address');
    }
}