<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\v1\AboutResource;
use App\Repositories\AboutRepository;
use App\Helpers\Responder;
use DB;

class AboutController extends Controller
{
    public function __construct(
        AboutRepository $aboutRepository,
        Responder $responder
    ) {
        $this->aboutRepository = $aboutRepository;
        $this->responder = $responder;
    }

    /**
    * @SWG\Get(
    *   path="/getAbout",
    *   summary="Get App About",
    *   tags={"About"},
    *   operationId="about",
    *   @SWG\Response(response=200, description="Successful operation !",
    *       examples={"application/json":
    *          {"data":
                    {
                        "about_message": "eyJpdiI6Ik5COUV5Y1ltRTM4eXNsRlpLY2ptTGc9PSIsInZhbHVlIjoiNDFCbG95c1RHSHRFT0IyWWZ4aWFRQVJ6RHhTS1A4SFJiQXp2amlQc3RCUFRUWWs5R3RQQ0ZlakdFNnlvRm50MSIsIm1hYyI6ImM"
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
    /**
    *
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function about()
    {

        DB::beginTransaction();

        try {
            $about = $this->aboutRepository->all([], false);
            if ($about->exists()) {
                return new AboutResource($about);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getContactMethods()
    {
        try {
            $contactMethods = config('contact.contact-methods');
            if(count($contactMethods) > 0) {
                return $contactMethods;
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}