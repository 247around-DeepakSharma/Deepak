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
        <div class="panel-heading">Partner Channel</div>
        <form action="<?php echo base_url();?>employee/partner/process_add_channel" method="POST">
        <div class="panel-body">
            <div class="form-group">
                <label for="Partner">Partner</label>
                <select name= "partner_id" class="form-control" id="partner_id" required>
                    <option value="">Please Select Partner Name</option>
                        
                 </select>
                </div>
         
            <div class="form-group  <?php if( form_error('channel') ) { echo 'has-error';} ?>">
                <label for="channel">Channel</label>
                <input type="text" class="form-control" id="channel"placeholder="Enter channel Name" name="channel" required >
                    <?php echo form_error('channel'); ?>
            </div>
             <button type="submit" class="btn btn-default">Submit</button>
         
            
        </div>
            </form>
    </div>
</div>
<script>
    
       get_partner_list(); 
    
    function get_partner_list(){

       $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/partner',
                data: {vendor_partner_id: "", invoice_flag: 0},
                success: function (data) {
                 $("#partner_id").select2().html(data).change();
            }
        });
        }
        
        
        
//        
//    $(function() {
//        $('#channel').on('keypress', function(e) {
//        if (e.which == 32)
//        $("#chanal_err").css('display','block');
//        return false;
//        });
//    });
</script>

