<?php ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000000);
?>
<style>
    #overall_summary .col-md-3 {
        width: 25%;
    }
/*    #wrapper{
        overflow-x: hidden;
    }*/
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
                                echo '<div class="alert alert-warning alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                    </button>
                                <strong>' . $this->session->flashdata('file_error') . '</strong>
                               </div>';
                            }
                            ?>
        
        <?php if(isset($service_center)){ $vendor_partner = 'vendor';}else{ $vendor_partner = 'partner';}?>
        <?php if(!isset($is_ajax)){ ?>
            
        
        <div class="row">
            <div class="col-md-12 ">
                <h1 class="page-header"><b><?php if (isset($service_center)) { ?>Service Center Invoices<?php } else { ?>
                    Partner Invoices
                    <?php } ?>
                    </b>
                    <a target="_blank" class="btn btn-lg btn-primary pull-right" href="<?php echo base_url(); ?>employee/invoice/insert_update_invoice/<?php if (isset($service_center)) {
                        echo 'vendor';
                        } else {
                        echo 'partner';
                        } ?>">Create Invoice</a>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label for="state" class="col-md-12 col-sm-12">
                    <?php if (isset($service_center)) { 
                        echo "Select Service Center"; 
                    }
                    else {
                        echo "Select Partner";
                    }
                    ?>
                </label>
                <div class="form-group">
                    <div class="col-md-12">
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
            </div>
            <hr>
            <div class="row">
                <?php if(isset($service_center)){ ?>
                    <div class="col-md-4">
                        <label for="due_date" class="col-md-12 col-sm-12">Select Due Date</label>
                        <div class="form-group col-md-12 col-sm-12">
                            <input placeholder="Select Due Date" type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" name="due_date" id="due_date" onchange="getVendor()" />
                        </div>
                    </div>
                <?php } else{ ?>  <input placeholder="Select Due Date" type="hidden" class="form-control" name="due_date" id="due_date" /> <?php }?>
                <div class="col-md-4">
                     <?php if(isset($service_center)){ $label = 'Select Service Center Type';}else{ $label = 'Select Partner Status' ;} ?>
                        <label for="sf_type" class="col-md-12 col-sm-12"><?php echo $label;?></label>
                        <div class="form-group col-md-12 col-sm-12">
                            <select class="form-control" id="sf_type" onchange="getVendor()">
                                <option value="1" selected>Active</option>
                                <option value="0">Disabled</option>
                                <option value="">All</option>
                            </select>
                        </div>
                </div>
                <div class="col-md-4">
                    <?php if(isset($service_center)) { ?>
                    <label for="sf_cp" class="col-md-12 col-sm-12">Select SF/CP</label>
                    <div class="form-group col-md-12 col-sm-12">
                        <select class="form-control" id="sf_cp" onchange="getVendor()">
                            <option value='<?php echo json_encode(array("is_sf" => 1));?>' selected>Service Center</option>
                            <option value='<?php echo json_encode(array("is_cp" => 1));?>'>Collection Partner</option>
                        </select>
                        <input type="hidden" name="partnerType" id="partner_sc" value="<?php print_r(array()); ?>">
                    </div>
                    <?php } else{ ?>
                    <input type="hidden" id="sf_cp" value="<?php echo json_encode(array())?>" />
                    <div class="form-group">
                        <label for="Service Code">Select Partner Type</label>
                        <select class="form-control filter_table" id="partner_sc" name="partnerType[]" onchange="getVendor()" multiple="multiple" placeholder="All">
                            <option value="<?php echo OEM; ?>" <?php if (in_array(OEM, $partnerType)) { echo "selected"; } ?>><?php echo OEM ?></option>
                            <option value="<?php echo EXTWARRANTYPROVIDERTYPE; ?>" <?php if (in_array(EXTWARRANTYPROVIDERTYPE, $partnerType)) { echo "selected"; } ?>><?php echo EXTWARRANTYPROVIDERTYPE ?></option>
                            <option value="<?php echo BUYBACKTYPE; ?>" <?php if (in_array(BUYBACKTYPE, $partnerType)) {
                                echo "selected";
                            } ?>><?php echo BUYBACKTYPE ?></option>
                            <option value="<?php echo INTERNALTYPE; ?>" <?php if (in_array(INTERNALTYPE, $partnerType)) {
                                echo "selected";
                            } ?>><?php echo INTERNALTYPE ?></option>
                            <option value="<?php echo ECOMMERCETYPE; ?>" <?php if (in_array(ECOMMERCETYPE, $partnerType)) {
                                echo "selected";
                            } ?>><?php echo ECOMMERCETYPE ?></option>
                        </select>
                    </div>
                   <?php } ?>
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
                    <input type="hidden" id="dowmload_excel_due_date" name="dowmload_excel_due_date" value="<?php echo date('Y-m-d'); ?>">
                <table class="table table-bordered  table-hover table-striped data"  >
                    <thead>
                        <tr>
                            <th>No #</th>
                            <th class="text-center">Partner</th>
                            <?php if(isset($invoicing_summary[0]['prepaid_data'])){ ?>
                            <th class="text-center">Prepaid Amount</th>
                            <?php } ?>
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
<!--                                if($value['is_stand']> 0) { ?>   <i class="fa fa-star" aria-hidden="true"></i>-->
                                <td><?php echo $count;   ?></td>
                                
                                <td> <a style="<?php if (isset($value['on_off'])) {
                                    if ($value['active'] == 0) {
                                        echo 'background-color:#f44336;color:#333;';
                                    } else if ($value['on_off'] == 0) {
                                        echo "background-color:#e57373;color:#333;";
                                    }
                                    }
                                    ?>" href="<?php echo base_url() ?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner'] ?>/<?php echo $value['id'] ?>" target='_blank'><?php echo $value['name'] ?></a></td>
                                <?php if(isset($value['prepaid_data']['prepaid_amount'])) { ?>
                                <td><span style="<?php if($value['prepaid_data']['prepaid_amount']> 0){ echo "color:green";} else { echo "color:red";}?>"><?php echo $value['prepaid_data']['prepaid_amount']; ?></span> </td>
                                <?php } ?>
                                <td><?php if ($value['final_amount'] < 0) {
                                    echo sprintf("%.2f",$value['final_amount']);
                                    $foc +=abs(sprintf("%.2f",$value['final_amount']));
                                    } ?></td>
                                <td><?php if ($value['final_amount'] > 0) {
                                    echo sprintf("%.2f",$value['final_amount']);
                                    $cash +=abs(sprintf("%.2f",$value['final_amount']));
                                    } ?></td>
                                <td><?php if ($value['final_amount'] < 0) { ?> 
                                    <a href="<?php echo base_url() ?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner'] ?>/<?php echo $value['id'] ?>" target='_blank' class="btn btn-sm btn-success">Pay</a>
                                    <?php } ?>
                                </td>
                                <?php if (isset($service_center)) { ?>
                                <td><?php if($value['count_spare_part'] > 0){ ?>
                                    <a href="javascript:void(0)" onclick="get_defective_spare_count(<?php echo $value['id']; ?>)">  <?php print_r($value['count_spare_part']);?> </a>
                                <?php } else { echo "0"; } ?>
                                </td>
                                
                                <td ><input type="checkbox" class="form-control checkbox_amt <?php //if (isset($value['on_off'])) {
                                        //if ($value['active'] == 1 && $value['on_off'] == 1) {
                                            //echo 'checkbox_amt';
                                       // }
                                    //}
                                    ?>" name="<?php echo "amount_service_center[" . $value['id']."_".$value["count_spare_part"]."_".$value["max_sp_age"] . "]"; ?>" value ='<?php echo json_encode(array("amount" => $value['final_amount'], "parts_name" => str_replace("'","",$value['shipped_parts']), "challan_value" => $value['challan_value'])); ?>' > </td>
                                
                                <?php } else { if($value['id'] !== CLOUDTAIL_LA) { $BUTTON_TEXT = PARTNER_INVOICE_BUTTON; $CRM_SETUP = CRM_SETUP_INVOICE_DESCRIPTION;} else { $BUTTON_TEXT = CT_INVOICE_BUTTON; $CRM_SETUP = QC_INVOICE_DESCRIPTION;}  ?>
                                <td>
                                    <a href="#myModel" id="<?php echo "invoice_setup_" . $value['id']; ?>" onclick="invoice_setup_model('<?php echo $value['id']; ?>','<?php echo $value["name"]; ?>','<?php echo $CRM_SETUP; ?>' )" class="btn btn-sm btn-primary text-center"
                                    data-toggle="modal" data-target="#myModal"  ><?php echo $BUTTON_TEXT;?></a>
                                    <?php
                                    if($CRM_SETUP == CRM_SETUP_INVOICE_DESCRIPTION){
                                    ?>
                                       <a href="#proFormaModal" id="" onclick="proforma_invoice_model('<?php echo $value['id']; ?>','<?php echo $value["name"]; ?>','<?php echo CRM_SETUP_PROFORMA_INVOICE_DESCRIPTION; ?>' )" class="btn btn-sm btn-warning text-center" data-toggle="modal" data-target="#proFormaModal">Proforma Invoice</a>     
                                    <?php 
                                    }
                                    ?>
                                </td>
                                <?php } ?>
                            </tr>
                            <?php $count++;
                                } ?>
                            <tr class="text-center">
                                <td>Total</td>
                                <td></td>
                                <?php if(isset($invoicing_summary[0]['prepaid_data'])){ ?>
                                <td></td>
                                <?php } ?>
                                <td><?php echo -sprintf("%.2f", $foc); ?></td>
                                <td><?php echo sprintf("%.2f", $cash); ?></td>
                                <td></td>
                                <?php if (isset($service_center)) { ?>
                                <td></td>
                                <td class="text-center"><input type="submit" class="btn btn-md btn-primary"  value="Download"/></td>
                                <?php }else{ ?> <td></td> <?php } ?>
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
                    <input class="form-control" type="hidden" id="model_partner_name" name="partner_name" value=""/>
                    <input class="form-control" type="hidden" id="model_invoice_type" name="invoice_type" value=""/>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Amount">Invoice Value<small> (with GST) </small>:</label>
                                    <input type="number" step=".02" class="form-control" style="width:92%" id="service_charge" name="service_charge" placeholder="Total Invoice Amount" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" id="agreement_date">Agreement Date:</label>
                                    <div class="input-group input-append date">
                                        <input id="from_date" class="form-control" style="z-index: 1059; background-color:#fff;" name="daterange" type="text" required readonly='true'>
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

<!--Invoice Defective Part Pending Booking with Age Modal-->
    <div id="defective_part_pending_booking_age" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <table class="table table-bordered  table-hover table-striped data">
                      <thead>
                        <th>SN</th>
                        <th>Booking ID</th>
                        <th>Shipped Part Type</th>
                        <th>Pending Age</th>
                        <th>Challan Approx Value</th>
                      </thead>
                      <tbody id="defective-model">
                          
                      </tbody>
                  </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<!-- end Invoice Defective Part Pending Booking with Age Moda -->

<!-- Modal for ProForma Invoices -->
<div id="proFormaModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CRM Proforma Setup Invoice</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="<?php echo base_url() . "employee/invoice/generate_partner_proforma_invoice"; ?>" method="POST">
                    <input class="form-control" type="hidden" id="proforma_partner_id" name="partner_id" value="" />
                    <input class="form-control" type="hidden" id="proforma_partner_name" name="partner_name" value=""/>
                    <input class="form-control" type="hidden" id="proforma_invoice_type" name="invoice_type" value=""/>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Amount">Invoice Value<small> (with GST) </small>:</label>
                                    <input type="number" step=".02" class="form-control" style="width:92%" id="proforma_service_charge" name="service_charge" placeholder="Total Invoice Amount" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" id="proforma_agreement_date">Agreement Date:</label>
                                    <div class="input-group input-append date">
                                        <input id="proforma_from_date" class="form-control" style="z-index: 1059; background-color:#fff;" name="daterange" type="text" required readonly='true'>
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Create Proforma Invoice" class="btn btn-md btn-warning col-md-offset-4"/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End -->

<?php
if(isset($_SESSION['file_error'])){
    unset($_SESSION['file_error']);
}
?>
<script type="text/javascript">
    $("#invoice_id").select2();
    if($("#partner_sc").attr('type') !== "hidden"){
        $("#partner_sc").select2();
    }
    
    function getInvoicingData(source) {
        $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
        var vendor_partner_id = $('#invoice_id').val();
        var invoice_period = 'all';
        $('#overall_summary').css('display', 'none');
        $("#invoicing_table").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
            data: {vendor_partner_id: vendor_partner_id, source: source,invoice_period:invoice_period},
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
    
    function invoice_setup_model(partner_id, partner_name, CRM_SETUP) {

        $(".modal-title").html(partner_name + " "+CRM_SETUP);
        $("#model_partner_id").val(partner_id);
        $("#model_partner_name").val(partner_name);
        $("#model_invoice_type").val(CRM_SETUP);
        if(CRM_SETUP === '<?php echo QC_INVOICE_DESCRIPTION;?>'){
            $("#agreement_date").text("Invoice Date");
        } else {
            $("#agreement_date").text("Agreement Date");
        }
       
    }
    $("#from_date").datepicker({dateFormat: 'dd/mm/yy'});
    
    
    function getVendor(){ 
        $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loadring.gif');
        var  partner_source_type = $("#partner_sc").val();
        var vendor_type = $('#sf_type').val();
        $("#invoicing_table").css('display', 'none');
        $('#overall_summary').css('display', 'none');
        var sf_cp = $('#sf_cp').val();
        var due_date = $("#due_date").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/invoice/invoice_listing_ajax/'+vendor_type,
            data:{'vendor_partner': '<?php echo $vendor_partner; ?>', 'sf_cp':sf_cp, 'partner_source_type':partner_source_type, 'due_date':due_date},
            success: function (data) {
                $('#loader_gif').attr('src', '');
                $("#overall_summary").show();
                $("#overall_summary").html(data);
                $("#dowmload_excel_due_date").val(due_date);
            }
        });
    }
    
</script>

<script type="text/javascript">
   $(document).ready(function(){
            $("#selecctall_amt").change(function(){
                       $(".checkbox_amt").prop('checked', $(this).prop("checked"));
                              });
            $("#due_date").datepicker({
                dateFormat: 'dd/mm/yy',
            });
           // $('#due_date').datepicker('setDate', new Date());
                              
               });
</script>

 <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

    <script type="text/javascript">
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            locale: {
               format: 'DD/MM/YYYY'
            },
            startDate: '<?php echo date("01/m/Y", strtotime("-1 month")) ?>',
            endDate: '<?php echo date('d/m/Y', strtotime('last day of previous month')); ?>'
        });
//        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
//          $(this).val(picker.startDate.format('YYYY/MM/DD') + '-' + picker.endDate.format('YYYY/MM/DD'));
//        });

        });
</script>
<script>
function get_defective_spare_count(vendor_id){
    
    $.ajax({
        type:"POST",
        beforeSend: function(){

                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                });

        },
        url: "<?php echo base_url(); ?>employee/invoice/get_pending_defective_parts_list/" + vendor_id,
        success:function(response){
            console.log(response);
            if(response === "DATA NOT FOUND"){
                $('body').loadingModal('destroy');
                alert("DATA NOT FOUND");
            } else {
               $("#defective-model").html(response);   
               $('#defective_part_pending_booking_age').modal('toggle'); 
               $('body').loadingModal('destroy');
            }
            
        }
    });
    
}

    function proforma_invoice_model(partner_id, partner_name, CRM_SETUP) {
        $(".modal-title").html(partner_name + " "+CRM_SETUP);
        $("#proforma_partner_id").val(partner_id);
        $("#proforma_partner_name").val(partner_name);
        $("#proforma_invoice_type").val(CRM_SETUP);
        $("#proforma_agreement_date").text("Invoice Date");
    }
       
     
</script>

