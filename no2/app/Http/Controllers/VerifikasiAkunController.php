<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TokenVerification;
use App\User;
use Hash;
use App\Role;
use DB;

class VerifikasiAkunController extends Controller
{
    //
    public function addPassword($id){

        $id_user = TokenVerification::where('token_verification',$id)->where('verified_at',null)->value('id_user');
        
        if(!empty($id_user)){
            return view('mail.add_password')->with('id_user',$id_user)->with('token_verification',$id);
        }else{
            return redirect()->route('login');
        }

    }

    public function storePassword(Request $request){

        DB::beginTransaction();
        $arrUser =[
            'password'=> Hash::make($request->password),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' =>1,


        ];

        $verified_at =[
            'verified_at' =>now()
        ];
        
        $user = User::where('id',$request->id_user)->first();

        $role               = Role::whereIn('name',['customer'])->first();                
        $role_ids           = [$role->id];                
        $user->roles()->sync($role_ids);


        $UpdatePassword = User::where('id',$request->id_user)->update($arrUser);
        $updateVerified = TokenVerification::where('token_verification',$request->token_verification)->update($verified_at);
        DB::commit();
        
        return $this->success('Berhasil',$updateVerified);
    }
}
