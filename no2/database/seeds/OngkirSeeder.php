<?php

use Illuminate\Database\Seeder;


class OngkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function getData($params){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('app.rajaongkir_url')."/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=".$params->origin."&"."destination=".$params->destination."&"."weight=".$params->weight."&"."courier=".$params->courier,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
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
        $getOriginYogya = DB::table('master_city')->whereRaw('LOWER(name) = ?',['yogyakarta'])->first();
        $getAllDestination = DB::table('master_city')->get();
        $weight = 1;
        $getAllCourier = DB::table('master_ekspedisi')->get();
        
        if($getOriginYogya){
            $this->command->info('disabling foreignkeys check');
            Schema::disableForeignKeyConstraints();
            $this->command->info('truncating ongkir...');
            DB::table('ongkir')->truncate();
            foreach ($getAllDestination as $key => $value) {
                foreach ($getAllCourier as $keyChild => $valueChild) {
                    $params = NULL;
                    $params = new stdClass();
                    $params->origin = $getOriginYogya->id;
                    $params->destination = $value->id;
                    $params->weight = $weight;
                    $params->courier = $valueChild->kode;
                    $datas = NULL;
                    $datas = $this->getData($params);
                    
                    if($datas->rajaongkir->status->code == 200){
                        foreach ($datas->rajaongkir->results as $keyResult => $valueResult) {
                            
                            
                            print_r($valueResult);
                            foreach ($valueResult->costs as $keyCost => $valueCost) {
                                foreach ($valueCost->cost as $keyChildCost => $valueChildCost) {
                                    $this->command->info('insert to ongkir...');
                                    $dateTime = new DateTime();
                                    $insert = DB::table('ongkir')->insert([
                                        'origin_id' => $datas->rajaongkir->origin_details->city_id,
                                        'destination_id' => $datas->rajaongkir->destination_details->city_id,
                                        'courier_id'=> $valueChild->id,
                                        'weight'=> $datas->rajaongkir->query->weight,
                                        'service' => $valueCost->service,
                                        'description'=> $valueCost->description,
                                        'cost_value'=>$valueChildCost->value,
                                        'cost_etd'=>$valueChildCost->etd,
                                        // /'cost_note'=> '',
                                        'created_at'=>$dateTime->format('Y-m-d H:i:s')
                                    ]);
                                    print_r($dateTime->format('Y-m-d H:i:s'));
                                    print_r($insert);
                                }
                                
                            }
                            
                            
                        }
                        
                    }
                }
            }
        }
        
        
        
    }
}
