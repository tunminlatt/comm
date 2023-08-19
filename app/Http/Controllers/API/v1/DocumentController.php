<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DocumentRepository;
use App\Repositories\VolunteerRepository;
use App\Http\Resources\API\v1\DocumentResource;
use App\Helpers\Responder;
use App\Helpers\Image;
use Validator;
use DB;

class DocumentController extends Controller
{
    public function __construct(
        DocumentRepository $documentRepository,
        VolunteerRepository $volunteerRepository,
        Image $image,
        Responder $responder
    ) {
        $this->documentRepository = $documentRepository;
        $this->volunteerRepository = $volunteerRepository;
        $this->image = $image;
        $this->responder = $responder;
    }

    protected function supportLargeFileUpload() {
        // prepare variables
        $size = '2000000M'; // 2GB
        $time = -1; // unlimited

        // change setting
        ini_set('upload_max_filesize', $size);
        ini_set('post_max_size', $size);
        ini_set('max_input_time', $time);
        ini_set('max_execution_time', $time);
    }

    protected function packData($request) {
        return $request->only('title', 'note');
    }

    /**
    * @return \Illuminate\Http\Response
    */

    public function postDocumentFile(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:100',
            'note' => 'max:1000',
            'file' => 'required|file|max:2000000',
        ];

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
            $documents = $this->documentRepository->show('title', $payload['title'], [], false, true);

            if($documents->count() > 0){
                $partTitle = $payload['title'] .' ('. ($documents->count() + 1) .')';
                $payload['title'] = $partTitle;
            }

            // create document
            $this->supportLargeFileUpload();
            $document = $this->documentRepository->create($payload);

            $documentID = $document->id;

            $this->image->add('documents/'. $documentID .'/file', [$request->file]);

            DB::commit();

            return $this->responder->customResponse(200, 'Document Uploaded Suceesful!');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getAllDocuments(Request $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $token = $request->header('api-token');

            $volunteer = $this->volunteerRepository->show('api_token', $token, [], false);

            if ($volunteer->exists()) {
                // prepare variables
                $uploaded_by = $volunteer->id;

                $documents = $this->documentRepository->show('uploaded_by', $uploaded_by, ['station', 'volunteer'], true, true, true);
                DB::commit();

                return DocumentResource::collection($documents);

            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, 'Something wrong!');
        }
    }
}