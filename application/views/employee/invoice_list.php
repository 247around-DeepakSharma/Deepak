<style>
    #overall_summary .col-md-3 {
        width: 25%;
    }
</style>

<div id="page-wrapper">
    <div class="container-fluid">
        <?php if(validation_errors()){?>
        
       
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
         <?php  if($this->session->flashdata('file_error')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                    </button>
                                <strong>' . $this->session->flashdata('file_error') . '</strong>
                               </div>';
                            }
                            ?>
        
        <?php if(isset($service_center)){ $vendor_partner = 'vendor';}else{ $vendor_partner = 'partner';}?>
        <?php if(!isset($is_ajax)){ ?>
            
        <a target="_blank" class="btn btn-lg btn-primary pull-right" style="margin-top:20px;" href="<?php echo base_url(); ?>employee/invoice/insert_update_invoice/<?php if (isset($service_center)) {
            echo 'vendor';
            } else {
            echo 'partner';
            } ?>">Create Invoice</a>
        <div class="row">
            <div class="col-md-6 ">
                <h1 class="page-header"><b><?php if (isset($service_center)) { ?>Service Center Invoices<?php } else { ?>
                    Partner Invoices
                    <?php } ?>
                    </b>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="state" class="col-sm-2">Select</label>
                <div class="form-group">
                    <div class="col-md-8">
                        <?php if (isset($service_center)) { ?>
                        <select class="form-control" name ="service_center" id="invoice_id" onChange="getInvoicingData('vendor')">
                            <option disabled selected >Service Center</option>
                            <?php
                                foreach ($service_center as $vendor) {
                                    ?>
                            <option value = "<?php echo $vendor['id'] ?>">
                                <?php echo $vendor['name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                        <select class="form-control" name ="partner" id="invoice_id" onChange="getInvoicingData('partner')">
                            <option disabled selected >Partner</option>
                            <?php
                                foreach ($partner as $partnerdetails) {
                                    ?>
                            <option value = "<?php echo $partnerdetails['id'] ?>">
                                <?php echo $partnerdetails['public_name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="col-sm-7">
                     <?php if(isset($service_center)){ $label = 'Select Service Center Type';}else{ $label = 'Select Partner Type' ;} ?>
                        <label for="sf_type" class="col-sm-5"><?php echo $label;?></label>
                        <div class="form-group col-sm-6">
                            <select class="form-control" id="sf_type" onchange="getVendor()">
                                <option value="1" selected>Active</option>
                                <option value="0">Disabled</option>
                                <option value="">All</option>
                            </select>
                        </div>
                </div>
                <div class="col-sm-5">
                    <?php if(isset($service_center)) { ?>
                    <div class="form-group col-sm-12">
                        <select class="form-control" id="sf_cp" onchange="getVendor()">
                            <option value='<?php echo json_encode(array("is_sf" => 1));?>' selected>Service Center</option>
                            <option value='<?php echo json_encode(array("is_cp" => 1));?>'>Collection Partner</option>
                        </select>
                    </div>
                    <?php } else{ ?>
                    <input type="hidden" id="sf_cp" value="<?php echo json_encode(array())?>" />
                   <?php } ?>
                </div>
            </div>
        </div>
        <?php }?>
        <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif" /></div>
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12 ">
                <div id="invoicing_table"></div>
            </div>
            <?php if (isset($invoicing_summary)) { ?>
            <div class="row" style="margin-top: 20px;" id="overall_summary">
                <?php if(isset($service_center)){ ?>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="col-md-4">
                                <div style="background-color: #eeff41;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                            </div>
                            <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;"> 
                                <span>Account Details Not verified</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-md-4">
                                <div style="background-color: #e57373;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                            </div>
                            <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                                <span >Vendor Temporary Off</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-md-4">
                                <div style="background-color: #f44336;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                            </div>
                            <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                                <span >Vendor Not Active</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-md-4">
                                <div style="background-color: #fff; border:1px solid #ccc;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                            </div>
                            <div class="col-md-8" style="margin-top:10px;margin-bottom: 10px;">
                                <span >Verified</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <h2>Invoices Overall Summary</h2>
                <form action="<?php echo base_url(); ?>employee/invoice/download_invoice_summary" method="POST" target="_blank">
                <table class="table table-bordered  table-hover table-striped data"  >
                    <thead>
                        <tr>
                            <th>No #</th>
                            <th class="text-center">Vendor/Partner</th>
                            <th class="text-center">Amount to be Paid</th>
                            <th class="text-center">Amount to be Received</th>
                            <th class="text-center">Pay</th>
                            <?php if (isset($service_center)) { ?>
                            <th class="text-center">Total Defective Spare Parts</th>
                            <th class="text-center">Download Summary  <input type="checkbox" id="selecctall_amt"/></th>
                            <?php } else { ?>
                            <th class="text-center">CRM Setup Invoice</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    
                        <tbody>
                            <?php $foc = 0;
                                $cash = 0; ?>
                            <?php $count = 1;
                                foreach ($invoicing_summary as $key => $value) { ?>
                            <tr class="text-center" style = "<?php if (isset($value['on_off'])) {
                                if ($value['active'] == 0) {
                                    echo 'background-color:#f44336 ;color:#333;';
                                } else if ($value['on_off'] == 0) {
                                    echo "background-color:#e57373;color:#333;";
                                }
                                }  if ($value['is_verified'] == 0) {
                                    echo "background-color: #eeff41;";
                                }
                                ?>">
                                <td><?php echo $count; if($value['is_stand']> 0) { ?>   <i class="fa fa-star" aria-hidden="true"></i> <?php } ?></td>
                                <td> <a style="<?php if (isset($value['on_off'])) {
                                    if ($value['active'] == 0) {
                                        echo 'background-color:#f44336;color:#333;';
                                    } else if ($value['on_off'] == 0) {
                                        echo "background-color:#e57373;color:#333;";
                                    }
                                    }
                                    ?>" href="<?php echo base_url() ?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner'] ?>/<?php echo $value['id'] ?>" target='_blank'><?php echo $value['name'] ?></a></td>
                                <td><?php if ($value['final_amount'] < 0) {
                                    echo round($value['final_amount'], 0);
                                    $foc +=abs(round($value['final_amount'], 0));
                                    } ?></td>
                                <td><?php if ($value['final_amount'] > 0) {
                                    echo round($value['final_amount'], 0);
                                    $cash +=abs(round($value['final_amount'], 0));
                                    } ?></td>
                                <td><?php if ($value['final_amount'] < 0) { ?> 
                                    <a href="<?php echo base_url() ?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner'] ?>/<?php echo $value['id'] ?>" target='_blank' class="btn btn-sm btn-success">Pay</a>
                                    <?php } ?>
                                </td>
                                <?php if (isset($service_center)) { ?>
                                <td><?php echo $value['count_spare_part']; ?></td>
                                
                                <td ><input type="checkbox" class="form-control <?php if (isset($value['on_off'])) {
                                        if ($value['active'] == 1 && $value['on_off'] == 1) {
                                            echo 'checkbox_amt';
                                        }
                                    }
                                    ?>" name="<?php echo "amount_service_center[" . $value['id'] . "]"; ?>" value ="<?php echo $value['final_amount']; ?>"  <?php if ($value['is_verified'] == 0) {
                                    echo "disabled";
                                    } ?>> </td>
                                
                                <?php } else { ?>
                                <td><a href="#myModel" id="<?php echo "invoice_setup_" . $value['id']; ?>" onclick="invoice_setup_model('<?php echo $value['id']; ?>','<?php echo $value["name"]; ?>', 
                                            '<?php echo $value['address']
                                        ." , ".$value['district'].", Pincode- ".$value['pincode'].", ".$value['state']; ?>', '<?php echo $value['state']; ?>', 
                                                    '<?php echo $value['seller_code']; ?>', '<?php echo $value['invoice_email_to']; ?>', '<?php echo $value['invoice_email_cc']; ?>')" class="btn btn-sm btn-primary text-center"
                                    data-toggle="modal" data-target="#myModal"  >CRM Setup Invoice</a></td>
                                <?php } ?>
                            </tr>
                            <?php $count++;
                                } ?>
                            <tr class="text-center">
                                <td>Total</td>
                                <td></td>
                                <td><?php echo -$foc; ?></td>
                                <td><?php echo $cash; ?></td>
                                <td></td>
                                <?php if (isset($service_center)) { ?>
                                <td></td>
                                <td class="text-center"><input type="submit" class="btn btn-md btn-primary"  value="Download"/></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                   
                </table>
                 </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CRM Setup Invoice</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="<?php echo base_url() . "employee/invoice/generate_crm_setup"; ?>" method="POST"  >
                    <input class="form-control" type="hidden" id="model_partner_id" name="partner_id" value="" />
                    <input class="form-control" type="hidden" id="model_seller_code" name="seller_code" value="" />
                     <input class="form-control" type="hidden" id="model_email_to" name="email_to" value="" />
                      <input class="form-control" type="hidden" id="model_email_cc" name="email_cc" value="" />
                    <input class="form-control" type="hidden" id="model_partner_name" name="partner_name" value=""/>
                    <input class="form-control" type="hidden" id="model_partner_address" name="partner_address" value=""/>
                    <input class="form-control" type="hidden" id="model_partner_state" name="partner_state" value=""/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Amount">Invoice Value:</label>
                                    <input type="text" class="form-control" style="width:92%" id="service_charge" name="service_charge" placeholder="Total Service Charge" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Agreement Date:</label>
                                    <div class="input-group input-append date">
                                        <input id="from_date" class="form-control" style="z-index: 1059; background-color:#fff;" name="from_date" type="date" required readonly='true'>
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Create Invoice" class="btn btn-md btn-primary col-md-offset-4"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_SESSION['file_error'])){
    unset($_SESSION['file_error']);
}
?>
<script type="text/javascript">
    $("#invoice_id").select2();
    
    function getInvoicingData(source) {
        $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
        var vendor_partner_id = $('#invoice_id').val();
        $('#overall_summary').css('display', 'none');
        $("#invoicing_table").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
            data: {vendor_partner_id: vendor_partner_id, source: source},
            success: function (data) {
                //console.log(data);
                $('#loader_gif').attr('src', '');
                $("#invoicing_table").html(data);
            }
        });
    }
    
    function delete_banktransaction(transactional_id) {
    
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/delete_banktransaction/' + transactional_id,
            success: function (data) {
                if (data === "success") {
                    getInvoicingData("vendor");
                }
    
            }
        });
    
    }
    
    function invoice_setup_model(partner_id, partner_name, partner_address, state, seller_code, email_to, email_cc) {

        $(".modal-title").html(partner_name + " CRM Setup Invoice");
        $("#model_partner_id").val(partner_id);
        $("#model_partner_name").val(partner_name);
        $("#model_partner_address").val(partner_address);
        $("#model_partner_state").val(state);
        $("#model_seller_code").val(seller_code);
        $("#model_email_to").val(email_to);
        $("#model_email_cc").val(email_cc);
    }
    $("#from_date").datepicker({dateFormat: 'yy-mm-dd'});
    
    
    function getVendor(){
        $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loadring.gif');
        var vendor_type = $('#sf_type').val();
        $("#invoicing_table").css('display', 'none');
        $('#overall_summary').css('display', 'none');
        var sf_cp = $('#sf_cp').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/invoice_listing_ajax/'+vendor_type,
            data:{'vendor_partner': '<?php echo $vendor_partner; ?>', 'sf_cp':sf_cp},
            success: function (data) {
                //console.log(data);
                $('#loader_gif').attr('src', '');
                $("#overall_summary").show();
                $("#overall_summary").html(data);
            }
        });
    }
    
</script>

<script type="text/javascript">
   $(document).ready(function(){
            $("#selecctall_amt").change(function(){
                       $(".checkbox_amt").prop('checked', $(this).prop("checked"));
                              });
               });
</script>
