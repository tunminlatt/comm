<?php

namespace App\Http\Controllers\API\v2;

use App\Models\Reaction;
use App\Helpers\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\v2\ProgrammeDetailResource;
use App\Repositories\ProgrammeRepository;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\API\v2\ProgrammeResource;
use App\Models\Programme;

class NewsFeedController extends Controller
{
    public function __construct(ProgrammeRepository $programmeRepository, Responder $responder)
    {
        $this->programmeRepository = $programmeRepository;
        $this->responder = $responder;
    }

    public function index()
    {
        DB::beginTransaction();
        try {
            if(request()->has('paginate') && request()->paginate == 0) {
                $paginateCount = 0;
            } else {
                $paginateCount = 10;
            }
            $programmes = $this->programmeRepository->getProgramByValue('state_id', 2,[], false, $paginateCount, true);
            DB::commit();
            
            if( $programmes instanceof \Illuminate\Support\Collection ) {
                return ProgrammeResource::collection($programmes);
            }
            
            if ($programmes->count() > 0) {
                return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getVideoProgrammes()
    {
        DB::beginTransaction();
        try {

            $programmes = $this->programmeRepository->getProgramByValue('type', 'video',[], false, 10, true);
            DB::commit();
            if ($programmes->count() > 0) {
                return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getPhotoProgrammes()
    {
        DB::beginTransaction();
        try {

            $programmes = $this->programmeRepository->getProgramByValue('type', 'photo',[], false, 10, true);
            DB::commit();
            if ($programmes->count() > 0) {
                return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function getAudioProgrammes()
    {
        DB::beginTransaction();
        try {

            $programmes = $this->programmeRepository->getProgramByValue('type', 'audio',[], false, 10, true);
            DB::commit();
            if ($programmes->count() > 0) {
                return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
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

            $programmes = $this->programmeRepository->searchProgramme($stationIds, [], false, 10, true);
            DB::commit();
            if ($programmes->count() > 0) {
                return $this->responder->paginateResponse(ProgrammeResource::collection($programmes), $programmes);
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function programmeReact($feed_id)
    {
        $rules = [
	        'reaction_status'  => 'required',
        ];

        $validator = Validator::make(request()->all(), $rules);

		if ($validator->fails()) {
            $messages = $validator->errors()->first();
            return $this->responder->customResponse(400, $messages);
        }

        $programmeId = $feed_id;
        $reactionStatus = request()->reaction_status; // '0' for unlike and '1' for like

        DB::beginTransaction();
        try {
            $reaction = Reaction::where('programme_id', $programmeId)->first();
            if(!$reaction) {
                $reaction = Reaction::create([
                    'programme_id' => $programmeId,
                ]);
            }

            if($reactionStatus == 0 && $reaction->reaction_count != 0) {
                $reaction->reaction_count --;
            }
            if($reactionStatus == 1) {
                $reaction->reaction_count ++;
            }

            $reaction->update();
            DB::commit();
            return $this->responder->updateResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    public function programmeDetail(Programme $programme)
    {
        try {
            return new ProgrammeDetailResource($programme);
        } catch (\Exception $e) {
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}
