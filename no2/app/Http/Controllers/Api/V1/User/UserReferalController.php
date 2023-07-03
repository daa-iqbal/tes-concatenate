<?php

namespace App\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Hash;
use Auth;
use DB;
use Carbon\Carbon;
use App\Models\DumaPoint;
USE Config;
use App\Models\UserReferal;
use App\Models\Nasabah;
use App\Models\TabunganHaji;
use App\Models\TabunganUmrah;

class UserReferalController extends Controller{
    public function all(Request $request){
        $datas      = [];
        if($request->referrer_id){
            $datas  = UserReferal::where('referrer_id',$request->referrer_id)->get();
        }else{
            $datas  = UserReferal::all();
        }
        $datas->load(['referrer','referee','duma_point']);
        return $this->success(@count($datas).' data berhasil ditampilkan', $datas);
    }

    public function claim(Request $request){
        $rules = [
            'id'            => 'required|int|exists:user_referals,id',
        ];
        $validator          =  $this->customValidation($request, $rules);
        if ($validator !== TRUE) {
            return $validator;
        }
        $user_referal       = UserReferal::find($request->id());
        $user               = User::find(Auth::id());
        if($user_referal->referrer_id == $user->id){
            if($user_referal->current_step >= 3){
                $nominal                        = 0;
                if($user->nominal_referral!=null){
                    $nominal                    = $user->nominal_referral;
                }
                $duma_point                     = new DumaPoint();
                $duma_point->in                 = $nominal;
                $duma_point->user_id            = $user->id;
                $duma_point->save();
                
                $user_referal->claimed_at       = Carbon::now();
                $user_referal->duma_point_id    = $duma_point->id;
                $user_referal->save();
                return $this->success('Klaim berhasil. Anda mendapatkan '.$nominal.' duma point.');
            }else{
                return $this->failure('Anda belum bisa klaim sebelum User melakukan setoran awal');
            }
        }else{
            return $this->failure('Anda bukan Referrer');
        }
        $datas      = [];
        if($request->referrer_id){
            $datas  = UserReferal::where('referrer_id',$request->referrer_id)->get();
        }else{
            $datas  = UserReferal::all();
        }
        $datas->load(['referrer','referee','duma_point']);
        return $this->success(@count($datas).' data berhasil ditampilkan', $datas);
    }

    public function repair(Request $request){
        $userReferals               = UserReferal::all();
        $updateCount                = 0;
        foreach($userReferals as $userReferal){
            $nasabah_ids                    = Nasabah::where('user_id',$userReferal->referee_id)->pluck('id');
            $umrahTerbayar                  = TabunganUmrah::whereIn('nasabah_id',$nasabah_ids)
            ->whereIn('status_tabungan_umrah_id',[2,3,4])->count();
            $hajiTerbayar                   = TabunganHaji::whereIn('nasabah_id',$nasabah_ids)
            ->whereIn('status_tabungan_haji_id',[2,3,4])->count();
            $totalTerbayar                  = $umrahTerbayar + $hajiTerbayar;
            if($totalTerbayar>0 && $userReferal->current_step != 3){
                $userReferal                = UserReferal::find($userReferal->id);
                $userReferal->current_step  = 3;
                $userReferal->save();
                $updateCount++;
            }else if(@count($nasabah_ids)>0 && $userReferal->current_step < 2){
                $userReferal                = UserReferal::find($userReferal->id);
                $userReferal->current_step  = 2;
                $userReferal->save();
                $updateCount++;
            }
        }
        return $this->success($updateCount.' data telah diupdate.');
    }
}
