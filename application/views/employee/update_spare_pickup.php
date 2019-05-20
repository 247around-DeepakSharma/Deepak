<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>Update PickUp </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form method="POST" action="<?php echo base_url();?>employee/booking/download_booking_bulk_search_snapshot">
                        <div class="form-group">
                            <label for="model_number">PickUp From *</label>
                            <select class="form-control" name="vendor_partner_type" id="vendor_partner_type">
                                <option value="" selected=" disabled">Select Pick From</option>
                                <option value="<?php echo _247AROUND_PARTNER_STRING?>">Partner</option>
                                <option value="<?php echo _247AROUND_SF_STRING;?>">Vendor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="model_number" >Enter Booking ID *</label>
                            <textarea class="form-control" rows="5" id="bulk_input" name="bulk_input" placeholder="Enter Booking ID"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-small btn-success" id="search" onclick="loadData()">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function loadData(){
        select_type = document.getElementById("vendor_partner_type").value;
        bulk_input = document.getElementById("bulk_input").value;
        bulkInputArray = bulk_input.replace( /\n/g, " " ).split( " " );
        if(bulkInputArray.length>500){
            alert("Search Input Should be less then 50");
        } else if(select_type && bulk_input){
            var fd = new FormData(document.getElementById("fileinfo"));
            fd.append("label", "WEBUPLOAD");
            fd.append("vendor_partner",select_type);
            fd.append("booking_id",bulkInputArray);

            $.ajax({
                url: "<?php echo base_url() ?>employee/spare_parts/process_update_pickup_for_booking",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response ==='Success'){
                        
                        alert('Sucessfuly Updated');
                        location. reload(true);
                    } else {
                        alert('There is issue to update booking');
                    }
                    $('body').loadingModal('destroy');
                    
                }
            });
        } else{
           alert("Please provide Booking ID and Select pickup from");
           return false;
        }
    }
    
    
</script>