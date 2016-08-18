<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<?php  $phone_number = $this->uri->segment(3);  ?>
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/partner/process_addbooking"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">User Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-4">User Name</label>
                                    <div class="col-md-6">
                                     <input type="hidden" class="form-control" id="user_id" name="user_id" value = "<?php if(isset($user[0]['user_id'])){ echo $user[0]['user_id']; }  ?>"  >
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($user[0]['name'])){ echo $user[0]['name']; }  ?>" <?php if(isset($user[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_primary_contact_no" class="col-md-4">Primary Contact Number *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if(isset($user[0]['phone_number'])){ echo $user[0]['phone_number']; } else { echo $phone_number; }  ?>" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_city" class="col-md-4">Booking City *</label>
                                    <div class="col-md-6">
                                        <select type="text" onchange= "getCategory()" class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php 
                                                foreach ($city as $key => $cites) { ?>
                                            <option <?php if(isset($user[0]['city'])){ if($cites['district'] == $user[0]['city']){ echo "Selected"; } }?>><?php echo $cites['district']; ?></option>
                                            <?php  }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_pincode" class="col-md-4">Booking Pincode *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($user[0]['pincode'])){echo $user[0]['pincode'];} ?>" placeholder="Enter Area Pin" required>
                                    </div>
                                </div>
                                  <div class="form-group ">
                                    <label for="booking_pincode" class="col-md-4">Near Landmark </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(isset($user[0]['landmark'])){echo $user[0]['landmark'];} ?>" placeholder="Enter Any Near Landmark" required>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label  class="col-md-4">User Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(isset($user[0]['user_email'])){  echo $user[0]['user_email'];  }  ?>" placeholder="Please Enter User Email">
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate Contact No</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php if(isset($user[0]['booking_alternate_contact_no'])){  echo $user[0]['alternate_phone_number']; } ?>" placeholder ="Please Enter Alternate Contact No" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  for="booking_address" class="col-md-4">Booking Address *</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="6" id="booking_address" name="booking_address"  required ><?php if(isset($user[0]['home_address'])){  echo $user[0]['home_address']; } ?></textarea>
                                    </div>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Order Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                              <div class="form-group ">
                                    <label for="order id" class="col-md-4">Order ID *</label>
                                    <div class="col-md-6">
                                        <input class="form-control" name= "order_id" value="<?php if(isset($user[0]['order_id'])){  echo $user[0]['order_id']; } ?>" placeholder ="Please Enter Order ID" id="order_id"  ></input>
                                    </div>
                                </div>
                              <div class="form-group ">
                                    <label for="service_name" class="col-md-4">Service Name *</label>
                                    <div class="col-md-6">
                                       
                                        <select type="text" class="form-control"  id="service_name" name="service_name"   required>
                                            <option selected disabled>Select Service</option>
                                            <?php foreach ($services as $key => $values) { ?>
                                            <option  value=<?= $values->services; ?>>
                                                <?php echo $values->services; }    ?>
                                            </option>
                                            
                                        </select>
                                    </div>
                                </div>
                              

                            
                               
                                
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                             <div class="form-group ">
                                    <label for="source_name" class="col-md-4">Booking Source *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="booking_source form-control"  id="partner_source" name="partner_source" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <option>CallCenter</option>
                                            <option>Snapdeal</option>
                                            <option>Flipkart</option>
                                            <option>Ebay</option>
                                            <option>Offline</option>
                                        </select>
                                    </div>
                                </div>
                           <div class="form-group ">
                                    <label for="booking_date" class="col-md-4">Booking Date *</label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control"  id="booking_date" name="booking_date"   value = "<?php echo  date("Y-m-d", strtotime("+1 day")); ?>"  >
                                        <!-- min="<?php echo date("Y-m-d", strtotime("+1 day")) ?>"  -->
                                    </div>
                                </div>
                               
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row End  -->
            <div class="clonedInput panel panel-info " id="clonedInput1">
                <!--  <i class="fa fa-plus addsection pull-right fa-3x" aria-hidden="true" style ="margin-top:15px; margin-bottom: 15px; margin-right:40px; "></i>
                    <i class="fa fa-times pull-right deletesection  fa-3x"  style ="margin-top:15px; margin-bottom: 15px; margin-right:20px; " aria-hidden="true"></i>-->
                <div class="panel-heading">
                    Product Description
                   
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="brand" class="col-md-4">Brand *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required>
                                            <option selected disabled>Select Brand</option>
                                          <?php if($this->session->userdata('partner_id') == "247011"){ ?>
                                          <option selected="selected">Ray</option>

                                        <?php } else if($this->session->userdata('partner_id') == "247010"){ ?>
                                         <option selected="selected">Wybor</option>
                                        <?php    } ?>
                                        </select>
                                    </div>
                                </div>
                                <input hidden="text" name="partner_id" id="partner_id" value="<?php echo $this->session->userdata('partner_id') ; ?>"></input>
                                <div class="form-group">
                                    <label for="category" class="col-md-4">Category *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required>
                                            <option selected disabled>Select Appliance Category</option>
                                            <option>TV-LED</option>
                                            <option>TV-LCD</option>
                                        </select>
                                    </div>
                                </div>
                                  <div class="form-group ">
                                    <label for="capacity" class="col-md-4">Capacity *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" >
                                            <option selected disabled>Select Appliance Capacity</option>
                                            <?php  for($i=16; $i<61; $i++){ ?>
                                            <option><?php echo $i." Inch"; ?></option>
                                            <?php } ?>
                                        </select>
                                       
                                    </div>
                                </div>

                                 <div class="form-group ">
                                    <label for="call type" class="col-md-4">Call Type *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control price_tags"   id="price_tag" name="price_tag" required>
                                            <option selected disabled>Select Call Type</option>
                                            <option>Installation & Demo</option>
                                            <option>Repair - In Warranty</option>
                                            <option>Repair - Out Of Warranty</option>
                                        </select>
                                       
                                    </div>
                                </div>
                  
                                
                                
                            </div>
                            <div class="col-md-6">
                                           
                                <div class="form-group <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Appliance Model </label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control"  name="model_number" id="model_number_1" value = "<?php echo set_value('model_number'); ?>" placeholder="Enter Model" >
                                        <?php echo form_error('model_number'); ?>
                                    </div>
                                </div>
                                  <div class="form-group ">
                                    <label for="type" class="col-md-4">Serial Number *</label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control"  name="serial_number" id="serial_number_1" value = "" placeholder="Enter Serial Number" >
                                       
                                    </div>
                                </div>
                                 <div class="form-group ">
                                    <label for="type" class="col-md-4">Description *</label>
                                    <div class="col-md-6">
                                        <textarea type="text" class="form-control"  name="description" id="description" value = "" placeholder="Enter Product Description" ></textarea>   
                                       
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="type" class="col-md-4">Purchase Year</label>
                                    <div class="col-md-4">
                                        <select  type="text" class=" form-control "   name="purchase_month" id="purchase_month_1" >
                                            <option selected="selected" value="">Month</option>
                                            <option <?php if(date("m") == "01"){echo "selected";} ?> >Jan</option>
                                            <option <?php if(date("m") == "02"){echo "selected";} ?>>Feb</option>
                                            <option <?php if(date("m") == "03"){echo "selected";} ?>>Mar</option>
                                            <option <?php if(date("m") == "04"){echo "selected";} ?>>Apr</option>
                                            <option <?php if(date("m") == "05"){echo "selected";} ?>>May</option>
                                            <option <?php if(date("m") == "06"){echo "selected";} ?>>Jun</option>
                                            <option <?php if(date("m") == "07"){echo "selected";} ?>>July</option>
                                            <option <?php if(date("m") == "08"){echo "selected";} ?>>Aug</option>
                                            <option <?php if(date("m") == "09"){echo "selected";} ?>>Sept</option>
                                            <option <?php if(date("m") == "10"){echo "selected";} ?>>Oct</option>
                                            <option <?php if(date("m") == "11"){echo "selected";} ?>>Nov</option>
                                            <option <?php if(date("m") == "12"){echo "selected";} ?>>Dec</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <select  type="text" class="col-md-3 form-control "   name="purchase_year" id="purchase_year_1" required>
                                                <option selected="selected" value="" >Year</option>
                                                <?php for($i = 0; $i> -26; $i--){ ?>
                                                <option <?php if(date("Y",strtotime($i." year")) == "2016"){ echo "selected";} ?> >
                                                    <?php echo date("Y",strtotime($i." year")); ?>
                                                </option>
                                                <?php }  ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cloned"></div>
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="form-group ">
                        <label for="type" class="col-md-2">Problem Description</label>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="5" name="query_remarks"  placeholder="Enter Problem Description" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group  col-md-12" >
                    <center>
                       
                        <input type="submit" id="submitform" class="btn btn-info " value="submit">
                </div>
                </center>
            </div>
        </form>
        <!-- end Panel Body  -->
    </div>
</div>
</div>
</div>

<script>


   (function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#booking_form").validate({
                rules: {
                    user_name: "required",
                    city: "required",
                    booking_primary_contact_no: {
                        required: true,
                        minlength: 10
                    },
                   
                    state: "required",
                    booking_pincode: {
                        required: true,
                        minlength: 6
                    },
                    
                    user_email: {

                        email: true
                    },
                    booking_address: "required",   
                    appliance_capacity: "required"
                },
                messages: {
                    user_name: "Please Enter Customer  Name",
                    booking_primary_contact_no: "Please Enter Customer Phone Number",
                    city: "Please Select City",
                    state: "Please Select State",
                    booking_pincode: "Please Enter Correct Pincode",
                    user_email: "Please fill correct email",
                    booking_address: "Please fill Customer Address",
                   
                    appliance_capacity: "Please Enter Capacity"
                   
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);

</script>
<style type="text/css">
    #errmsg1
    {
    color: red;
    }

    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 4px 0 5px 0px;
    padding: 0;
    text-align: left;
    width: 220px;
}
</style>

