<?php

namespace App\Exports;

use App\Models\Nasabah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use DB;

class NasabahsExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	// $array = ['jenis_layanan'=>function($query){
    	// 	$query->select('id','display_name');
    	// }];
    	$nasabahs = Nasabah::all();    
        $nasabahs->load(['kelurahan','kecamatan','kota','provinsi','jenis_layanan','user']);
        $results    = [];
        foreach($nasabahs as $nasabah){

            $in_cash = $nasabah->duma_cash->sum('in');
            $out_cash = $nasabah->duma_cash->sum('out');
            $nasabah->total_cash = $in_cash - $out_cash;

            $total_point = 0;
            $in_point = $nasabah->user->duma_point->sum('in');
                
            $out_point = $nasabah->user->duma_point->sum('out');

            $total_point += $in_point - $out_point;            
            $nasabah->total_point = $total_point;    

            if($nasabah->nama               == null){
                $nasabah->nama_nasabah      = '-';
            }
            if($nasabah->nomor_va           == null){
                $nasabah->nomor_va          = '-';
            }
            if(!empty($nasabah->user_referral->referrer->number_id)){
                $nasabah->kode_referral    = $nasabah->user_referral->referrer->number_id;                
            }else{
                $nasabah->kode_referral     = '-';            
            }            
            if($nasabah->jenis_layanan      != null){
                $nasabah->jenis_layanan    = $nasabah->jenis_layanan->display_name;                
            }else{
                $nasabah->jenis_layanan     = '-';            
            }            
            if($nasabah->kelurahan     != null){                
                $nasabah->nama_kelurahan   = $nasabah->kelurahan->nama;                                       
            }else{
                $nasabah->nama_kelurahan    = '-';    
            }
            if($nasabah->kecamatan     != null){
                $nasabah->nama_kecamatan   = $nasabah->kecamatan->nama;                
            }else{
                $nasabah->nama_kecamatan    = '-';    
            }
            if($nasabah->kota          != null){
                $nasabah->nama_kota        = $nasabah->kota->nama;                
            }else{
                $nasabah->nama_kota         = '-';    
            }
            if($nasabah->provinsi      != null){
                $nasabah->nama_provinsi    = $nasabah->provinsi->nama;                 
            }else{
                $nasabah->nama_provinsi     = '-';    
            }
            if($nasabah->tanggal_lahir      == null){
                $nasabah->tanggal_lahir     = '-';
            }
            if($nasabah->jenis_kelamin      == null){
                $nasabah->jenis_kelamin     = '-';    
            }
            if($nasabah->nomor_hp           == null){
                $nasabah->nomor_hp          = '-';    
            }
            if($nasabah->email              == null){
                $nasabah->email             = '-';    
            }
            if($nasabah->alamat             == null){
                $nasabah->alamat            = '-';
            }
            if($nasabah->nomor_ktp          ==  null){
                $nasabah->nomor_ktp         = '-';    
            }        

            array_push($results,[
                'Nama'          => $nasabah->nama,
                'Nomor VA'      => $nasabah->nomor_va,
                'Jenis Layanan' => $nasabah->jenis_layanan,
                'Kode Referral' => $nasabah->kode_referral,
                'Duma Cash'     => $nasabah->total_cash,
                'Duma Point'    => $nasabah->total_point,
                'Kelurahan'     => $nasabah->nama_kelurahan,
                'Kecamatan'     => $nasabah->nama_kecamatan,
                'Kota'          => $nasabah->nama_kota,
                'Provinsi'      => $nasabah->nama_provinsi,
                'Tanggal Lahir' => $nasabah->tanggal_lahir,
                'Jenis Kelamin' => $nasabah->jenis_kelamin,
                'Nomor HP'      => $nasabah->nomor_hp,
                'Email'         => $nasabah->email,
                'Alamat'        => $nasabah->alamat,
                'Nomor KTP'     => $nasabah->nomor_ktp,
            ]);
        }
        $result = collect($results);
        
    	return $result;

        // return Nasabah::with($array)->select('nama','nomor_va','jenis_layanan.display_name')->get();
        
    }

    public function headings():array
    {
        return ["Nama","Nomor VA","Jenis Layanan","Kode Referral","Duma Cash","Duma Point","Kelurahan","Kecamatan","Kota","Provinsi","Tanggal Lahir","Jenis Kelamin", "Nomor HP","Email","Alamat","Nomor KTP"];
    }

     public function styles(Worksheet $sheet)
    {	
    	 $sheet->getStyle('A1:P1') ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    	 $sheet->getStyle('A1:P1')->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE ) );

    	 $sheet->getStyle('A1:P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF8C00');;



    	 $sheet->getColumnDimension('A')->setWidth(30);
    	 $sheet->getColumnDimension('B')->setWidth(30);
    	 $sheet->getColumnDimension('C')->setWidth(20);
    	 $sheet->getColumnDimension('D')->setWidth(30);
    	 $sheet->getColumnDimension('E')->setWidth(30);
    	 $sheet->getColumnDimension('F')->setWidth(30);
    	 $sheet->getColumnDimension('G')->setWidth(30);
    	 $sheet->getColumnDimension('H')->setWidth(30);
    	 $sheet->getColumnDimension('I')->setWidth(30);
    	 $sheet->getColumnDimension('J')->setWidth(30);
    	 $sheet->getColumnDimension('K')->setWidth(30);
    	 $sheet->getColumnDimension('L')->setWidth(30);
    	 $sheet->getColumnDimension('M')->setWidth(30);
         $sheet->getColumnDimension('N')->setWidth(30);
         $sheet->getColumnDimension('O')->setWidth(30);
         $sheet->getColumnDimension('P')->setWidth(30);
         
    	 
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],            

        ];

    }


    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class    => function(AfterSheet $event) {
    //             $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
    //         },
    //     ];
    // }

    //  public function columnWidths(): array
    // {
    //     return [
    //         'A' => 55,
    //         'B' => 45,            
    //     ];
    // }
}
