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
   </div>

<script type="text/javascript">
   $("#invoice_id").select2();

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


</script>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>