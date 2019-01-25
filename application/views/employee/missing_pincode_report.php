<?php
$table = "";
$table .= '<table class="table table-bordered" style = "border: 1px solid #000;">';
            $table .= '<thead>';
            $table .=  '<tr>';
            $table .=  '<th scope="col" style = "border: 1px solid #000;">State</th>';
            foreach($serviceData as $services){
                  $table .=  '<th scope="col" style = "border: 1px solid #000;">'.$services['services'].'</th>';
            }
            $table .=  '</tr></thead><tbody>';
            foreach($rmPincodeDetails as $states => $pincodeData){
                $table .='<tr>';
                $table .= '<td style = "border: 1px solid #000;">'.$states.'</td>';
                foreach($serviceData as $services){
                    if(isset($pincodeData['services'][$services['services']])){
                        $table .= '<td style = "border: 1px solid #000;">'.$pincodeData['services'][$services['services']]['count'].'</td>';
                    }
                    else{
                        $table .= '<td style = "border: 1px solid #000;">0</td>';
                    }
                }
                $table .='</tr>'; 
            }
           echo $table;
           echo "</br>";
?>