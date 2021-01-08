<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonFunctionsController extends Controller
{
    //
    public function api_default_fail_response($fxname, \Exception $e) {
		\Log::alert('API Exception : ' . $fxname . ' : ' . $e->getMessage());
		\Log::debug($e);
		$array_json_return = array(
			'status' => 'failure',
			'message' => 'Error. Please try again.'.$e->getMessage()
		);
		return $array_json_return;
	}
}
