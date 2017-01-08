<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">SERVICE CENTER PAY OUT (CALL CHARGES)</center></div>
        <div class="panel-body">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">S.No.</th>
                        <th class="jumbotron"style="text-align: center" >BOOKING CODE</th>
                        <th class="jumbotron"style="text-align: center" >PRODUCT</th>
                        <th class="jumbotron"style="text-align: center" >CATEGORY</th>
                        <th class="jumbotron"style="text-align: center" >CAPACITY</th>
                        <th class="jumbotron"style="text-align: center" >SERVICE CATEGORY</th>
                        <th class="jumbotron"style="text-align: center" >VENDOR BASIC CHARGEs</th>
                        <th class="jumbotron"style="text-align: center" >VENDOR TAX</th>
                        <th class="jumbotron"style="text-align: center" >VENDOR TOTAL</th>
                        <th class="jumbotron"style="text-align: center" >CUSTOMER NET PAYABLE</th>
                        <th class="jumbotron"style="text-align: center" >PROOF OF DELIVERY</th>
                        
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($final_array as $key=>$value){?>		
                    <tr>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td style="text-align: center;"><?php echo $value['sc_code']?></td>
                            <td style="text-align: center;"><?php echo $value['product']?></td>
                            <td style="text-align: center;"><?php echo $value['category']?></td>
                            <td style="text-align: center;"><?php echo $value['capacity']?></td>
                            <td style="text-align: center;"><?php echo $value['service_category']?></td>
                            <td style="text-align: center;"><?php echo $value['vendor_basic_charges']?></td>
                            <td style="text-align: center;"><?php echo $value['vendor_tax_basic_charges']?></td>
                            <td style="text-align: center;"><?php echo $value['vendor_total']?></td>
                            <td style="text-align: center;"><?php echo $value['customer_net_payable']?></td>
                            <td style="text-align: center;"><?php echo $value['pod']?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>