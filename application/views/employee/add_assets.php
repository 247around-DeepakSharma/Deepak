<div class="container">
<?php if($this->session->userdata('success')) {
    echo '<div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>' . $this->session->userdata('success') . '</strong>
    </div>';
    }
    ?>
<?php if($this->session->userdata('error')) {
    echo '<div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>' . $this->session->userdata('error') . '</strong>
    </div>';
    }
    ?>
<div class="panel panel-info">
    <div class="panel-heading">Add Asset</div>
    <div class="panel-body">
        <form action="<?php echo base_url();?>employee/assets/process_add_assets" method="POST">
            <div class="form-group  <?php if( form_error('assests') ) { echo 'has-error';} ?>">
                <label for="assets">Assets *</label>
                <input type="assets" class="form-control" id="assets" placeholder="Enter Assets Name" name="assets" required>
                <?php echo form_error('assets'); ?>
            </div>
            <div class="form-group"  <?php if( form_error('serial_no') ) { echo 'has-error';} ?>">
                <label for="serial_no ">Serial Number *</label>
                <input type="serial_no" class="form-control" id="serial_no" placeholder="Enter Serial Number" name="serial_no" required>
                <?php echo form_error('serial_no'); ?>
            </div>
            <div class="form-group">
                <label for="Employee">Employee</label>
                <select name = "employee_id" class="form-control">
                    <option value="">Please Select Employee Name</option>
                        <?php
                        foreach ($data as $key => $value) {
                            ?>
                        
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['full_name']; ?></option>
                       
                            
                        <?php }
                        ?>

                      </select>
  
                <?php echo form_error('Select'); ?>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</div>
</body>
</html>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>