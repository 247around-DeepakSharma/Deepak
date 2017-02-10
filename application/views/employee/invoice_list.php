<div id="page-wrapper">
   <div class="container-fluid">
        <a class="btn btn-lg btn-primary pull-right" style="margin-top:20px;" href="<?php echo base_url();?>employee/invoice/insert_update_invoice/<?php if(isset($service_center)){ echo 'vendor'; } else { echo 'partner';}?>">Create Invoice</a>
      <div class="row">
         <div class="col-md-6 ">
             <h1 class="page-header"><b><?php if(isset($service_center)){ ?>Service Center Invoices<?php } else {?>
               Partner Invoices
            <?php } ?>
            </b>
             </h1>
            
         </div>
          
      </div>
      <div class="row" >
         <div class="form-group">
            <label for="state" class="col-sm-1">Select</label>
            <div class="col-md-4">
                <?php if(isset($service_center)){ ?>
               <select class="form-control" name ="service_center" id="invoice_id" onChange="getInvoicingData('vendor')">
                  <option disabled selected >Service Center</option>
                 
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
                  <option value = "<?php echo $partnerdetails['id']?>">
                     <?php echo $partnerdetails['public_name'];?>
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

<?php if(isset($invoicing_summary)){ ?>
 <div class="row" style="margin-top: 20px;" id="overall_summary">
<h2>Invoices Overall Summary</h2>

  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr>
         <th>No #</th>
         <th>Vendor/Partner</th>
         <th>Amount to be Paid</th>
         <th>Amount to be Received</th>
         <th>Pay</th>
          <?php if(isset($service_center)){ ?>
         <th>Download Summary</th>
          <?php } ?>
      
      </tr>
   </thead>
   <form action="<?php echo base_url();?>employee/invoice/download_invoice_summary" method="POST" target="_blank">
   <tbody><?php $foc= 0; $cash = 0;?>
     <?php $count = 1; foreach ($invoicing_summary as $key => $value) { ?>
       <tr style = "<?php if(isset($value['on_off'])){ if($value['active'] == 0){ echo 'background-color:#FF8041;color:#fff;'; } 
       else if($value['on_off'] == 0){ echo "background-color:#FFEC8B;color:black;"; }}?>"> 
        <td><?php echo $count; ?></td>
        <td> <a style="<?php if(isset($value['on_off'])){ if($value['active'] == 0){ echo 'background-color:#FF8041;color:#fff;'; } 
       else if($value['on_off'] == 0){ echo "background-color:#FFEC8B;color:black;"; }}?>" href="<?php echo base_url()?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner']?>/<?php echo $value['id'] ?>" target='_blank'><?php echo $value['name']?></a></td>
        <td><?php if($value['final_amount'] <0){echo round($value['final_amount'],0); $foc +=abs(round($value['final_amount'],0));}?></td>
        <td><?php if($value['final_amount'] >0){echo round($value['final_amount'],0);  $cash +=abs(round($value['final_amount'],0));}?></td>
       
        <td><?php if($value['final_amount'] <0){?> 
        <a href="<?php echo base_url()?>employee/invoice/invoice_summary/<?php echo $value['vendor_partner']?>/<?php echo $value['id'] ?>" target='_blank' class="btn btn-sm btn-success">Pay</a>

        <?php }?></td>
        <?php if(isset($service_center)){ ?>
        <td ><input type="checkbox" name="<?php echo "amount_service_center[".$value['id']."]";?>" value ="<?php echo abs($value['final_amount']);?>" class="form-control" <?php if($value['is_verified'] ==0){ echo "disabled";}?>> </td>
         <?php } ?>
      </tr>
    <?php  $count++ ;} ?>
      <tr>
          <td>Total</td>
          <td></td>
          <td><?php echo -$foc; ?></td>
          <td><?php echo $cash; ?></td>
          <td></td>
          <td class="text-center"><input type="submit" class="btn btn-md btn-primary"  value="Download"/></td>
      </tr>
   </tbody>
   </form>
   </table>

<?php } ?>

</div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $("#invoice_id").select2();
   
   function getInvoicingData(source){
       $('#loader_gif').attr('src', '<?php echo base_url() ?>images/loader.gif');
    var vendor_partner_id = $('#invoice_id').val();
    $('#overall_summary').css('display', 'none');
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
