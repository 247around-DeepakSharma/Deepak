<?php 
/*
$transction_date="12-10-2018";

$transction_amount =500;
$review='Hi,this is the testing text.';
 * 
 */
?>

<style>
/*table, th, tr {
    border: 1px solid black;
    border-collapse: collapse;
}
th, tr {
    padding: 5px;
    text-align: left;
    
    
}*/
</style> 

<table style="width:100%; border:1px solid #000;">
    <tr>
        <th colspan="3" style = "text-align:center; border-bottom: 1px solid #000; background:grey; "><span style="color: #fff;">SVC Load Request Form</span></th>
                    </tr>
                     <tr>
                         <th style="text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;">Client Name</th>
                        <th style="text-align:center; border-bottom: 1px solid #000;  border-right:1px solid #000;">Amazon_Ext_Buyback_247Around</th>
                        <th style="text-align:center; border-bottom: 1px solid #000;">Amazon SPOC Name</th>
                    </tr>
                   <?php if(!empty($transction_date))?>
                    <tr>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;">Date of filling up form</th>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;"><?php echo date('d-M-Y', strtotime($transction_date));?></th>
                         <th style = "text-align:center; border-bottom: 1px solid #000;">Order Delivery Date </th>
                    </tr>
                    <?php if(!empty($transction_amount))?>
                    <tr>
                        
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;">Amount(Rs.)</th>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;"><?php echo $transction_amount;?> </th>
                    </tr>
                    <tr>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;">Discount%</th>
                         <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;"></th>
                    </tr>
                    <?php if(!empty($transction_amount))?>
                    <tr>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;">Final Payable amount</th>
                        <th style = "text-align:center; border-bottom: 1px solid #000; border-right:1px solid #000;"><?php echo $transction_amount;?></th>
                        <th style = "text-align:center; border-bottom: 1px solid #000;"></th>
                    </tr
                    
                    <?php if(!empty($review))?> 
                    <tr>
                        <th style = "text-align:center; border-right:1px solid #000;">Special remarks for invoicing (if any)</th>
                        <th style = "text-align:center; border-right:1px solid #000;"><?php echo $review;?></th>
                    </tr>
 </table>





