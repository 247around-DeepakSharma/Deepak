<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="row">
                <h1 class="col-md-6 col-sm-12 col-xs-12"><b> Add New Symptom </b></h1>
                <?php if($this->session->userdata('user_group') != 'closure'){?>
                    <div class="col-md-6 col-sm-12 col-xs-12" style="float:right;margin-top: 30px;margin-bottom: 10px;">
                        <a href="<?php echo base_url();?>employee/booking_request/symptom_list"><input class="btn btn-primary pull-right" type="Button" value="View Symptom"></a>
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
                <form class="form-horizontal"  id="fileinfo" action="<?php echo base_url();?>employee/booking_request/process_add_new_symptom" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group <?php if( form_error('partner_id') ) { echo 'has-error';} ?>" >
                        <label for="excel" class="col-md-2">Select&nbsp;Partner *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="partner_id" required="" name="partner_id" onchange='get_appliance()'>
                                <option selected disabled  >Select Partner</option>
                            </select>
                        </div>
                        <?php echo form_error('partner_id'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>" >
                        <label for="excel" class="col-md-2">Select&nbsp;Service *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="service_id" required="" name="service_id">
                                <option selected disabled  >Select Service</option>
                            </select>
                        </div>
                        <?php echo form_error('service_id'); ?>
                    </div>
                    <div class="form-group <?php if( form_error('symptom') ) { echo 'has-error';} ?>">
                        <label for="excel" class="col-md-2">Select&nbsp;Symptom *</label>
                        <div class="col-md-4">
                            <textarea class="form-control" id="symptom" name="symptom" rows="4"></textarea>
                        </div>
                        <?php echo form_error('symptom'); ?>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-2">
                            <input type="submit"  class="btn btn-success btn-md" id="submit_btn" value ="Add Symptom" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#partner_id').select2();
    $('#service_id').select2();

    get_partner();
    
    function get_appliance(){
        var partner_id=$("#partner_id").val();
        $('#select2-service_id-container').text('Select Service');
        $('#select2-service_id-container').attr('title','Select Service');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/booking/get_appliances/0',
            data: {'partner_id':partner_id},
            success: function (response) {
                response=JSON.parse(response);
                if(response.services){
                    $('#service_id').html(response.services);
                }
            }
        });
       
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id option:selected').removeAttr("selected");
                $('#partner_id option:eq(0)').prop("selected",true);
            }
        });
    }

</script>
<?php  if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php  if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>

