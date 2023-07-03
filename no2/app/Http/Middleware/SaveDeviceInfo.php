<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Auth;
use App\Models\ActivityRecord;
use App\Models\UserDevice;

class SaveDeviceInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $user                           = Auth::user();
        $deviceInfo                     = '-';
        if($request->header('Device-Info')){
            $deviceInfo                 = $request->header('Device-Info');
        }
        if (!$user) {
            $user   = auth('api')->user();
        }else{
            if ($request->header('Token-Firebase') && $user!=null) {
                $tokenFirebase                  = $request->header('Token-Firebase');
                $user->token_firebase           = $tokenFirebase;
                $user->device_info              = $deviceInfo;
                $user->current_apk_version_code = $request->header('Version-Code');
                $user->current_apk_version_name = $request->header('Version-Name');

                $userDevice                     = UserDevice::where('firebase_token',$tokenFirebase)->first();
                if(!$userDevice){
                    $userDevice                 = new UserDevice();
                    $userDevice->firebase_token = $tokenFirebase;
                }
                $userDevice->device_info    = $deviceInfo;
                $userDevice->save();
            }
            $user->last_access                  = Carbon::now();
            //$user->last_activity                = $request->path();
            $user->save();
        }
        return $next($request);
    }
}
