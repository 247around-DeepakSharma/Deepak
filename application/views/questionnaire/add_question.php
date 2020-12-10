<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
            <?php 
                if(!empty($q_data[0]->q_id)){
                    echo "Update Question";
                }
                else {
                    echo "Add Question";
                }
            ?>            
        </h1>
        
        <?php if ($this->session->userdata('failed')) { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php echo $this->session->userdata('failed') ?></strong>
            </div>
        <?php } ?>

        <?php if ($this->session->userdata('success')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><?php echo $this->session->userdata('success') ?></strong>
            </div>
        <?php } ?>

        <?php       
            $this->session->unset_userdata('success');
            $this->session->unset_userdata('failed');          
        ?>

        <form name="myForm" class="form-horizontal" id="question_form" action="<?php echo base_url(); ?>employee/questionnaire/save_question"  method="POST">
            <div class="panel panel-info" >
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="q_id" id="q_id" value="<?php if (!empty($q_data[0]->q_id)) { echo $q_data[0]->q_id; } ?>">
                            <div class="col-md-10 col-md-offset-2">                                
                                <!-- select Panel -->
                                <div class="form-group <?php if (form_error('panel')) { echo 'has-error'; } ?>">
                                    <label for="panel" class="col-md-3">Panel *</label>
                                    <div class="col-md-6">
                                        <select name="panel" class="form-control" id="panel" required>
                                            <option selected disabled="">Please Select Panel</option>
                                            <?php
                                            foreach ($panels as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>"<?php if (!empty($q_data[0]->panel) && $q_data[0]->panel == $key) { echo 'selected';  } ?> ><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('panel'); ?>
                                    </div>
                                </div>
                                <!-- select Form -->
                                <div class="form-group <?php if (form_error('form')) { echo 'has-error'; } ?>">
                                    <label for="form" class="col-md-3">Form *</label>
                                    <div class="col-md-6">
                                        <select name="form" class="form-control" id="form" required>
                                            <option selected disabled="">Please Select Form</option>
                                            <?php
                                            foreach ($forms as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>"<?php if (!empty($q_data[0]->form) && $q_data[0]->form == $key) { echo 'selected';  } ?> ><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('form'); ?>
                                    </div>
                                </div>
                                <!-- select Appliance -->
                                <div class="form-group <?php if (form_error('service_id')) { echo 'has-error'; } ?>">
                                    <label for="service_id" class="col-md-3">Appliance *</label>
                                    <div class="col-md-6">
                                        <select name="service_id" class="form-control" id="service_id" required onchange="getPriceTags()">
                                            <option selected disabled="">Please Select Appliance</option>
                                            <?php                                            
                                            foreach ($services as $value) { ?>
                                                <option value="<?php echo $value->id; ?>" <?php if (!empty($q_data[0]->service_id) && $q_data[0]->service_id == $value->id) { echo 'selected';  } ?> >
                                                    <?php echo $value->services; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('service_id'); ?>
                                    </div>
                                </div>
                                <!-- select Request Type -->
                                <div class="form-group <?php if (form_error('request_type')) { echo 'has-error'; } ?>">
                                    <label for="request_type" class="col-md-3">Request Type *</label>
                                    <div class="col-md-6">
                                        <select name="request_type" class="form-control" id="request_type" required>
                                            <option selected disabled="">Please Select Service category</option>
                                        </select>
                                        <?php echo form_error('request_type'); ?>
                                    </div>
                                    <div id="loader_gif"></div>
                                </div>
                                <!-- Add Question -->
                                <div class="form-group <?php if (form_error('question')) { echo 'has-error'; } ?>">
                                    <label for="question" class="col-md-3">Question *</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="4" wrap="physical" id="question" name="question" placeholder="Enter Question" required><?php if (!empty($q_data[0]->question)) { echo $q_data[0]->question; } ?></textarea>
                                        <?php echo form_error('question'); ?>
                                    </div>
                                </div>
                                <!-- Add Sequence -->
                                <div class="form-group <?php if (form_error('sequence')) { echo 'has-error'; } ?>">
                                    <label for="sequence" class="col-md-3">Sequence *</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" id="sequence" name="sequence" placeholder="Enter Sequence" max="10" min="1" required value="<?php if (!empty($q_data[0]->sequence)) { echo $q_data[0]->sequence; } ?>">
                                        <?php echo form_error('sequence'); ?>
                                    </div>
                                </div>
                                <!-- Add Is Required -->
                                <div class="form-group">
                                    <label for="is_required" class="col-md-3">Is Required *</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" id="is_required" name="is_required" value="1" <?php if (!empty($q_data[0]->is_required) ) { echo "checked"; } ?>>
                                    </div>
                                </div>
                                <!-- Add Options (if any) -->
                                <div class="form-group">
                                    <label for="options" class="col-md-3">Options</label>
                                    <div class="col-md-6">
                                        <input type="text" name="options" id="options"class="form-control" value="<?php if (!empty($q_data[0]->answers)) { echo $q_data[0]->answers; } ?>">
                                        <label style="font-weight:normal">
                                            <ul style="padding: 6px 12px;">
                                                <li> Add comma separated values (in case above question has some pre-defined answers) </li>
                                                <?php if(!empty($q_data[0]->q_id)){ 
                                                  echo "<li>Options that are already saved against any Booking will not be changed.</li>";  
                                                } ?>
                                            </ul>                                            
                                        </label>
                                    </div>                                    
                                </div>
                                <!-- Active/In-Active -->
                                <div class="form-group">
                                    <label for="active" class="col-md-3">Active *</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" id="active" name="active" value="1" <?php if (!empty($q_data[0]->active)) { echo "checked"; } ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 50px;">
                <center><input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" /></center>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
       getPriceTags(); 
    });

    function getPriceTags(){
        var get_price_tag_url = '<?php echo base_url(); ?>employee/service_centre_charges/get_service_category_request_type';
        var postData = {};
        postData['service_id'] = $("#service_id").val();
        postData['price_tags'] = "<?php echo !empty($q_data[0]->service_category) ? $q_data[0]->service_category : '' ?>";
        $('#loader_gif').html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:40px'>");
        $.ajax({
            url: get_price_tag_url,
            method : 'POST',
            data: postData,
            success: function (data) {
                console.log(data);
                $("#request_type option").remove();
                $('#request_type').append("<option selected disabled>Select Service category</option>").change();
                $('#request_type').append(data).change();  
                $('#loader_gif').html('');
            }
        });
    }
</script>