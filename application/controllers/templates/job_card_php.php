<?php
set_time_limit(0);
include 'job_card_html.php';
require_once 'pdf/vendor/autoload.php';


        	$mpdf=new \Mpdf\Mpdf();
            $mpdf->setFooter('BlackMelon Advance Technologies Pvt Ltd');
            $mpdf->WriteHTML($html);

            if($mpdf->Output('c:\xampp\htdocs\internship\pdfs\job_card.pdf', 'F'))
            	echo $i;
            echo "<br>";
            $mpdf->Output();
            flush();
            /*sleep(1);
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];		//	$_SERVER["REQUEST_TIME_FLOAT"]; returns timestamp when process was started
            echo "Process Time: {$time} s";  		// no need of {}*/
            // if(!($mpdf->Output('c:\xampp\htdocs\internship\pdfs\invoice'.$i.'.pdf', 'F')))
            // {
            //     echo $i."error";
            // }
            

sleep(1);
                $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];		//	$_SERVER["REQUEST_TIME_FLOAT"]; returns timestamp when process was started
                echo "Process Time: {$time} s";  
  
?>
