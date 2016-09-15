<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-6 ">
             <h1 class="page-header"><b><?php if(isset($service_center)){ ?>Service Centre Invoices<?php } else {?>
               Partner Invoices
            <?php } ?></b></h1>
         </div>
      </div>
      <div class="row" >
         <div class="form-group">
            <label for="state" class="col-sm-1">Select</label>
            <div class="col-md-4">
                <?php if(isset($service_center)){ ?>
               <select class="form-control" name ="service_center" id="invoice_id" onChange="getInvoicingData('vendor')">
                  <option disabled selected >Service Centre</option>
                 
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
                  <option <?php if(isset($vendor_partner_id)){ if($vendor_partner_id == $partnerdetails['id']) { echo "selected"; }} ?> value = "<?php echo $partnerdetails['id']?>">
                     <?php echo $partnerdetails['name'];?>
                  </option>
                  <?php } ?>
               </select>
                     
                <?php }?>
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
</div>
<script type="text/javascript">
   $("#invoice_id").select2();

   $(document).ready(function () {
  
  getInvoicingData('<?php echo $vendor_partner; ?>');
});

   
   function getInvoicingData(source){
       $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
    var vendor_partner_id = $('#invoice_id').val();
    $('#overall_summary').css('display', 'none');
    $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
          data: {vendor_partner_id: vendor_partner_id, source: source},
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
            if(data =="success"){
               getInvoicingData("vendor");
            }
         
         }
       });

   }

     
</script>
