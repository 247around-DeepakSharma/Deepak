<?php
$table = "";
$table .= '<table class="table table-bordered" style = "border: 1px solid #000;">';
            $table .= '<thead>';
            $table .=  '<tr>';
            $table .=  '<th scope="col" style = "border: 1px solid #000;">State</th>';
            foreach($service_arr as $servicekey=>$servicevalue){
                  $table .=  '<th scope="col" style = "border: 1px solid #000;" colspan="2">'.$servicevalue.'</th>';
            }
            $table .=  '</tr></thead><tbody>';
            foreach($rm_arr as $key=>$value){
                foreach($value as $rmstateid)
                {
                        if(isset($state_arr[$rmstateid]))
                        {
                                $table .='<tr>';
                                $table .= '<td style = "border: 1px solid #000;">'.$state_arr[$rmstateid].'</td>';
                                foreach($service_arr as $servicekey=>$servicevalue){

                                   if(isset($vendorStructuredArray['state_'.$rmstateid]['appliance_'.$servicekey])){
                                        $result=$vendorStructuredArray['state_'.$rmstateid]['appliance_'.$servicekey];
                                        $percent=round(($result['missing_pincode_per']*100),2);
                                        $table .= '<td style = "border: 1px solid #000;">'.$result['missing_pincode'].'</td>';
                                        $table .= '<td style = "border: 1px solid #000;">'.$percent.'%</td>';
                                    }
                                    else{
                                        $table .= '<td style = "border: 1px solid #000;">0</td>';
                                        $table .= '<td style = "border: 1px solid #000;">0%</td>';
                                    }
                                }
                        }
                }
                $table .='</tr>'; 
            }
           echo $table;
           echo "</br>";
?>