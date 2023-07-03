<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use \Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use DB;
use App\Http\EncapsulatedApiResponder;
use App\Models\ConfirmationUser;
use Mail;
use Illuminate\Support\Str;
use DateTime;
use App\Models\UserRecord;
use App\Models\UserWithToken;
use App\Notifications\KodeOtpAktivasiAkun;
use App\Notifications\FirebasePushNotif;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use App\Models\RoleUser;
use App\Models\Role;
use App\Models\UserDevice;
use App\Http\Controllers\Helpers\EmailHelperController;
use Illuminate\Support\Facades\Crypt;
use stdClass;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->digit_code     = 5;
        $this->email_helper   = new EmailHelperController();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function customRegister(Request $request){
        $dateTime = new DateTime();

        if ($request->isMethod('get')) {
            return view('auth.register');
        }
        else{
            // $validator = $this->customValidation($request, [
        //     'username' => 'required|string',
        //     'password' => 'required|string|min:5',
        //     'email'    => 'required|email|unique:users' ,
        //     'phone'    => 'required|string',
        //     'name'     => 'required|string'
        // ]);
        // if ($validator !== TRUE) {
        //     return $validator;
        // }
            $request->validate([
                //'username' => 'required|string',
                'password' => 'required|string|confirmed|min:5',
                'email'    => 'required|email|unique:users' ,
                'phone'    => 'required|string',
                'name'     => 'required|string'
            ], [
                //'username.required' => 'Username is required!',
                'password.required' => 'Password is required!',
                'email.required'    => 'Email is required!',
                'phone.required'    => 'Phone is required!',
                'name.required'     => 'Name is required!',
                'email.unique'      => 'Email already exist!',
            ]);
            DB::beginTransaction();
            $user               = User::where('email',$request->email)->first();
            if (!$user) {
                $user           = new User();
                if (User::where('phone',$request->phone)->count()>0) {
                    return redirect()->back()->withErrors(['msg', 'Nomor telepon yang anda inputkan sudah terdaftar.']);
                }
            }else{
                if ($user->is_active==1) {
                    return redirect()->back()->withErrors(['msg', 'Email yang anda inputkan sudah terdaftar.']);
                }
                if (User::where('phone',$request->phone)->where('id','!=',$user->id)->count()>0 && $user->is_active == 0) {
                    return redirect()->back()->withErrors(['msg', 'Nomor telepon yang anda inputkan sudah terdaftar.']);
                }
            }
            $kodeNegara             = '+62';
            $user->name             = $request->name;
            $user->email            = $request->email;
            $user->phone            = $request->phone;
            $user->password         = $request->password;
            $user->is_active        = 0;
            $user->save();
            ConfirmationUser::where('user_id',$user->id)->where('confirmation_type_id',1)->delete();
            $code   = str_pad(rand(0, pow(10, $this->digit_code)-1), $this->digit_code, '0', STR_PAD_LEFT);
            while (ConfirmationUser::where('code',$code)->count()>0) {
                $code   = str_pad(rand(0, pow(10, $this->digit_code)-1), $this->digit_code, '0', STR_PAD_LEFT);
            }
            $expired_at                                 = Carbon::now()->addHour()->toDateTimeString();
            $kodeKonfirmasiUser                         = new ConfirmationUser();
            $kodeKonfirmasiUser->code                   = $code;
            $kodeKonfirmasiUser->user_id                = $user->id;
            $kodeKonfirmasiUser->expired_at             = $expired_at;
            $kodeKonfirmasiUser->confirmation_type_id   = 1;
            $kodeKonfirmasiUser->save();
            $jsonConfirmation                           = json_encode($kodeKonfirmasiUser);
            $username            = $request->email;
            $data = array('email'=>$request->email);
            $x = [
                'code'          => url('/').'/regis-confirmation?'.'confirm_code='.Crypt::encryptString($jsonConfirmation),
                'expired_at'    => $expired_at,
                'email'         => $request->email
            ];
            Mail::send([], $data, function($message) use ($x) {
                $message->to($x['email'], env('APP_NAME', 'VIRTUAL HOSPITAL'))->subject('VirtHost - Account Confirmation')->setBody('Klik link berikut untuk melakukan konfirmasi akun : <h2>'.$x['code'].'</h2> Aktifkan akun anda sebelum '.$x['expired_at'],'text/html');
            });


            if($request->role_names){
                $role_names_json    = json_decode($request->role_names, TRUE);
                $role_ids           = Role::whereIn('name',$role_names_json)->pluck('id');
                $user->roles()->sync($role_ids);
            }else{
                $role               = Role::where('name','pasien')->first();
                $role_ids           = [$role->id];
                $user->roles()->sync($role_ids);
            }
            DB::commit();
            $kodeKonfirmasiUser->bypass                 = 0;
            return redirect()->route('login')->with('success', 'Kode verifikasi berhasil dikirim ke '.$username);
        }

    }

    
    public function signupConfirmation(Request $request){
        
        $dateTime = new DateTime();
        $dataJson = Crypt::decryptString($request->confirm_code);
        $result   = json_decode($dataJson); 
        if ($result === FALSE) {
             return $this->failure('Kode verifikasi salah.');
        }      
        DB::beginTransaction();
        $user     = User::where('id',$result->user_id)->first();
        if (!$user) {
            return $this->failure('Pengguna tidak ditemukan.');
        }
       
        $user->phone_verified_at    = Carbon::now()->toDateTimeString();
        
        $kodeKonfirmasiUser         = ConfirmationUser::where('code', $result->code)->where('user_id',$user->id)->where('confirmation_type_id',1)->first();
        if (!$kodeKonfirmasiUser) {
            return $this->failure('Kode verifikasi salah.');
        }
        if ($kodeKonfirmasiUser->expired_at < Carbon::now()->toDateTimeString()) {
            return $this->failure('Kode verifikasi expired.');
        }
        if ($kodeKonfirmasiUser->expired_at<Carbon::now()->toDateTimeString()) {
            ConfirmationUser::where('code',$result->code)->delete();
            return $this->failure('Kode expired.');
        }
        ConfirmationUser::where('user_id',$user->id)->where('confirmation_type_id',1)->delete();
        $user->is_active              = 1;
        $user->save();
        DB::commit();
        $notifEmail                   =  new stdClass;
        $notifEmail->view             = 'new_account';
        $notifEmail->nama_nasabah     = $user->name;
        $notifEmail->receiver_email   = $user->email;
        $notifEmail->content          = 'Registrasi Pasien VirtHost Sukses';
        $notifEmail->subject          = 'Registrasi Pasien VirtHost Sukses';
        $notifEmail->sender_email     = 'noreply@virthost.id';
        $notifEmail->sender_name      = 'noreply VirtHost';
        $sendEmail                    = $this->email_helper->sendBeautyMail('new_account',$notifEmail);
        
        return redirect()->route('login')->with('success','Konfirmasi Pendaftaran Berhasil.');
    }

}
