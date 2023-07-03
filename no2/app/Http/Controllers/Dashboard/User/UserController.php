<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Helpers\WebHelperController;
use DataTables;
use DateTime;
use stdClass;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Role;
use App\Models\RoleUser;
use DB;
use App\Models\UserXRole;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserController extends Controller {
    
    public function __construct()
    {
        $this->route      ='dashboard.user.';
        $this->view       ='dashboard.user.';
        $this->web_helper =new WebHelperController();
        $this->sidebar    ='pengaturan'; 
    }
    public function index(Request $request){
        return view($this->view.'index')->with('sidebar', $this->sidebar);
    }

    public function data(Request $request){
        $user                   = User::all(); 
        $errors                 =[];
        $return                 = new stdClass;
        $return->response_code  = 200;
        $return->errors         = $errors;
        $return->data           = $user;
        return response()->json($return);

    }
    public function edit($id){
        $user       = User::query()
        ->where('id',$id)->first();
        $role = Role::query()->get();
        if(is_null($user)){
            return $this->web_helper->error404();
        }
        $myRole = DB::table('role_user')->where('user_id',$id)->get();
        $arrReturn=[
            'data' => $user,
            'role' => $role,
            'myRole'=>$myRole,
        ];
        return view($this->view.'edit',$arrReturn)->with('sidebar', $this->sidebar);
    }
    public function update(Request $request,$id){
       
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            
            'phone' => 'required|string|max:255|unique:users,phone,'.$id,
            
           
        ]);
        if(!isset($request->role)){
            return redirect()->back()
            ->withErrors('role', 'role harus diisi');
        }
        DB::table('role_user')->where('user_id',$id)->delete();
        $arrSetRole = $request->role;
        foreach ($arrSetRole as $key => $value) {
            UserXRole::create([
                'role_id' => $value,
                'user_id' => $id,
                'user_type' =>  'App\User',
            ]);
        } 
        $updateteUser=[
            'name'                  =>$request->name,
           
            'email'                 =>$request->email,
           
           
            'phone'                 =>$request->phone,
           
       
        ];
       
        $create = User::where('id',$id)->update($updateteUser);
        return redirect()->route($this->route.'index')
        ->with('success', 'Sukses mengedit user');
    }
    public function create(Request $request){
        $role = Role::query()->get();
 
        $arrReturn=[
            'role' => $role,
        ];
        return view($this->view.'create',$arrReturn)->with('sidebar', $this->sidebar);
    }
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
           
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if(!isset($request->role)){
            return redirect()->back()
            ->withErrors('role', 'role harus diisi');
        }
        
        $arrSetRole = $request->role;
        
        $createUser=[
            'name'                  =>$request->name,
           
            'email'                 =>$request->email,
            'password'              =>$request->password,
            
            'phone'                 =>$request->phone,
            
       
        ];
        $create = User::create($createUser);
        $idUser = $create->id;
        foreach ($arrSetRole as $key => $value) {
            UserXRole::create([
                'role_id' => $value,
                'user_id' => $idUser,
                'user_type' =>  'App\User',
            ]);
        } 
        return redirect()->route($this->route.'index')
        ->with('success', 'Sukses menambah user');
    }
    public function destroy(Request $request,$id){
        $deleteInstansi=User::where('id',$id)->delete();
        return redirect()->route($this->route.'index')
        ->with('success', 'Sukses menghapus user');
    }
    public function destroyJson(Request $request){
        $id                     = $request->id;
        $deleteInstansi         = User::where('id',$id)->delete();
        $errors                 = [];
        $data                   = new stdClass;
        $data->result           = $deleteInstansi;
        $data->id               = $id;
        $return                 = new stdClass;
        $return->response_code  = 200;
        $return->errors         = $errors;
        $return->data           = $data;
        return response()->json($return);
    }
    public function detail(Request $request,$id){
        $data           = User::find($id);
        $arrReturn      =[
            'data'=>$data,
        ];
        return view($this->view.'detail',$arrReturn)->with('sidebar', $this->sidebar);

    }

    public function detailJson(Request $request,$id){
        $data       = User::find($id);
        $errors     = [];
        $return     = new stdClass;
        if($data){
            $return->response_code  = 200;
            $return->errors         = $errors;
            $return->data           = $data;
            return response()->json($return);
        }
        else{
            $return->response_code  = 500;
            $errors['message']      = 'Data Not Found';
            $return->errors         = $errors;
            $return->data           = $data;
            return response()->json($return);
        }
        
    }

    public function chart($rentang_waktu,$rentang_value)
    {
        $query = Role::where('name','customer')->first();
        $query = $query->users();
        if($rentang_waktu =='bulanan'||$rentang_waktu == 'default'){
            $query = $query->selectRaw("count(*) as total,date(created_at) as tanggal");
            //bentuk rentang value bukan default = 'YYYYMM'
            if($rentang_value!='default'){
                $rentang_bulan = substr($rentang_value,-2,2);
                $rentang_tahun = substr($rentang_value,0,4);
            }
            else{
                $now = Carbon::now();
                $rentang_bulan = $now->month;
                $rentang_tahun = $now->year;
            }
            $query = $query->whereYear('created_at','=',$rentang_tahun)->whereMonth('created_at','=',$rentang_bulan);
            $query = $query->groupByRaw('date(created_at)');
            $data = $query->get();
            $result = $this->dataFormatDaily($data,$rentang_bulan,$rentang_tahun);
        }
        else{
            $query = $query->selectRaw("count(*) as total,month(created_at) as bulan");
            if($rentang_value=='default'){
                $now = Carbon::now();
                $rentang_value = $now->year;
            }
            $query = $query->whereYear('created_at','=',$rentang_value);
            $query = $query->groupByRaw('month(created_at)');
            $data = $query->get();
            $result = $this->dataFormatMonthly($data,$rentang_value);
        }
        $data = $query->get();
        $arrReturn = [
            'data' => $result,
        ];
        return response()->json($arrReturn);
    }
    public function summary()
    {
        $query = Role::where('name','customer')->first();
        $query = $query->users();
        $usersHariIni = clone $query;
        $usersBulanIni = clone $query;
        $usersTotal = clone $query;
        $usersHariIni = $usersHariIni->whereDate('created_at',Carbon::today())->count();
        $usersBulanIni = $usersBulanIni->whereMonth('created_at',Carbon::today()->month)->count();
        $usersTotal = $usersTotal->count();
        $arrReturn=[
            'summary_hari_ini' => $usersHariIni,
            'summary_bulan_ini' => $usersBulanIni,
            'summary_total' => $usersTotal,
        ];
        return response()->json($arrReturn);
    }


    public function dataFormatMonthly($data,$year)
    {
        $datasets   =[];
        $result     = [];
        for($i = 1;$i<=12;$i++){
            $datasets[$i] = 0;
        };
        foreach($data as $row){
            $datasets[$row->bulan] = $row->total;
        }
        $arrBulan = $this->getArrayBulan();
        foreach($datasets as $key => $value){
            array_push($result,[
                "bulan" => $arrBulan[$key],
                "total" => $value,
            ]);
        }
        return $result;
    }
    protected function dataFormatDaily($data,$month,$year)
    {
        $dayOnMonth = Carbon::parse($year.'-'.$month.'-01');
        $firstDay =clone $dayOnMonth;
        $lastDay =clone $dayOnMonth;
        $firstDay = $firstDay->startOfMonth();
        $lastDay = $lastDay->endOfMonth();
        $period = CarbonPeriod::create($firstDay,$lastDay);
        $datasets = [];
        $result = [];
        foreach($period as $key => $date){
            $datasets[$date->format('Y-m-d')] = 0;
        }
        foreach($data as $row){
            $datasets[$row->tanggal] = $row->total;
        }
        foreach($datasets as $key => $value){
            array_push($result,[
                'tanggal'=>$key,
                "total" => $value,
            ]);
        }
        return $result;
        // $firstDay = 
    }


    protected function getArrayBulan()
    {
        $arrBulan = [
            1   => "Januari",
            2   => "Februari",
            3   => "Maret",
            4   => "April",
            5   => "Mei",
            6   => "Juni",
            7   => "Juli",
            8   => "Agustus",
            9   => "September",
            10  => "Oktober",
            11  => "November",
            12  => "Desember",

        ];
        return $arrBulan; 

    }
    
}
