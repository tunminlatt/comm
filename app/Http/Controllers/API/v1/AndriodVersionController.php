<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AndriodVersionRepository;
use Illuminate\Support\Facades\Storage;

class AndriodVersionController extends Controller
{
	public function __construct(
        AndriodVersionRepository $andriodVersionRepository
    ) {
        $this->andriodVersionRepository = $andriodVersionRepository;
    }

    public function getLatestVersion(Request $request)
    {
    	$andriodVersion = $this->andriodVersionRepository->show('id', '0c43ede3-8288-4bbf-9fa3-88dccd72187c', null, false, false);
    	if (Storage::has('andriodVersion/yyat.apk')) {
    		$self_hosted_link = Storage::url('andriodVersion/yyat.apk');
    	} else {
    		$self_hosted_link = null;
    	}
    	if ($andriodVersion) {
    		$result = [
	    		"latest_version_code" => $andriodVersion->latest_version_code,
	    		"require_force_update" => $andriodVersion->require_force_update,
	    		"min_version_code" => $andriodVersion->min_version_code,
	    		"play_store_link" => $andriodVersion->play_store_link,
	    		"self_hosted_link" => $self_hosted_link,
	    	];
    	} else {
    		$result = null;
    	}
    	return response()->json($result, 200);
    }
}
         