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
            <div class="row">
                <h1 class="col-md-6 col-sm-12 col-xs-12"><b> Add Symptom Defect Solution Mapping</b></h1>
                <?php if($this->session->userdata('user_group') != 'closure'){?>
                    <div class="col-md-6 col-sm-12 col-xs-12" style="float:right;margin-top: 30px;margin-bottom: 10px;">
                        <a href="<?php echo base_url();?>employee/booking_request/symptom_defect_solution_mapping"><input class="btn btn-primary pull-right" type="Button" value="View Symptom Defect Solution Mapping"></a>
                    </div>
                <?php }?>
            </div>   
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
            <hr>
            <div class="col-lg-12">
                <form class="form-horizontal"  id="fileinfo" action="<?php echo base_url();?>employee/booking_request/process_add_new_symptom_defect_solution" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>" >
                        <label for="excel" class="col-md-2">Select&nbsp;Appliance *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="service_id" required="" name="service_id" onchange="getPriceTags();get_symptoms();get_defects();get_solutions();">
                                <option selected disabled  >Select Appliance</option>
                            </select>
                        </div>
                        <?php echo form_error('service_id'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('request_type') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-2">Select&nbsp;Request&nbsp;Type *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="request_type" name="request_type[]" multiple="multiple" required="">
                            </select>
                        </div>
                        <?php echo form_error('request_type'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('symptom') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-2">Select&nbsp;Symptom *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="symptom" name="symptom" required="">
                            </select>
                        </div>
                        <?php echo form_error('symptom'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('defect') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-2">Select&nbsp;Defect *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="defect" name="defect" required="">
                            </select>
                        </div>
                        <?php echo form_error('defect'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('solution') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-2">Select Solution *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="solution" name="solution" required="">
                            </select>
                        </div>
                        <?php echo form_error('solution'); ?>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-2">
                            
                            <input type= "submit"  class="btn btn-success btn-md" id="submit_btn" value ="Add New Mapping" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#service_id').select2();
    $('#symptom').select2({
        placeholder: "Select Symptom"
    });
    $('#defect').select2({
        placeholder: "Select Defect"
    });
    $('#solution').select2({
        placeholder: "Select Solution"
    });
    get_appliance();
    /*get_symptoms();
    get_defects();
    get_solutions();*/
    
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
                }
            }
        });
       
    }
    
    function get_symptoms(){
        var service_id=$("#service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_symptoms',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#symptom').append(response);
                   $("#symptom option[value=all]").remove();
                   //$('#model_service_id').select2();
                }else{
                   //console.log(response);
                }
           }
        });
    }
    
    function get_defects(){
        var service_id=$("#service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_defects',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#defect').append(response);
                   $("#defect option[value=all]").remove();
                }
           }
        });
    }
    
    function get_solutions(){
        var service_id=$("#service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_solutions',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#solution').append(response);
                   $("#solution option[value=all]").remove();
                }
           }
        });
    }
    
    function getPriceTags(){
       var url = '<?php echo base_url(); ?>employee/service_centre_charges/get_service_category_request_type';
       var postData = {};
       postData['service_id'] = $("#service_id").val();
      
       sendAjaxRequest(postData, url).done(function (data) {
        
          $("#request_type option[value !='Select Request Type']").remove();
          //$('#request_type').append("<option selected disabled>Select Request Type</option>").change();
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