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
                  <option value = "<?php echo $vendor['id']?>">
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
                  <option value = "<?php echo $partnerdetails['partner_id']?>">
                     <?php echo $partnerdetails['source'];?>
                  </option>
                  <?php } ?>
               </select>
                     
                <?php }?>
            </div>
         </div>
      </div>
      <div class="row" style="margin-top: 20px;">
         <div class="col-md-12 ">
            <table class="table table-bordered  table-hover table-striped data" id="invoicing_table"></table>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $("#invoice_id").select2();
   
   function getInvoicingData(source){
   	var vendor_partner_id = $('#invoice_id').val();
   	$.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/invoice/getInvoicingData',
          data: {vendor_partner_id: vendor_partner_id, source: source},
          success: function (data) {
            //console.log(data);
            $("#invoicing_table").html(data);          
         }
       });
   }
</script>