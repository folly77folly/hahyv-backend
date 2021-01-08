<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Collections\StatusCodes;
use App\Http\Controllers\Controller;

class CommonFunctionsController extends Controller
{
    //
    public function api_default_fail_response($fxname, \Exception $e) {
		\Log::alert('API Exception : ' . $fxname . ' : ' . $e->getMessage());
		\Log::debug($e);
		$array_json_return = array(
			'status' => 'failure',
			'status_code' => StatusCodes::BAD_REQUEST,
			'message' => 'Error. Please try again.'.$e->getMessage()
			
		);
		return $array_json_return;
	}
}
