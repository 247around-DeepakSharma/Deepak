<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
            Map Model to Plan
        </h1>
        <?php if ($this->session->userdata('failed')) { ?>
            <div class="alert alert-success alert-dismissible" role="alert">
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
        <form name="myForm" class="form-horizontal" id ="plan_model_form" action="<?php echo base_url(); ?>employee/warranty/add_model_to_plan"  method="POST">
            <div class="panel panel-info" >
                <div class="panel-heading">Add Model to Plan </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                <div class="form-group">
                                    <label for="service_id" class="col-md-4">Plan *</label>
                                    <div class="col-md-6">
                                        <select name="plan_id" class="form-control" id="plan_id"   required>
                                            <option selected disabled="">Please Select Plan</option>
                                            <?php foreach ($warranty_plans as $value) { ?>
                                                <option value="<?php echo $value->plan_id; ?>"  <?php
                                                if (set_value('plan_id') == $value->plan_id) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo $value->plan_name . " (" . $value->plan_id . ")"; ?></option>
                                                    <?php } ?>
                                        </select>
                                        <?php echo form_error('plan_id'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="model_id" class="col-md-4">Model *</label>
                                    <div class="col-md-6">                                        
                                        <select name="model_id" class="form-control" id="model_id" required>
                                            <option selected disabled="">Please Select Model</option>
                                            <?php foreach ($appliance_models as $value) { ?>
                                                <option value="<?php echo $value->id."###".$value->service_id; ?>"  <?php
                                                if (set_value('id') == $value->id) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo $value->model_number; ?></option>
                                                    <?php } ?>
                                        </select>
                                        <?php echo form_error('model_id'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php
                                if (form_error('model_id')) {
                                    echo 'has-error';
                                }
                                ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
                    <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
                    <a href="<?php echo base_url(); ?>employee/warranty/plan_model_mapping" class="btn btn-primary" id="back_btn" value="Back" style="margin-left: 10px;"/>Back</a>
                </center>
            </div>
        </form>
    </div>
</div>
<script>
    $('#plan_id, #model_id').select2();
</script>