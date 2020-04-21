
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
    <div class="panel-heading">Update Channel</div>

    <div class="panel-body">
        <form action="<?php echo base_url();?>employee/partner/process_update_channel/<?php echo $fetch_data[0]['id'];?>" method="POST">
             <div class="form-group">
                <label for="Partner">Partner</label>
                <select name = "partner_id" class="form-control" id="partner_id">
                    <option value="">Please Select Partner Name</option>
                        
                      </select>
  
                <?php echo form_error('Select'); ?>
                
            </div>
            <div class="form-group  <?php if( form_error('channel') ) { echo 'has-error';} ?>">
                <label for="channel">Channel *</label>
                <input type="channel" class="form-control" value="<?php echo $fetch_data[0]['channel_name'];?>"id="channel" placeholder="Enter Channel Name" name="channel" required>
                <?php echo form_error('channel'); ?>
            </div>
           
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</div>
</div>
</body>
</html>
<script>
    
    get_partner_list(); 
    function get_partner_list(){
       $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/partner',
                data: {vendor_partner_id: '<?php echo $fetch_data[0]['partner_id'];?>', invoice_flag: 1},
                success: function (data) {
                 $("#partner_id").select2().html(data).change();
                 if(!$("#partner_id").val()){
                    $("#partner_id").select2().val("All").change(); 
                 }
                 
            }
        });
        }
</script>


<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>