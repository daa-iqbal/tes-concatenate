<?php

class BageConcat {

  public $datas = [];
  

  // Methods
  function setDatas($datas) {
    $this->datas = $datas;
  }
  function getResult(){
    $countBageConcat = 0;
    $datas = $this->datas;
    foreach ($datas as $key => $value) {
        if($countBageConcat == 5){
            break;
        }
        elseif($countBageConcat >= 2){
           if($value % 15 == 0){
                $countBageConcat++;
                echo "Bage Concat"."\n";
            }
            elseif($value % 5 == 0){
                echo "Bage"."\n";
            }
            elseif($value % 3 == 0){
                echo "Concat"."\n";
            }
            else{
                echo ""."\n";
            } 
        }
        elseif($countBageConcat < 2){
            if($value % 15 == 0){
                $countBageConcat++;
                echo "Bage Concat"."\n";
            }
            elseif($value % 5 == 0){
                echo "Concat"."\n";
            }
            elseif($value % 3 == 0){
                echo "Bage"."\n";
            }
            else{
                echo ""."\n";
            }
            
        }
        
    }
  }
  
}
