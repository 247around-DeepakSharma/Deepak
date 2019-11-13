<div id="page-wrapper" >
    <div class="container col-md-12" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" >
            <div class="panel-heading" >
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
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
    </div>
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >Update Miscellaneous Charges</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form class="form-horizontal" enctype="multipart/form-data" id ="misc_charges" action="<?php echo base_url();?>employee/service_centre_charges/process_upload_misc_charges/<?php echo $booking_id;?>"  method="POST" >
                        <div class="col-md-12" >
                            <?php foreach ($data as $key => $value) { ?>
                                
                           
                            <div class="static-form-box" id="<?php echo "static-form-box_".$value['id'];?>">
                                <div class="col-md-3" >
                                    <div class="form-group col-md-12  ">
                                        <label for="product serices">Product/Service </label>
                                        <select name="<?php echo "misc[".$value['id']."][product_or_services]"; ?>" class="form-control" id="<?php echo "product_or_services_".$value['id'];?>" required>
                                            <option value="Service" <?php if($value['product_or_services'] == "Service"){ echo "selected";}?>>Service</option>
                                            <option value="Product" <?php if($value['product_or_services'] == "Product"){ echo "selected";}?>>Product</option>
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" >
                                    <div class="form-group col-md-12  ">
                                        <label for="Service Description">Description </label>
                                        <input type="text" class="form-control" id="<?php echo "description_".$value['id'];?>" 
                                               placeholder="Enter Description" name="<?php echo "misc[".$value['id']."][description]"; ?>" 
                                               value = "<?php echo $value['description'];?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4 " style="width:17%;">
                                    <div class="form-group col-md-12  ">
                                        <label for="Vendor Amounts">Vendor Charge </label>
                                        <input type="number" step=".02" class="form-control" id="<?php echo "vendor_charge_".$value['id'];?>" placeholder="Vendor Amount" name="<?php echo "misc[".$value['id']."][vendor_charge]"; ?>" value = "<?php echo $value['vendor_basic_charges'];?>" >
                                    </div>
                                </div>
                                <div class="col-md-4 " style="width:17%;">
                                    <div class="form-group col-md-12  ">
                                        <label for="partner Charge">Partner Charge </label>
                                        <input type="number" step=".02" class="form-control" id="<?php echo "partner_charge_".$value['id'];?>" placeholder="Enter Partner Charge" name="<?php echo "misc[".$value['id']."][partner_charge]"; ?>" value = "<?php echo $value['partner_charge'];?>" >
                                    </div>
                                </div>
                                <div class="col-md-1 text-center" style="margin-top:20px;">
                                    <div class="form-group col-md-12  ">
                                        <button type="button" class="btn btn-default removeButton" onclick="removeitem('<?php echo $value['id'];?>', '<?php echo $booking_id;?>')"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <?php  }?>
                            <div class="col-md-6" >
                                    <div class="form-group col-md-12  ">
                                        <label for="booking ID">Booking ID * </label>
                                        <input type="text" class="form-control" id="booking_id" placeholder="Enter Booking ID" name="booking_id" value = "<?php echo $value['booking_id'];?>" required>
                                    </div>
                                </div>
                            <div class="col-md-5 ">
                                <div class="form-group col-md-12  ">
                                    <label for="remarks">Remarks *</label>
                                    <input type="text" class="form-control" id="remarks" placeholder="Enter Remarks" name="remarks" value = "<?php echo $value['remarks'];?>" required>
                                </div>
                            </div>
                            <div class="col-md-11 ">
                                <div class="form-group col-md-12  ">
                                     <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                $required = false;
                                                if (!empty($value['approval_file'])) {
                                                    //Path to be changed
                                                    $src = S3_WEBSITE_URL."misc-images/" . $value['approval_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                    $required = true;
                                                }
                                                ?>
                                    <label for="detailed Invoice">Approval/Email File </label>
                                    <input type="file" class="form-control" id="approval_misc_charges_file" name="approval_misc_charges_file" <?php if($required) { "required";}?>  >
                                    <input type="hidden" name="file_required" value="<?php if($required) { echo "1";} else { echo "0";}?>" >
                                </div>
                                
                            </div>
                            <div class="col-md-1 text-center" style="margin-top:20px;">
                                    <div class="form-group col-md-12  ">

                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            
                                       
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-xs-5 col-md-4 col-md-offset-5">
                                    <button type="submit" class="btn btn-success" id="submit_btn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
if($this->session->userdata('success')){$this->session->unset_userdata('success');}
if($this->session->userdata('error')){$this->session->unset_userdata('error');}
?>

<script>
function removeitem(id, booking_id){
    $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>/employee/service_centre_charges/cancel_misc_charges/'+id +"/"+booking_id,
       data: {},
       success: function (data) {
          if(data === "success"){
              alert("Charges Removed");
              location.reload(true);
          } else {
              alert("Please refresh and tyy again");
          }

       }
    });
}
</script>
