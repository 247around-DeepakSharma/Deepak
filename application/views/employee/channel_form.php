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
                <input type="channel" class="form-control" id="channel" onkeypress="check_space()" placeholder="Enter channel Name" name="channel" required >
                <span style="color:red; display: none;" id="chanal_err">No blank spaces.</span>
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
        
        function check_space(e){
        var numericVal = document.getElementById("channel").value;

        if(isNaN(numericVal) || numericVal == "" || numericVal == null || numericVal.indexOf(' ') >= 0) {
            $("#chanal_err").css('display','block');
            return false;
        } 
        
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

