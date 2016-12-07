<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;"><b>SF Documents List</b></center></div>
        <div class='col-md-12'>
        <div style='border-radius: 5px;background: #EEEEEE;margin-top: 10px;margin-bottom: 10px;width:330px;' class='col-md-6'><b>NOTE:</b> <i>Click on checkmarks to view documents.</i></div>
        <div class='col-md-6' style='margin-top:10px;margin-left:250px;'>
        <form name="myForm" class="form-horizontal" id ="documents_form" action="<?php echo base_url();?>employee/vendor/show_vendor_documents_view"  method="POST">
            
            <div class='col-md-2 form-group'>
            <select name='all_active' id='all_vendor'>
                <option value='all'>ALL</option>
                <option value='active'>ACTIVE</option>
            </select>
            </div>
            <div class='col-md-4 form-group'>
            <select name='rm' id='rm'>
                <option value='all'>ALL</option>
                <?php foreach($rm as $value){?>
                    <option value='<?php echo $value['id']?>'><?php echo $value['full_name']?></option>
                <?php }?>
            </select>
            </div>
            <div class='col-md-2'>
            <input type='submit' value="Filter" class='btn btn-primary'>
            </div>
        </form>
        </div>
        </div>
        <div class="panel-body">
                <table class="table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">SF NAME</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CONTRACT</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">PAN</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CST</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">TIN</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">SERVICE TAX</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ID PROOF 1</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ID PROOF 2</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CANCELLED CHEQUE</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">ADDRESS PROOF</th>
                    </tr>
                </thead>
                <tbody>

                    <?php 
                    $pan =0;
                    $cst =0;
                    $contract =0;
                    $tin =0;
                    $service_tax =0;
                    $id_1 =0;
                    $id_2 =0;
                    $cancelled_cheque =0;
                    $address_proof =0;
                    foreach($data as $key=>$value){ ?>		
                    <tr>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td ><?php echo $value['name']?></td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['contract_file'])){
                                    $contract++; ?>;
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['contract_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['pan_file']) && $value['is_pan_doc'] == 1){
                                    $pan++;?>
                                <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['pan_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['cst_file']) && $value['is_cst_doc'] == 1){ 
                                    $cst++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['cst_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['tin_file']) && $value['is_tin_doc'] == 1){ 
                                    $tin++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['tin_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['service_tax_file']) && $value['is_st_doc'] == 1){
                                    $service_tax++; ?>
                                   <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['service_tax_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }
                                ?>
                                
                                </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['id_proof_1_file'])){
                                    $id_1++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['id_proof_1_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php if(!empty($value['id_proof_2_file'])) {
                                    $id_2++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['id_proof_2_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['cancelled_cheque_file'])) {
                                    $cancelled_cheque++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['cancelled_cheque_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>   
                            </td>
                            <td style="text-align: center">
                                <?php  if(!empty($value['address_proof_file'])) {
                                    $address_proof++; ?>
                                    <a href='https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/<?php echo $value['address_proof_file']?>' target="_blank"><img src="<?php echo base_url()?>images/ok.png" width="20" height="20"/></a>
                                <?php }?>
                            </td>
                            
                        </tr>
                    <?php } ?>
                        <tr style='background: #FF9900'>
                            <td></td>
                            <td></td>
                            <td><?php echo $contract?></td>
                            <td><?php echo $pan?></td>
                            <td><?php echo $cst?></td>
                            <td><?php echo $tin?></td>
                            <td><?php echo $service_tax?></td>
                            <td><?php echo $id_1?></td>
                            <td><?php echo $id_2?></td>
                            <td><?php echo $cancelled_cheque?></td>
                            <td><?php echo $address_proof?></td>
                        </tr>
                    </tbody>
            </table>

        </div>
    </div>
</div>
<script type='text/javascript'>
    $('#all_vendor').select2();
    $('#rm').select2();
    </script>