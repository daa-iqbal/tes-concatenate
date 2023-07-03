<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ActivityController extends Controller
{
    //
    public function data($rentang_waktu,$rentang_value)
    {
        $query = ActivityRecord::query();
        $query = $query->whereNotIn('from_ip',['localhost']);
        $query = $query->groupByRaw('from_ip');
        if($rentang_waktu =='bulanan'||$rentang_waktu == 'default'){
            $query = $query->selectRaw("from_ip as ip,date(created_at) as tanggal");
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
            $rawData = $query->get();
            $data = $this->getActiveUserDaily($rawData);
            $result = $this->dataFormatDaily($data,$rentang_bulan,$rentang_tahun);
        }
        else{
            $query = $query->selectRaw("from_ip as ip,month(created_at) as bulan");
            if($rentang_value=='default'){
                $now = Carbon::now();
                $rentang_value = $now->year;
            }
            $query = $query->whereYear('created_at','=',$rentang_value);
            $query = $query->groupByRaw('month(created_at)');
            $rawData = $query->get();
            $data = $this->getActiveUserMonthly($rawData);
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
        $query          = ActivityRecord::query();
        $query          = $query->whereNotIn('from_ip',['localhost']);
        $query          = $query->groupBy('from_ip');
        
        $queryDaily     = clone $query;
        $queryMonthly   = clone $query;

        $queryDaily     = $queryDaily->groupByRaw('date(created_at)');
        $queryMonthly   = $queryMonthly->groupByRaw('month(created_at)');

        $queryDaily     = $queryDaily->selectRaw('from_ip as ip, date(created_at) as tanggal');
        $queryMonthly   = $queryMonthly->selectRaw('from_ip as ip, month(created_at) as bulan');

        $aktivitasHariIni   = clone $queryDaily;
        $aktivitasBulanIni  = clone $queryMonthly;
        $aktivitasBulanLalu = clone $queryMonthly;

        $aktivitasHariIni   = $aktivitasHariIni->whereDate('created_at',Carbon::today())->get();
        $aktivitasBulanIni  = $aktivitasBulanIni->whereMonth('created_at',Carbon::today()->month)->get();
        $aktivitasBulanLalu = $aktivitasBulanLalu->whereMonth('created_at',Carbon::today()->month-1)->get();

        $aktivitasHariIni   = $this->getActiveUserDaily($aktivitasHariIni);
        $aktivitasBulanIni  = $this->getActiveUserMonthly($aktivitasBulanIni);
        $aktivitasBulanLalu = $this->getActiveUserMonthly($aktivitasBulanLalu);

        $arrReturn=[
            'summary_hari_ini'  => $aktivitasHariIni[0]->total,
            'summary_bulan_ini' => $aktivitasBulanIni[0]->total,
            'summary_bulan_lalu'     => $aktivitasBulanLalu[0]->total,
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
    

    public function getActiveUserDaily($data)
    {
        $parser = clone $data;
        $parser = $parser->groupBy('tanggal');
        $result = [];
        foreach($parser as $key => $row)
        {
            $resultRow = [
                "tanggal"   => $key,
                "total"     => $row->count(),
            ];
            $resultRow = (object) $resultRow;
            array_push($result,$resultRow);
        }

        return $result;
    }

    public function getActiveUserMonthly($data)
    {
        $parser = clone $data;
        $parser = $parser->groupBy('bulan');
        $result = [];
        foreach($parser as $key => $row)
        {
            $resultRow = [
                "bulan"     => $key,
                "total"     => $row->count(),
            ];
            $resultRow = (object) $resultRow;
            array_push($result,$resultRow);
        }

        return $result;
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
