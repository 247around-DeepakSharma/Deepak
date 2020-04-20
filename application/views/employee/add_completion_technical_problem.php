<style>
    #datatable1_info{
    display: none;
    }
    #datatable1_filter{
    text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <?php
                    if ($this->session->userdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                    ?>
            
            <?php
                    if ($this->session->userdata('success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
            <div class="col-lg-12">
                <h1 class="page-header">
                    <b> Add Symptom</b>
                </h1>
                <form class="form-horizontal"  id="fileinfo" action="<?php echo base_url();?>employee/booking_request/process_add_new_completion_technical_problem" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>" >
                        <label for="excel" class="col-md-1">Select Appliance</label>
                        <div class="col-md-4">
                            <select class="form-control" id="service_id" required="" name="service_id" onchange="getPriceTags()">
                                <option selected disabled  >Select Appliance</option>
                            </select>
                        </div>
                        <?php echo form_error('service_id'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('request_type') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-1">Select Service Category</label>
                        <div class="col-md-4">
                            <select class="form-control" id="request_type" name="request_type[]" multiple="" required="">
                                <option selected disabled  >Select Service category</option>
                            </select>
                        </div>
                        <?php echo form_error('request_type'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('symptom') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-1">Add Symptom</label>
                        <div class="col-md-4">
                            <textarea class="form-control" id="symptom" name="symptom" rows="4"></textarea>
                        </div>
                        <?php echo form_error('symptom'); ?>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-2">
                            
                            <input type= "submit"  class="btn btn-success btn-md" id="submit_btn" value ="Add New Symptom" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#service_id').select2();
    get_appliance();
    
    $('#request_type').select2({
        placeholder: "Please select request type"
    });
    function get_appliance(){
    
           $.ajax({
               type: 'GET',
               url: '<?php echo base_url() ?>employee/booking/get_service_id',
               data: {'is_option_selected':true},
               success: function (response) {
                   if(response){
                       $('#service_id').html(response);
                       $("#service_id option[value=all]").remove();
                   }else{
                       console.log(response);
                   }
               }
           });
       
    }
    
    function getPriceTags(){
       var url = '<?php echo base_url(); ?>employee/service_centre_charges/get_service_category_request_type';
       var postData = {};
       postData['service_id'] = $("#service_id").val();
      
       sendAjaxRequest(postData, url).done(function (data) {
        
          $("#request_type option[value !='Select Service category']").remove();
          $('#request_type').append("<option selected disabled>Select Service category</option>").change();
          $('#request_type').append(data).change();
          
    
       });
    }
    
    function sendAjaxRequest(postData, url) {
       return $.ajax({
           data: postData,
           url: url,
           type: 'post'
       });
    }
</script>
<?php  if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php  if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>