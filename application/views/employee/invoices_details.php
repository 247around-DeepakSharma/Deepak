<script src="<?php echo base_url(); ?>js/invoice_tag.js"></script>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
          <?php
          if ($this->session->userdata('success')) {
              echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
          }
          ?>
          <?php
          if ($this->session->userdata('error')) {
              echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('error') . '</strong>
                                </div>';
          }
          ?>
         <div class="col-md-12">
            <h1 class="page-header"><b><?php if(isset($service_center)){ ?>Service Center Invoices <?php } else {?>
               Partner Invoices
            <?php } ?></b>
            <div class="pull-right">
              <button onclick="open_create_cd_invoice_form()" class="btn btn-md btn-primary">Create CN/DN</button>
            </div>
            </h1>
        </div>
          
      </div>
      <div class="row">
        <div class="form-group col-md-4">
            <label for="state" class="col-sm-4"> <?php if(isset($service_center)){ echo "Vendor Name"; }else{ echo "Partner Name"; } ?> </label>
            <div class="col-md-8">
                <?php if(isset($service_center)){ ?>
               <select class="form-control" name ="service_center" id="invoice_id" onChange="getInvoicingData('vendor')">
                  <option disabled selected >Service Center</option>

                  <?php
                     foreach ($service_center as $vendor) {
                     ?>
                  <option <?php if(isset($vendor_partner_id)){ if($vendor_partner_id ==$vendor['id']) { echo "selected"; }} ?> value = "<?php echo $vendor['id']?>">
                     <?php echo $vendor['name'];?>
                  </option>
                  <?php } ?>
               </select>

                <?php } else { ?>

                 <select class="form-control" name ="partner" id="invoice_id" onChange="getInvoicingData('partner')">
                  <option disabled selected >Partner Name</option>

                  <?php
                     foreach ($partner as $partnerdetails) {
                     ?>
                  <option value = "<?php echo $partnerdetails['id']?>" <?php if($partnerdetails['id'] == $vendor_partner_id){ echo "selected";}?>>
                     <?php echo $partnerdetails['public_name'];?>
                  </option>
                  <?php } ?>
               </select>

                <?php }?>
            </div>
        </div>
           <div class="form-group col-md-4" style="">
            <label for="filter_invoice_type" class="col-sm-3">Invoice Type</label>
            <div class="col-md-9">
                <select class="form-control filter_table" id="filter_invoice_type" name="filter_invoice_type[]" onchange="getInvoicingData('<?php echo $vendor_partner; ?>')" multiple="multiple" data-placeholder="All">
                    
                </select>
            </div>
         </div>
          <div class="form-group col-md-4" style="">
            <label for="invoice_period" class="col-sm-4">Invoice Period</label>
            <div class="col-md-8">
                <select class="form-control" id="invoice_period" onchange="getInvoicingData('<?php echo $vendor_partner; ?>')">
                    <option value="all">All</option>
                    <option value="cur_fin_year">Current Financial Year</option>
                </select>
            </div>
         </div>
         <div class="form-group col-md-4" style="">
            <label for="vertical" class="col-sm-4">Vertical</label>
            <div class="col-md-8">
                <select class="form-control" id="vertical">
                  <option disabled selected>Select Vertical</option>
                </select>
            </div>
         </div>
          <div class="form-group col-md-4" style="">
            <label for="category" class="col-sm-3">Category</label>
            <div class="col-md-9">
                <select class="form-control" id="category">
                    <option disabled selected>Select Category</option>
                </select>
            </div>
         </div>
          <div class="form-group col-md-4" style="">
            <label for="sub_category" class="col-sm-4">Sub Category</label>
            <div class="col-md-8">
                <select class="form-control" id="sub_category">
                   <option disabled selected>Select Sub Category</option>
                </select>
            </div>
         </div>
         <div class="form-group col-md-4" style="">
            <label for="type_code" class="col-sm-4">247around Sales / Purchase</label>
            <div class="col-md-8">
                <select class="form-control" id="type_code" onchange="getInvoicingData('<?php echo $vendor_partner; ?>')">
                   <option disabled selected>None Of these</option>
                   <option value='A'>Sales</option>
                   <option value='B'>Purchase</option>
                </select>
            </div>
         </div>
         <div class="form-group col-md-3">
            <label for="settle_invoice_checkbox" class="col-sm-8">Invoice Un-Settled</label>
            <div class="col-md-4">
                <input style="margin-left:15px" type="checkbox" onclick="getInvoicingData('<?php echo $vendor_partner; ?>');" checked id="settle_invoice_checkbox" name="settle_invoice_checkbox" class="form-control">
            </div>
         </div>
          
          <?php if(isset($service_center)){ ?>
          <div class="form-group col-md-3">
            <label for="fnf_invoice_checkbox" class="col-sm-8">FnF Invoice</label>
            <div class="col-md-4">
                <input style="margin-left:15px" type="checkbox" onclick="getInvoicingData('<?php echo $vendor_partner; ?>');" id="fnf_invoice_checkbox" name="fnf_invoice_checkbox" class="form-control">
            </div>
         </div>
          <?php } ?>
          
          <div class="form-group col-md-3">
            <label for="msl_invoice_checkbox" class="col-sm-8">MSL Invoice</label>
            <div class="col-md-4">
                <input style="margin-left:15px" type="checkbox" onclick="getInvoicingData('<?php echo $vendor_partner; ?>');" id="msl_invoice_checkbox" name="msl_invoice_checkbox" class="form-control">
            </div>
         </div>
          
      </div>
       <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif" /></div>
      <div class="row" style="margin-top: 20px;">
         <div class="col-md-12 ">
             <div id="invoicing_table"></div>
         </div>

</div>
      </div>
    
    <?php
	  if(isset($service_center_payment_hold_reason) && count($service_center_payment_hold_reason) > 0 && isset($service_center))
	  {
	  ?>
		<div class='col-md-12' id='payment_hold_reason' style='display:none'>
		<h2><u>Payment Hold Reason</u></h2>
			<table class="table  table-striped">
					<thead style='background: #f2dede;'>
						<tr>
							<th class="text-center">Sn.</th>				
							<th class="text-center">Payment hold reason</th>
							<th class="text-center">Created Date</th>
						</tr>
					</thead>
					<tbody>
						<?php  
						$StartRowCount=0;
						$totalAmount=0;
						$TotalCashInoviceInst=0;

						foreach ($service_center_payment_hold_reason as $row)  
						{  //print_r($row);
						?>
							<tr id='rowid<?php echo $row['id']; ?>'>
							<td class="text-center"><?php echo ++$StartRowCount; ?></td>			
							<td class="text-center"><?php echo $row['payment_hold_reason']; ?></td>
							<td class="text-center"><?php echo $this->miscelleneous->get_formatted_date($row['create_date']); ?></td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
		</div>
		
	  <?php
	  }
	  ?>
    
         <div id="myModal2" class="modal fade" role="dialog">
             <div class="modal-dialog modal-lg" style="width: 62%;">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Generate CN/DN</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id ="cn_dn_form" action="#"  method="POST" >
                    <div class="col-md-12" >
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="gst_number">247around GST Number *</label>
                                <select class="form-control"  id="gst_number" name="gst_number" required>
                                    <option value="" disabled selected>Select 247around GST Number *</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="Invoice type">Select Type *</label>
                                <select name="invoice_type" id="invoice_type" class="form-control">
                                    <option value="<?php echo CREDIT_NOTE; ?>"><?php echo CREDIT_NOTE;?></option>
                                    <option value="<?php echo DEBIT_NOTE; ?>"><?php echo DEBIT_NOTE;?></option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="invoice Date">Date *</label>
                                 
                                <input type="text" class="form-control" style="font-size: 13px; background-color:#fff;" placeholder="Select Date" id="invoice_date" name="invoice_date" required readonly='true' >
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="reference_numner">Reference Number </label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="reference_numner" placeholder="Enter Reference Number" name="reference_numner" value = "" required>
                            </div>
                        </div>
                    
                        <div class="col-md-12 ">
                         <hr/>
                        <h4><label for="Invoice type">Service Charge</label></h4>
                        </div>
                       
                        <div class="col-md-4" style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="Service Charge">Service Rate *</label>
                                <input type="number" step=".02" class="form-control" style="font-size: 13px;"  id="service_rate" placeholder="Enter Serivce Rate" name="service_rate" value = "0" >
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="Service Qty">Service Qty *</label>
                                <input type="number" class="form-control" style="font-size: 13px;"  id="service_count" placeholder="Enter Service Quantity" name="service_count" value = "0" >
                            </div>
                        </div>
                         <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                
                                <label for="GST Rate">Service GST Rate *</label>
                                <input type="number" step=".02" class="form-control" style="font-size: 13px;" id="gst_rate" placeholder="Enter GST Rate" name="service_gst_rate" value = "18" >
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="HSN CODE">Service HSN Code </label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="hsn_code" placeholder="Enter HSN Code" name="service_hsn_code" value = "" >
                            </div>
                        </div>
                         <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="Description">Service Description *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="description" placeholder="Enter Description" name="service_description" value = "" >
                            </div>
                        </div>
                        <div class="col-md-12"> <hr/></div>
                         
                        <div class="col-md-12 ">
                        
                        <h4><label for="Invoice type">Part Charge</label></h4>
                        </div>
                       
                        <div class="col-md-4" style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="Part Charge">Part Rate *</label>
                                <input type="number" step=".02" class="form-control" style="font-size: 13px;"  id="part_rate" placeholder="Enter Part Rate" name="part_rate" value = "0" >
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="Service Qty">Part Qty *</label>
                                <input type="number" class="form-control" style="font-size: 13px;"  id="part_count" placeholder="Enter Part Quantity" name="part_count" value = "0" >
                            </div>
                        </div>
                         <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                
                                <label for="GST Rate">Part GST Rate *</label>
                                <input type="number" step=".02" class="form-control" style="font-size: 13px;" id="part_gst_rate" placeholder="Enter Part GST Rate" name="part_gst_rate" value = "18" >
                            </div>
                        </div>
                        <div class="col-md-4 " style="width:25%;">
                            <div class="form-group col-md-12  ">
                                <label for="HSN CODE">Part HSN Code </label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="part_hsn_code" placeholder="Enter HSN Code" name="part_hsn_code" value = "" >
                            </div>
                        </div>
                         <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="Description">Description *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="part_description" placeholder="Enter Description" name="part_description" value = "" >
                            </div>
                        </div>
                        <div class="col-md-12"> <hr/></div>
                       
                      
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Remarks *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="remarks" placeholder="Enter Remarks" name="remarks" value = "" required>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="detailed Invoice">Detailed Invoice </label>
                                <input type="file" class="form-control" style="font-size: 13px;"  id="invoice_detailed_excel" name="invoice_detailed_excel" value = "">
                            </div>
                        </div>
                    </div>
                </form>
                
            </div>

            <div class="modal-footer">
               <button type="submit" class="btn btn-success" onclick="genaerate_cn_dn_invoice()">Submit</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
   </div>

<script type="text/javascript">
   $("#invoice_id, #category, #sub_category, #vertical").select2();
   $(function() {
        $('#invoice_date').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y-m-01", strtotime("-1 month")) ?>',
            endDate: '<?php echo date('Y-m-d', strtotime('last day of previous month')); ?>'
        });
        
        get_247around_wh_gst_number('247001');

    });

   $(document).ready(function () {

        getInvoicingData('<?php echo $vendor_partner; ?>');
        get_invoice_type();
        $("#filter_invoice_type").select2();
        get_vertical('<?php echo base_url(); ?>');
        
        $('#vertical').change(function(){
            get_category('<?php echo base_url(); ?>');
            getInvoicingData('<?php echo $vendor_partner; ?>');
        });
        
        $('#category').change(function(){
            get_sub_category('<?php echo base_url(); ?>')
            getInvoicingData('<?php echo $vendor_partner; ?>');
        });
        
        $('#sub_category').change(function(){
           getInvoicingData('<?php echo $vendor_partner; ?>');
        });
        
    });


   function getInvoicingData(source){

       $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
    var vendor_partner_id = $('#invoice_id').val();
    var c =  0, msl = 0, fnf = 0;
    
    if($('#settle_invoice_checkbox').is(":checked")){
        c = 1;
    } else {
        c = 0;
    }
    
    //check if MSL invoice checkbox is checked
    if($('#msl_invoice_checkbox').is(":checked")){
        msl = 1;
    } else {
        msl = 0;
    }
    
    //check if Fnf security checkbox is checked
    if($('#fnf_invoice_checkbox').is(":checked")){
        fnf = 1;
    } else {
        fnf = 0;
    }    
    
    var invoice_period = $('#invoice_period').val();
    var invoice_type = $('#filter_invoice_type').val();
    var vertical = $("#vertical").val();
    var category = $("#category").val();
    var sub_category = $("#sub_category").val();
    var type_code = $("#type_code").val();
    
    $('#overall_summary').css('display', 'none');
    
    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
          data: {vendor_partner_id: vendor_partner_id, source: source, invoice_type:invoice_type ,
          invoice_period:invoice_period, settle_invoice: c, vertical:vertical, category:category, 
          sub_category:sub_category, 
          msl_invoice : msl,
          fnf_invoice : fnf,
          type_code:type_code},
              
          success: function (data) {
            $('#loader_gif').attr('src', '');
            $("#invoicing_table").html(data);
             if($("#payment_hold_reason").length != 0) {
            $('#payment_hold_reason').show();
            }
         }
       });
    }
   
    function get_invoice_type(){
        $.ajax({
            method: 'POST',
            data: {},
            url: '<?php echo base_url(); ?>employee/accounting/get_invoice_type',
            success: function (response) {
                $('#filter_invoice_type').html(response);
            }
        });
    }

//   function delete_banktransaction(transactional_id){

//     $.ajax({
//           type: 'POST',
//           url: '<?php// echo base_url(); ?>employee/invoice/delete_banktransaction/'+ transactional_id,

//           success: function (data) {
//             if(data ==="success"){
//                getInvoicingData("vendor");
//             }

//          }
//        });

//    }
   
   function open_create_cd_invoice_form(){
        $('#myModal2').modal('toggle'); 
    }
    
    function get_247around_wh_gst_number(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/inventory/get_247around_wh_gst_number',
            async: false,
            data:{partner_id:partner_id},
            success: function (response) {
                $("#gst_number").html(response);
            }
        });
    }
    
    function genaerate_cn_dn_invoice(){
        var fd = new FormData(document.getElementById("cn_dn_form"));
        var vendor_partner_id = $('#invoice_id').val();
        var service_charge = Number($("#service_rate").val());
        var parts_charge = Number($("#part_rate").val());
        if(Number(parts_charge + service_charge) > 0){
   <?php if (isset($service_center)) { ?>
                 fd.append('vendor_partner','vendor');
   <?php } else { ?>
                fd.append('vendor_partner','partner');
   <?php } ?>
       
        fd.append('vendor_partner_id',vendor_partner_id);
        $.ajax({
            url: "<?php echo base_url() ?>employee/invoice/generate_credit_debit_note",
            type: "POST",
            beforeSend: function(){
                 swal("Thanks!", "Please Wait..", "success");
                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                  });

             },
            data: fd,
            processData: false,
            contentType: false,
            success: function (data) {
                if(data === 'Success'){

                    location.reload();

                } else {
                    swal("Oops", data, "error");
                   
                    
                }
                $('body').loadingModal('destroy');
            }
        });
        } else {
            alert("Please Enter Basic Amount");
            return false;
        }
       
    }
    
</script>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>