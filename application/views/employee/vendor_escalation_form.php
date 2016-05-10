<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
      <?php if($this->session->userdata('success')) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
               }
               ?>  
         <div class="col-md-6">
            <h1 class="page-header">
               Vendor Escalate Form
            </h1>
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active">
                  <div class="container" style="margin-top:5%;">
                     <div class="row">
                        <div class="col-md-8">
			    <form class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_vendor_escalation_form" method="POST" >
                           <div class="form-group">
                              <label for="Booking Id" class="col-md-2">Booking Id</label>
                              <div class="col-md-6">
                                 <input type="text" class="form-control"  name="booking_id"  value = "<?php echo $booking_id;?>" placeholder = "Booking Id"  readonly>
                              </div>
                           </div>
                           <div class="form-group ">
                              <label for="Vendor Id" class="col-md-2">Vendor Name</label>
                              <div class="col-md-6">
                                 <input type="text" class="form-control"  name="vendor_name"  value = "<?php echo  $vendor_details[0]['name']; ?>"  disabled>
                                 <input type="hidden" class="form-control"  name="vendor_id"  value = "<?php echo  $vendor_details[0]['id']; ?>" >
                              </div>
                           </div>
                           <div class="form-group  <?php if( form_error('escalation_reason_id') ) { echo 'has-error';} ?>">
                              <label for="Service" class="col-md-2">Reason</label>
                              <div class="col-md-6">
                                 <select class=" form-control" name ="escalation_reason_id" >
                                    <option selected disabled>-----------Select Reason Any One------------</option>
                                    <?php 
                                       foreach ($escalation_reason as $reason) {     
                                       ?>
                                    <option value = "<?php echo $reason['id']?>">
                                       <?php echo $reason['escalation_reason'];?>
                                    </option>
                                    <?php } ?>
                                 </select>
                                 <?php echo form_error('escalation_reason_id'); ?>
                              </div>
                           </div>
                           
                           <div class="form-group ">
                              
                                 <input type= "submit"  class=" col-md-3 col-md-offset-4 btn btn-primary btn-md" value ="Save">
                             
                           </div>
                          
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end row -->
      </div>
      <!-- end container fluid -->
   </div>
</div>
<script>
   $(".js-example-basic-multiple").select2();
</script>
<?php $this->session->unset_userdata('success'); ?>