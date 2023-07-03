<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Http\ApiResponder;


class Controller extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponder;

    protected function customValidation(Request $request, $rules = [], $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->invalidParameters($validator->errors()->all());
        } else {
            return TRUE;
        }
    }

    protected function guardWithValidation(Request $request, $rules = [], $messages = [], $callback)
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->invalidParameters($validator->errors()->all());
        } else {
            return $callback();
        }
    }

}
