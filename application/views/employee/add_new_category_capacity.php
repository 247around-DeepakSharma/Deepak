<div id="page-wrapper" >
    <div class="container" >
        <h1 class="page-header">
            Map Product
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
        <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url(); ?>employee/service_centre_charges/process_add_new_category"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" >
                <div class="panel-heading">Add Product Category / Capacity </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-2">
                                <div class="form-group <?php
                                if (form_error('service_id')) {
                                    echo 'has-error';
                                }
                                ?>">
                                    <label for="service_id" class="col-md-4">Product *</label>
                                    <div class="col-md-6">
                                        <select name="service_id" class="form-control" id="service_id"   required>
                                            <option selected disabled="">Please Select Product</option>
                                            <?php foreach ($services as $value) { ?>
                                                <option value="<?php echo $value->id; ?>"  <?php
                                                if (set_value('service_id') == $value->id) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo $value->services; ?></option>
                                                    <?php } ?>
                                        </select>
                                        <?php echo form_error('service_id'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php
                                if (form_error('category_id')) {
                                    echo 'has-error';
                                }
                                ?>">
                                    <label for="category_id" class="col-md-4">Appliance Category *</label>
                                    <div class="col-md-6">                                        
                                        <select name="category_id" class="form-control" id="category_id" required>
                                            <option selected disabled="">Please Select Category</option>
                                            <?php foreach ($categories as $value) { ?>
                                                <option value="<?php echo $value->id; ?>"  <?php
                                                if (set_value('category_id') == $value->id) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo $value->name; ?></option>
                                                    <?php } ?>
                                        </select>
                                        <?php echo form_error('category_id'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php
                                if (form_error('capacity_id')) {
                                    echo 'has-error';
                                }
                                ?>">
                                    <label for="capacity_id" class="col-md-4">Appliance Capacity *</label>
                                    <div class="col-md-6">
                                        <select name="capacity_id" class="form-control" id="capacity_id" required>
                                            <option selected disabled="">Please Select Capacity</option>
                                            <?php foreach ($capacities as $value) { ?>
                                                <option value="<?php echo $value->id; ?>"  <?php
                                                if (set_value('capacity_id') == $value->id) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo $value->name; ?></option>
                                                    <?php } ?>
                                        </select>
                                        <?php echo form_error('capacity_id'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                <center>
                    <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
                </center>
            </div>
        </form>
    </div>
</div>
<script>
    $('#service_id, #category_id, #capacity_id').select2();
    
</script>