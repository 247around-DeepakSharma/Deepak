<div id="page-wrapper" >
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >
                Search GST Number: 
            </div>
            <div class="panel-body">
                <?php if (isset($gst_not_found) && !empty($gst_not_found)) { ?>
                <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>GST number not found - <?php print_r($gst_not_found); ?></strong>
                </div>
                <?php } ?>
                <form class="form-horizontal" onsubmit="return check_validation()"  method="POST" action="<?php echo base_url(); ?>employee/vendor/seach_gst_number">
                    <div class="form-group">
                        <label for="gst_number" class="col-md-1"> GST Number</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="gst_number" value="" id="gst_number" style="text-transform: uppercase" placeholder="You can enter multiple GST number separated by comma"/>
                        </div>
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-primary" value="Search"> 
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php if (isset($data) && !empty($data)) { ?>
        <div class="container col-md-12" >
            <div class="panel panel-info" >
                <div class="panel-heading" >GST Number Detail</div>
                <div class="panel-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>GST Number</th>
                                <th>Legal Name of Business</th>
                                <th>Trade Name</th>
                                <th>Effective Date of Registration</th>
                                <th>Constitution of Business</th>
                                <th>GSTIN / UIN Status</th>
                                <th>Taxpayer Type</th>
                                <th>Principal Place of Business</th>
                                <th>Effective Date of Cancellation</th>
                            </tr>
                        </thead>
                        <tbody>
             <?php   foreach ($data as $value){ ?>
                    <tr>
                        <td><?php echo $value['gst_number']; ?></td>
                        <td><?php echo $value['legal_name']; ?></td>
                        <td><?php echo $value['company_name']; ?></td>
                        <td><?php echo $value['registration_date']; ?></td>
                        <td><?php echo $value['constitution_of_business']; ?></td>
                        <td><?php echo $value['status']; ?></td>
                        <td><?php echo $value['type']; ?></td>
                        <td><?php echo $value['address_readable']; ?></td>
                        <td><?php echo isset($value['cancellation_date']) ? $value['cancellation_date'] : ''; ?></td>                        
                    </tr>
               <?php } ?>
                   </tbody>
                     </table>
                </div>
                </div>
            </div>
        </div>
   
<?php } ?>
</div>
<script>
    function check_validation(){
        var gstin = $("#gst_number").val().toUpperCase();
        if(gstin){
            while(gstin.slice(-1) === ','){
               gstin = gstin.slice(0, -1); 
            }
            gstin = gstin.split(",");
            for(var i=0; i<gstin.length; i++){
                if(/^[A-Z0-9,]*$/.test(gstin[i]) === true){
                    if(gstin[i].length != 15){
                        alert("GST Number should have only 15 digit - "+ gstin[i]);  
                        return false;
                    }
                }
                else{
                   alert("Special Character Not Allowed - "+ gstin[i]); 
                   return false;
                }
            }
        }
        else{
            alert("Enter GST Number");
            return false;
        }
    }
</script>
