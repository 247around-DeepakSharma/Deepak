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
         <div class="col-md-6 ">
             <h1 class="page-header"><b><?php if(isset($service_center)){ ?>Service Center Invoices<?php } else {?>
               Partner Invoices
            <?php } ?></b></h1>
             
         </div>
          <div class="pull-right">
              <button onclick="open_create_cd_invoice_form()" class="btn btn-md btn-primary">Create CN/DN</button>
             </div>
      </div>
      <div class="row" >
         <div class="form-group col-md-6">
            <label for="state" class="col-sm-2">Select</label>
            <div class="col-md-10">
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
                  <option disabled selected >Partner</option>

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
          <div class="form-group col-md-6">
            <label for="invoice_period" class="col-sm-4">Select Invoice Period</label>
            <div class="col-md-8">
                <select class="form-control" id="invoice_period" onchange="getInvoicingData('<?php echo $vendor_partner; ?>')">
                    <option value="cur_fin_year">Current Financial Year</option>
                    <option value="all">All</option>
                </select>
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
    
         <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Generate CN/DN</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id ="cn_dn_form" action="#"  method="POST" >
                    <div class="col-md-12" >
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Invoice type">Select Type *</label>
                                <select name="invoice_type" id="invoice_type" class="form-control">
                                    <option value="CreditNote">Credit Note</option>
                                    <option value="DebitNote">Debit Note</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="invoice Date">Invoice Date *</label>
                                 
                                <input type="text" class="form-control" style="font-size: 13px; background-color:#fff;" placeholder="Select Date" id="invoice_date" name="invoice_date" required readonly='true' >
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Service Charge">Basic Service Charge *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="service_charge" placeholder="Enter Serivce Charge" name="service_charge" value = "0" >
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Parts Charge">Basic Parts Charge *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="parts_charge" placeholder="Enter Parts Charge" name="parts_charge" value = "0" >
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Service Qty">Service Qty *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="service_count" placeholder="Enter Service Quantity" name="service_count" value = "0" >
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Parts Qty">Parts Qty *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="parts_count" placeholder="Enter Parts Quantity" name="parts_count" value = "0" >
                            </div>
                        </div>
                       
                        
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="GST Rate">GST Rate *</label>
                                <input type="text" class="form-control" style="font-size: 13px;" id="gst_rate" placeholder="Enter GST Rate" name="gst_rate" value = "0" required>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="HSN CODE">HSN Code </label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="hsn_code" placeholder="Enter HSN Code" name="hsn_code" value = "" >
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="Description">Description *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="description" placeholder="Enter Description" name="description" value = "" required>
                            </div>
                        </div>
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
   $("#invoice_id").select2();
   $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});

   $(document).ready(function () {

  getInvoicingData('<?php echo $vendor_partner; ?>');
});


   function getInvoicingData(source){

       $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
    var vendor_partner_id = $('#invoice_id').val();
    var invoice_period = $('#invoice_period').val();
    $('#overall_summary').css('display', 'none');
    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
          data: {vendor_partner_id: vendor_partner_id, source: source,invoice_period:invoice_period},
          success: function (data) {

            $('#loader_gif').attr('src', '');
            $("#invoicing_table").html(data);
         }
       });
   }

  function delete_banktransaction(transactional_id){

    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/delete_banktransaction/'+ transactional_id,

          success: function (data) {
            if(data ==="success"){
               getInvoicingData("vendor");
            }

         }
       });

   }
   
   function open_create_cd_invoice_form(){
        $('#myModal2').modal('toggle'); 
    }
    
    function genaerate_cn_dn_invoice(){
        var fd = new FormData(document.getElementById("cn_dn_form"));
        var vendor_partner_id = $('#invoice_id').val();
        var service_charge = Number($("#service_charge").val());
        var parts_charge = Number($("#parts_charge").val());
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