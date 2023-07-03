<?php

use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getData(){
        $datas = [

            [
                 "kode" => "jne",
                 "name" => "Jalur Nugraha Ekakurir (JNE)",
            ],
            [
                 "kode" => "pos",
                 "name" => "POS Indonesia (POS)",
            ],
            [
                 "kode" => "tiki",
                 "name" => "Citra Van Titipan Kilat (TIKI)",
            ],

        ];
        return $datas;
    }
    public function run()
    {
        
        $datas = $this->getData();
        $this->command->info('disabling foreignkeys check');
        Schema::disableForeignKeyConstraints();
        $this->command->info('truncating master_ekspedisi...');
        DB::table('master_ekspedisi')->truncate();
        
        foreach ($datas as $key => $value) {
            $dateTime = new DateTime();
            $this->command->info('insert to master_ekspedisi...');
            print_r($value);
            $value["created_at"] = $dateTime->format('Y-m-d H:i:s'); 
            $insert = DB::table('master_ekspedisi')->insert($value);
            print_r($dateTime->format('Y-m-d H:i:s'));
            print_r($insert);
        }
            
        
    }
}
