<?php

use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getData(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('app.rajaongkir_url')."/starter/city",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: ".config('app.rajaongkir_key')
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }
    public function run()
    {
        
        $datas = $this->getData();
        $this->command->info('disabling foreignkeys check');
        Schema::disableForeignKeyConstraints();
        $this->command->info('truncating master_city...');
        DB::table('master_city')->truncate();
        if($datas->rajaongkir->status->code == 200){
            foreach ($datas->rajaongkir->results as $key => $value) {
                $dateTime = new DateTime();
                $this->command->info('insert to master_city...');
                print_r($value);
                $insert = DB::table('master_city')->insert([
                    'id' => $value->city_id,
                    'provinsi_id' => $value->province_id,
                    'type'        => $value->type,
                    'name'=>$value->city_name,
                    'postal_code'=>$value->postal_code,
                    'created_at'=>$dateTime->format('Y-m-d H:i:s')
                ]);
                print_r($dateTime->format('Y-m-d H:i:s'));
                print_r($insert);
            }
            
        }
    }
}
