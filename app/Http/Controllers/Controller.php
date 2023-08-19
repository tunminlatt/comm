<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @SWG\Swagger(
 *     basePath="/v1",
 *     host=L5_SWAGGER_CONST_HOST,
 *     schemes={"http", "https"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Community Voice Core API",
 *     ),
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
