<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;"><b>SF Documents List</b></center></div>
        
        <div class="panel-body">
                <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">SF NAME</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">PAN</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CST</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">TIN</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">SERVICE TAX</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ID PROOF 1</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ID PROOF 2</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CONTRACT</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CANCELLED CHEQUE</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ADDRESS PROOF</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($data as $key=>$value){ ?>		
                    <tr>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td ><?php echo $value['name']?></td>
                            <td style="text-align: center">
                                <?php if(!empty($value['pan_file']) && $value['is_pan_doc'] == 1){ ?>
                                <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['pan_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['cst_file']) && $value['is_cst_doc'] == 1){ ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['cst_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['tin_file']) && $value['is_tin_doc'] == 1){ ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['tin_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['service_tax_file']) && $value['is_st_doc'] == 1){ ?>
                                   <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['service_tax_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                                
                                </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['id_proof_1_file'])){?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['id_proof_1_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['id_proof_2_file'])) {?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['id_proof_2_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['contract_file'])){?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['contract_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['cancelled_cheque_file'])) {?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['cancelled_cheque_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>   
                            </td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['address_proof_file'])) {?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['address_proof_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            
                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

        </div>
    </div>
</div>