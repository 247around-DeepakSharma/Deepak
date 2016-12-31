<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
     <?php
                if ($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
                }
                ?>
         <div class="col-md-6">
            <h1 class="page-header">
               Escalate Form
            </h1>
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active">
                  <div class="container" style="margin-top:5%;">
                     <div class="row">
                        <div class="col-md-8">
			    <form class="form-horizontal" action="<?php echo base_url() ?>partner/process_escalation/<?php echo $booking_id;?>" method="POST" >
                           <div class="form-group">
                              <label for="Booking Id" class="col-md-2">Booking Id</label>
                              <div class="col-md-6">
                                 <input type="text" class="form-control"  name="booking_id"  value = "<?php echo $booking_id;?>" placeholder = "Booking Id"  readonly>
                              </div>
                           </div>
                           <div class="form-group  <?php if( form_error('escalation_reason_id') ) { echo 'has-error';} ?>">
                              <label for="Service" class="col-md-2">Reason</label>
                              <div class="col-md-6">
                                 <select class=" form-control" name ="escalation_reason_id" >
                                    <option selected disabled>----------- Select Reason ------------</option>
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
                               <div class="form-group">
                              <label for="Escalation Remarks" class="col-md-2">Remarks</label>
                              <div class="col-md-6">
                                  <textarea  class="form-control"  name="escalation_remarks" placeholder = "Remarks" ></textarea>
                              </div>
                           </div>
                           
                           <div class="form-group ">
                              
                                 <input type= "submit"  class=" col-md-3 col-md-offset-4 btn btn-primary btn-lg" value ="Save" style="background-color:#2C9D9C; border-color:#2C9D9C;">
                             
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