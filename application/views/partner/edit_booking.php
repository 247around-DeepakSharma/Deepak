
<div id="page-wrapper" >
    <div class="container-fluid" >
        <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>partner/process_update_booking/<?php echo $booking_history[0]['booking_id']; ?>"  method="POST" enctype="multipart/form-data">
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">User Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if($this->session->userdata('success')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
                                }
                                ?>
                                <?php
                if ($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible partner_error" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
                }
                ?>
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('user_name') ) { echo 'has-error';} ?>">
                                    <label for="name" class="col-md-4">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="name" name="user_name" value = "<?php if(isset($booking_history[0]['name'])){ echo $booking_history[0]['name']; } else { echo set_value('user_name'); }  ?>" <?php if(isset($booking_history[0]['name'])){ echo "readonly"; }  ?> placeholder="Please Enter User Name">
                                        <?php echo form_error('user_name'); ?>
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" value="<?php if($booking_history){ echo $booking_history[0]['user_id']; }?>"/>
                                <input type="hidden" name="appliance_id" value="<?php if($unit_details){ echo $unit_details[0]['appliance_id']; }?>"/>
                                
                                <div class="form-group <?php if( form_error('booking_primary_contact_no') ) { echo 'has-error';} ?>">
                                    <label for="booking_primary_contact_no" class="col-md-4">Mobile *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php if(isset($booking_history[0]['booking_primary_contact_no'])){ echo $booking_history[0]['booking_primary_contact_no']; } ?>" required>
                                        <?php echo form_error('booking_primary_contact_no'); ?>
                                    </div>
                                    <span id="error_mobile_number"></span>
                                </div>
                                <div class="form-group <?php if( form_error('city') ) { echo 'has-error';} ?>">
                                    <label for="booking_city" class="col-md-4">City *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="booking_city" name="city" required>
                                            <option selected="selected" disabled="disabled">Select City</option>
                                            <?php 
                                                foreach ($city as $key => $cites) { ?>
                                            <option <?php if(isset($booking_history[0]['city'])){ if($cites['district'] == $booking_history[0]['city']){ echo "Selected"; } }?>><?php echo $cites['district']; ?></option>
                                            <?php  }
                                                ?>
                                        </select>
                                        <?php echo form_error('city'); ?>
                                    </div>
                                    <span id="error_city" style="color: red;"></span>
                                </div>
                                <div class="form-group <?php if( form_error('booking_pincode') ) { echo 'has-error';} ?>">
                                    <label for="booking_pincode" class="col-md-4">Pincode *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="booking_pincode" name="booking_pincode" value = "<?php if(isset($booking_history[0]['booking_pincode'])){echo $booking_history[0]['pincode'];} else { echo set_value('booking_pincode');} ?>" placeholder="Enter Area Pin" required>
                                        <?php echo form_error('booking_pincode'); ?>
                                    </div>
                                    <span id="error_pincode" style="color: red;"></span>
                                </div>
                                <div class="form-group <?php if( form_error('landmark') ) { echo 'has-error';} ?>">
                                    <label for="booking_pincode" class="col-md-4"> Landmark</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="landmark" name="landmark" value = "<?php if(isset($booking_history[0]['booking_landmark'])){echo $booking_history[0]['booking_landmark'];} else { echo set_value('landmark');} ?>" placeholder="Enter Any Landmark">
                                        <?php echo form_error('landmark'); ?>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6 <?php if( form_error('user_email') ) { echo 'has-error';} ?>">
                                <div class="form-group ">
                                    <label  class="col-md-4">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control"  id="booking_user_email" name="user_email" value = "<?php if(isset($booking_history[0]['user_email'])){  echo $booking_history[0]['user_email'];  }  ?>" placeholder="Please Enter User Email">
                                        <?php echo form_error('user_email'); ?>
                                    </div>
                                </div>
                                <div class="form-group  <?php if( form_error('alternate_phone_number') ) { echo 'has-error';} ?>">
                                    <label for="booking_alternate_contact_no" class="col-md-4">Alternate Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control booking_alternate_contact_no"  id="booking_alternate_contact_no" name="alternate_phone_number" value = "<?php if(isset($booking_history[0]['booking_alternate_contact_no'])){  echo $booking_history[0]['booking_alternate_contact_no']; } else { echo set_value('booking_alternate_contact_no');} ?>" placeholder ="Please Enter Alternate Contact No" >
                                    </div>
                                     <?php echo form_error('alternate_phone_number'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('booking_address') ) { echo 'has-error';} ?>">
                                    <label  for="booking_address" class="col-md-4">Address *</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="6" id="booking_address" name="booking_address" placeholder="Please Enter Address"  required ><?php if(isset($booking_history[0]['booking_address'])){  echo $booking_history[0]['booking_address']; } else { echo set_value('booking_address'); } ?></textarea>
                                        <?php echo form_error('booking_address'); ?>
                                    </div>
                                    <span id="error_address" style="color: red"></span>
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
                    Product Details
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('order_id') ) { echo 'has-error';} ?>">
                                    <label for="order id" class="col-md-4">Order ID </label>
                                    <div class="col-md-6">
                                        <input class="form-control" name= "order_id" value="<?php if(isset($booking_history[0]['order_id'])){  echo $booking_history[0]['order_id']; } else { echo set_value('order_id');} ?>" placeholder ="Please Enter Order ID" id="order_id"  />
                                    </div>
                                    <?php echo form_error('order_id'); ?>
                                    <span id="error_order_id"></span>
                                </div>
                                <div class="form-group <?php if( form_error('service_name') ) { echo 'has-error';} ?>">
                                    <label for="service_name" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control"  id="service_name" name="service_name"   required>
                                            <option selected disabled>Select Appliance</option>
                                            <!--<?php foreach ($services as $key => $values) { ?>
                                                <option  value=<?= $values->services; ?>>
                                                    <?php echo $values->services; }    ?>
                                                </option>-->
                                            <option value="Television" selected="selected">Television</option>
                                        </select>
                                        <?php echo form_error('service_name'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                                    <label for="brand" class="col-md-4">Brand *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_brand"    name="appliance_brand" id="appliance_brand_1" required>
                                            <option selected disabled>Select Brand</option>
                                            <?php if($this->session->userdata('partner_id') == "247011"){ ?>
                                            <option selected="selected">Ray</option>
                                            <?php } else if($this->session->userdata('partner_id') == "247010"){ ?>
                                            <option <?php if(set_value('appliance_brand') == "Wybor"){ echo "selected";}
                                            else if($unit_details[0]['appliance_brand']== "Wybor"){ echo "selected"; }
                                            ?> >Wybor</option>
                                            <option <?php if(set_value('appliance_brand') == "EgoVision"){ echo "selected";} else
                                                if($unit_details[0]['appliance_brand']== "EgoVision"){ echo "selected"; }?>>EgoVision</option>
                                            <option <?php if(set_value('appliance_brand') == "Belco"){ echo "selected";} else if($unit_details[0]['appliance_brand']== "Belco"){ echo "selected"; } ?>>Belco</option>
                                            <?php } else if($this->session->userdata('partner_id') == "247013"){ ?>
                                              <option selected="selected">Nacson</option>
                                              <?php } else if($this->session->userdata('partner_id') == "247014"){ ?>
                                                <option selected="selected">BoschDelon</option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <input hidden="text" name="partner_id" id="partner_id" value="<?php echo $this->session->userdata('partner_id') ; ?>"/>
                                <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                                    <label for="category" class="col-md-4">Category *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_category"   id="appliance_category_1" name="appliance_category"   required>
                                            <option selected disabled>Select Appliance Category</option>
                                            <option <?php  if(set_value('appliance_category') == "TV-LED"){ echo "selected";} else
                                                 if($unit_details[0]['appliance_category']== "TV-LED"){ echo "selected"; }?>>TV-LED</option>
                                            <option <?php if(set_value('appliance_category') == "TV-LCD"){ echo "selected";}else
                                                if($unit_details[0]['appliance_category']== "TV-LCD"){ echo "selected"; }  ?>>TV-LCD</option>
                                        </select>
                                        <?php echo form_error('appliance_category'); ?>
                                    </div>
                                    <span id="error_category" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                                    <label for="capacity" class="col-md-4">Capacity *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control appliance_capacity"   id="appliance_capacity_1" name="appliance_capacity" >
                                            <option selected disabled>Select Appliance Capacity</option>
                                            <?php  for($i=16; $i<61; $i++){ ?>
                                            <option <?php if(set_value('appliance_capacity') == $i." Inch" ){ echo "selected";} else
                                                if($unit_details[0]['appliance_capacity']==  $i." Inch"){ echo "selected"; }  ?>><?php echo $i." Inch"; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('appliance_capacity'); ?>
                                    </div>
                                    <span id="error_capacity" style="color: red"></span>
                                </div>
                                                                <div class="form-group <?php if( form_error('model_number') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Model Number</label>
                                    <div class="col-md-6">

                                       <?php  if($this->session->userdata('partner_id') == "247010"){ ?>
                                        <select class="form-control"  name="model_number" id="model_number_1" >
                                            <option value="">Select Model</option>
                                            <option value='E-16' <?php if(set_value('model_number') == "E-16"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-16"){ echo "selected"; }  ?>>E-16</option>
                                            <option value='E-19' <?php if(set_value('model_number') == "E-19"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "E-19"){ echo "selected"; }  ?>>E-19</option>
                                            <option value='E-20' <?php if(set_value('model_number') == "E-20"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-20"){ echo "selected"; }  ?>>E-20</option>
                                            <option value='E-21' <?php if(set_value('model_number') == "E-21"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-21"){ echo "selected"; }  ?>>E-21</option>
                                            <option value='E-22' <?php if(set_value('model_number') == "E-22"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "E-22"){ echo "selected"; }  ?>>E-22</option>
                                            <option value='E-24' <?php if(set_value('model_number') == "E-24"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-24"){ echo "selected"; } ?>>E-24</option>
                                            <option value='E-32' <?php if(set_value('model_number') == "E-32"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-32"){ echo "selected"; }  ?>>E-32</option>
                                            <option value='E-32 SMART' <?php if(set_value('model_number') == "E-32 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-32 SMART"){ echo "selected"; } ?>>E-32 SMART</option>
                                            
                                            <option value='E-40' <?php if(set_value('model_number') == "E-40"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-40"){ echo "selected"; } ?>>E-40</option>
                                            
                                            <option value='E-40 SMART' <?php  if(set_value('model_number') == "E-40 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-40 SMART"){ echo "selected"; } ?>>E-40 SMART</option>
                                            <option value='E-48' <?php  if(set_value('model_number') == "E-48"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-48"){ echo "selected"; }  ?>>E-48</option>
                                            <option value='E-48 SMART' <?php  if(set_value('model_number') == "E-48 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-48 SMART"){ echo "selected"; }  ?>>E-48 SMART</option>
                                            <option value='E-49' <?php if(set_value('model_number') == "E-49"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-49"){ echo "selected"; }  ?>>E-49 </option>
                                            <option value='E-49 SMART' <?php if(set_value('model_number') == "E-49 SMART"){ echo "selected";} else  
                                                if($unit_details[0]['model_number']==  "E-49 SMART"){ echo "selected"; } ?>>E-49 SMART</option>
                                            <option value='E-55 SMART' <?php  if(set_value('model_number') == "E-55 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "E-55 SMART"){ echo "selected"; } ?>>E-55 SMART</option>
                                            <option value='W-16' <?php if(set_value('model_number') == "W-16"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-16"){ echo "selected"; } ?>>W-16</option>
                                           
                                            <option value='W-19' <?php if(set_value('model_number') == "W-19"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-19"){ echo "selected"; } ?>>W-19</option>
                                            <option value='W-20' <?php if(set_value('model_number') == "W-20"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-20"){ echo "selected"; }?>>W-20</option>
                                            <option value='W-21' <?php if(set_value('model_number') == "W-21"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-21"){ echo "selected"; } ?>>W-21</option>
                                            <option value='W-22' <?php if(set_value('model_number') == "W-22"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-22"){ echo "selected"; } ?>>W-22</option>
                                            <option value='W-24' <?php if(set_value('model_number') == "W-24"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-24"){ echo "selected"; } ?>>W-24</option>
                                            <option value='W-32' <?php if(set_value('model_number') == "W-32"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-32"){ echo "selected"; } ?>>W-32</option>
                                            <option value='W-32 SMART' <?php if(set_value('model_number') == "W-32 SMART"){ echo "selected";} else 
                                                 if($unit_details[0]['model_number']==  "W-32 SMART"){ echo "selected"; }?>>W-32 SMART</option>
                                            <option value='W-40' <?php if(set_value('model_number') == "W-40"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-40"){ echo "selected"; }?>>W-40 </option>
                                            <option value='W-40 SMART' <?php if(set_value('model_number') == "W-40 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-40 SMART"){ echo "selected"; }?>>W-40 SMART</option>
                                            <option value='W-48' <?php if(set_value('model_number') == "W-48"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-48"){ echo "selected"; }?>>W-48</option>
                                            <option value='W-48 SMART' <?php if(set_value('model_number') == "W-48 SMART"){ echo "selected";}else
                                                if($unit_details[0]['model_number']==  "W-48 SMART"){ echo "selected"; }?>>W-48 SMART</option>
                                            <option value='W-49' <?php if(set_value('model_number') == "W-49"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "W-49"){ echo "selected"; } ?>>W-49 </option>
                                            <option value='W-49 SMART' <?php if(set_value('model_number') == "W-49 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-49 SMART"){ echo "selected"; }?>>W-49 SMART</option>
                                            <option value='W-55 SMART'  <?php if(set_value('model_number') == "W-55 SMART"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "W-55 SMART"){ echo "selected"; }?>>W-55 SMART</option>
                                            <option value='BL-16' <?php if(set_value('model_number') == "BL-16"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "BL-16"){ echo "selected"; } ?>>BL-16</option>
                                            <option value='BL-19'  <?php if(set_value('model_number') == "BL-19"){ echo "selected";}  else 
                                                if($unit_details[0]['model_number']==  "BL-19"){ echo "selected"; } ?>>BL-19</option>
                                            <option value='BL-20'  <?php if(set_value('model_number') == "BL-20"){ echo "selected";}  else 
                                                if($unit_details[0]['model_number']==  "BL-20"){ echo "selected"; } ?>>BL-20</option>
                                            <option value='BL-21'  <?php if(set_value('model_number') == "BL-21"){ echo "selected";}  else 
                                                if($unit_details[0]['model_number']==  "BL-21"){ echo "selected"; } ?>>BL-21</option>
                                            <option value='BL-22'  <?php if(set_value('model_number') == "BL-22"){ echo "selected";}  else 
                                                if($unit_details[0]['model_number']==  "BL-22"){ echo "selected"; } ?>>BL-22</option>
                                            <option value='BL-24'  <?php if(set_value('model_number') == "BL-24"){ echo "selected";}  else 
                                                if($unit_details[0]['model_number']==  "BL-24"){ echo "selected"; } ?>>BL-24</option>
                                            <option value='BL-32'  <?php if(set_value('model_number') == "BL-32"){ echo "selected";} else 
                                                if($unit_details[0]['model_number']==  "BL-32"){ echo "selected"; } ?>>BL-32</option>
                                            <option value='BL-32 SMART'  <?php if(set_value('model_number') == "BL-32 SMART"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-32 SMART"){ echo "selected"; }   ?>>BL-32 SMART</option>
                                            <option value='BL-40' <?php if(set_value('model_number') == "BL-40"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-40"){ echo "selected"; } ?>>BL-40 </option>
                                            <option value='BL-40 SMART' <?php if(set_value('model_number') == "BL-40 SMART"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-40 SMART"){ echo "selected"; } ?>>BL-40 SMART</option>
                                            <option value='BL-48' <?php if(set_value('model_number') == "BL-48"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-48"){ echo "selected"; } ?>>BL-48</option>
                                            <option value='BL-48 SMART' <?php if(set_value('model_number') == "BL-48 SMART"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-48 SMART"){ echo "selected"; } ?>>BL-48 SMART</option>
                                            <option value='BL-49' <?php if(set_value('model_number') == "BL-49"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-49"){ echo "selected"; } ?>>BL-49 </option>
                                            <option value='BL-49 SMART' <?php if(set_value('model_number') == "BL-49 SMART"){ echo "selected";}else
                                                if($unit_details[0]['model_number']==  "BL-49 SMART"){ echo "selected"; } ?>>BL-49 SMART</option>
                                            <option value='BL-55 SMART' <?php if(set_value('model_number') == "BL-55 SMART"){ echo "selected";} else
                                                if($unit_details[0]['model_number']==  "BL-55 SMART"){ echo "selected"; } ?>>BL-55 SMART</option>
                                        </select>
                                        <?php } else { ?>
                                        <input  type="text" class="form-control"  name="model_number" id="model_number_1" value = "<?php if(set_value('model_number')){ echo set_value('model_number');} else
                                            if($unit_details[0]['model_number']){ echo $unit_details[0]['model_number'];  }  ?>" placeholder="Enter Model" >
                                        <?php } ?>
                                         <?php echo form_error('model_number'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('serial_number') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Serial Number </label>
                                    <div class="col-md-6">
                                        <input  type="text" class="form-control"  name="serial_number" id="serial_number" value = "<?php if(set_value('serial_number')) { echo set_value('serial_number');} else 
                                            if($unit_details[0]['serial_number']){ echo $unit_details[0]['serial_number'];  }  ?>" placeholder="Enter Serial Number" >
                                        <?php echo form_error('serial_number'); ?>
                                    </div>
                                    <span id="error_serial_number"></span>
                                </div>
                                <div class="form-group <?php if( form_error('purchase_month') ) { echo 'has-error';} ?>">
                                    <label for="type" class="col-md-4">Date of Purchase</label>
                                    <div class="col-md-4">
                                        <select  type="text" class=" form-control "   name="purchase_month" id="purchase_month_1" >
                                            <option selected="selected" value="">Month</option>
                                            <option <?php if(set_value('purchase_month') == "Jan"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "Jan"){ echo "selected"; }  ?> >Jan</option>
                                            <option <?php if(set_value('purchase_month') == "Feb"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "Feb"){ echo "selected"; } ?>>Feb</option>
                                            <option <?php if(set_value('purchase_month') == "Mar"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "Mar"){ echo "selected"; } ?>>Mar</option>
                                            <option <?php if(set_value('purchase_month') == "Apr"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "Apr"){ echo "selected"; }?>>Apr</option>
                                            <option <?php if(set_value('purchase_month') == "May"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "May"){ echo "selected"; } ?>>May</option>
                                            <option <?php if(set_value('purchase_month') == "Jun"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "Jun"){ echo "selected"; } ?>>Jun</option>
                                            <option <?php if(set_value('purchase_month') == "July"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "July"){ echo "selected"; } ?> >July</option>
                                            <option <?php if(set_value('purchase_month') == "Aug"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "Aug"){ echo "selected"; } ?>>Aug</option>
                                            <option <?php if(set_value('purchase_month') == "Sept"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "Sept"){ echo "selected"; } ?>>Sept</option>
                                            <option <?php if(set_value('purchase_month') == "Oct"){ echo "selected";} else 
                                                if($unit_details[0]['purchase_month']==  "Oct"){ echo "selected"; } ?>>Oct</option>
                                            <option <?php if(set_value('purchase_month') == "Nov"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "Nov"){ echo "selected"; } ?>>Nov</option>
                                            <option <?php if(set_value('purchase_month') == "Dec"){ echo "selected";}else 
                                                if($unit_details[0]['purchase_month']==  "Dec"){ echo "selected"; } ?>>Dec</option>
                                        </select>
                                        <?php echo form_error('purchase_month'); ?>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <select  type="text" class="col-md-3 form-control "   name="purchase_year" id="purchase_year_1" >
                                                <option selected="selected" value="" >Year</option>
                                                <?php for($i = 0; $i> -26; $i--){ ?>
                                                <option  <?php if(set_value('purchase_year') == date("Y",strtotime($i." year"))){ echo "selected";}
                                                else  if($unit_details[0]['purchase_year']==  date("Y",strtotime($i." year"))){ echo "selected"; } ?> >
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
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">Booking Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('partner_source') ) { echo 'has-error';} ?>">
                                    <label for="source_name" class="col-md-4">Booking Source </label>
                                    <div class="col-md-6">
                                        <select type="text" class="booking_source form-control"  id="partner_source" name="partner_source" required>
                                            <option selected="selected" disabled="disabled">Select Booking Source</option>
                                            <option <?php if(set_value('partner_source') == "CallCenter"){ echo "selected";}else 
                                                if($booking_history[0]['partner_source']==  "CallCenter"){ echo "selected"; } else { echo "selected"; } ?>>CallCenter</option>
                                            <option <?php if(set_value('partner_source') == "Snapdeal"){ echo "selected";}else 
                                                if($booking_history[0]['partner_source']==  "Snapdeal"){ echo "selected"; } else { echo "selected"; } ?>>Snapdeal</option>
                                            <option <?php if(set_value('partner_source') == "Flipkart"){ echo "selected";}else 
                                                if($booking_history[0]['partner_source']==  "Flipkart"){ echo "selected"; } else { echo "selected"; } ?>>Flipkart</option>
                                            <option <?php if(set_value('partner_source') == "Ebay"){ echo "selected";}else 
                                                if($booking_history[0]['partner_source']==  "Ebay"){ echo "selected"; } else { echo "selected"; } ?>>Ebay</option>
                                            <option  <?php if(set_value('partner_source') == "Offline"){ echo "selected";}else 
                                                if($booking_history[0]['partner_source']==  "Offline"){ echo "selected"; } else { echo "selected"; } ?>>Offline</option>
                                        </select>
                                        <?php echo form_error('partner_source'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('booking_date') ) { echo 'has-error';} ?>">
                                    <label for="booking_date" class="col-md-4">Booking Date *</label>
                                    <div class="col-md-6">
                                        <input type="date" min="<?php echo date("Y-m-d", strtotime("+1 day")) ?>" class="form-control"  id="booking_date" name="booking_date"   value = "<?php if(set_value('booking_date')) { echo set_value('booking_date');} else 
                                            if($booking_history[0]['booking_date']){ echo date('Y-m-d', strtotime($booking_history[0]['booking_date']));  } else { echo  date("Y-m-d", strtotime("+1 day")); } ?>"  >
                                        <!--   -->
                                        <?php echo form_error('booking_date'); ?>
                                    </div>
                                </div>
                                <!--  end col-md-6  -->
                            </div>
                            <!--  start col-md-6  -->
                            <div class="col-md-6">
                                <div class="form-group <?php if( form_error('price_tag') ) { echo 'has-error';} ?>">
                                    <label for="call type" class="col-md-4">Call Type *</label>
                                    <div class="col-md-6">
                                        <select type="text" class="form-control price_tags"   id="price_tag" name="price_tag" required>
                                            <option selected disabled>Select Call Type</option>
                                            <option <?php if(set_value('price_tag') == "Installation & Demo"){ echo "selected";} else
                                                if($unit_details[0]['price_tags']==  "Installation & Demo"){ echo "selected"; } ?>>Installation & Demo</option>


                                            <option <?php if(set_value('price_tag') == "Repair - In Warranty"){ echo "selected";} else
                                                if($unit_details[0]['price_tags']==  "Repair - In Warranty"){ echo "selected"; }  ?>>Repair - In Warranty</option>
                                            <option <?php if(set_value('price_tag') == "Repair - Out Of Warranty"){ echo "selected";} else
                                                if($unit_details[0]['price_tags']==  "Repair - Out Of Warranty"){ echo "selected"; } ?>>Repair - Out Of Warranty</option>
                                        </select>
                                        <?php echo form_error('price_tag'); ?>
                                    </div>
                                    <span id="error_call_type" style="color: red;"></span>
                                </div>
                                <!-- end col-md-6 -->
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left:9px;">
                            <div class="form-group <?php if( form_error('query_remarks') ) { echo 'has-error';} ?> ">
                                <label for="type" class="col-md-2">Problem Description</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" rows="5" id="remarks" name="query_remarks"  placeholder="Enter Problem Description" ><?php if(set_value('query_remarks')){ echo set_value('query_remarks'); } else
                                        if($booking_history[0]['booking_remarks']){ echo $booking_history[0]['booking_remarks']; } ?></textarea>
                                    <?php echo form_error('query_remarks'); ?>
                                </div>
                                <span id="error_remarks" style="color: red;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group  col-md-12" >
                    <center>
                        <input type="submit" id="submitform" class="btn btn-info " onclick="return check_vakidation()" value="Submit Booking">
                        </center>
                </div>
                
            </div>
        </form>
         
        <!-- end Panel Body  -->
    </div>
</div>
</div>
</div>
<script type="text/javascript">
    function check_vakidation(){
        var order_id =  $('#order_id').val();
        var booking_address = $('#booking_address').val();
        var mobile_number = $('#booking_primary_contact_no').val();
        var city = $('#booking_city').val();
        var pincode = $('#booking_pincode').val();
        var serial_number = $('#serial_number').val();
        var category = $('#appliance_category_1').val();
        var capacity = $('#appliance_capacity_1').val();
        var remarks = $('#remarks').val();
        var call_type  =  $('#price_tag').val();
        if(mobile_number == ""){
             document.getElementById('booking_primary_contact_no').style.borderColor = "red";
              document.getElementById('error_mobile_number').innerHTML = "Please Enter Mobile";
             return false;
        } else {
            document.getElementById('booking_primary_contact_no').style.borderColor = "green";
              document.getElementById('error_mobile_number').innerHTML = "";
             
        }
        
        if(city == null){
            
             document.getElementById('booking_city').style.borderColor = "red";
              document.getElementById('error_city').innerHTML = "Please Enter City";
             return false;
        } else {
             document.getElementById('booking_city').style.borderColor = "green";
              document.getElementById('error_city').innerHTML = "";
            
        }
        if(pincode == ""){
             document.getElementById('booking_pincode').style.borderColor = "red";
              document.getElementById('error_pincode').innerHTML = "Please Enter Pincode";
             return false;
        } else {

            document.getElementById('booking_pincode').style.borderColor = "green";
            document.getElementById('error_pincode').innerHTML = "";
        }
        if(booking_address == ""){
             document.getElementById('booking_address').style.borderColor = "red";
             document.getElementById('error_address').innerHTML = "Please Enter Booking Address";
             return false;
        } else {

            document.getElementById('booking_address').style.borderColor = "green";
            document.getElementById('error_address').innerHTML = ""; 
        }
        if(pincode == ""){
             document.getElementById('booking_pincode').style.borderColor = "red";
              document.getElementById('error_pincode').innerHTML = "Please Enter Pincode";
             return false;
        } else {
            document.getElementById('booking_pincode').style.borderColor = "green";
              document.getElementById('error_pincode').innerHTML = "";
        }
        if (order_id == "" && serial_number == ""  ) {
             document.getElementById('order_id').style.borderColor = "red";
             document.getElementById('serial_number').style.borderColor = "red";
            document.getElementById('error_order_id').innerHTML = "Please enter either Order ID OR Serial Number";
            document.getElementById('error_serial_number').innerHTML = "Please enter either Order ID OR Serial Number";
               
            return false;
        } else {

            document.getElementById('order_id').style.borderColor = "green";
            document.getElementById('serial_number').style.borderColor = "green";
            document.getElementById('error_order_id').innerHTML = "";
            document.getElementById('error_serial_number').innerHTML = "";
        }

        if(category == null){
             document.getElementById('appliance_category_1').style.borderColor = "red";
              document.getElementById('error_category').innerHTML = "Please Select Category";
             return false;
        } else {
             document.getElementById('appliance_category_1').style.borderColor = "green";
              document.getElementById('error_category').innerHTML = "";
        }

        if(capacity == null){
             document.getElementById('appliance_capacity_1').style.borderColor = "red";
              document.getElementById('error_capacity').innerHTML = "Please Select Capacity";
             return false;
        } else {
            document.getElementById('appliance_capacity_1').style.borderColor = "green";
              document.getElementById('error_capacity').innerHTML = "";
        }

        if(call_type == null){
             document.getElementById('price_tag').style.borderColor = "red";
              document.getElementById('error_call_type').innerHTML = "Please Select Capacity";
             return false;
        } else {
            document.getElementById('price_tag').style.borderColor = "green";
              document.getElementById('error_call_type').innerHTML = "";
        }

        if(remarks == ""){
             document.getElementById('remarks').style.borderColor = "red";
              document.getElementById('error_remarks').innerHTML = "Please Enter Problem Description";
             return false;
        } else {
            document.getElementById('remarks').style.borderColor = "green";
            document.getElementById('error_remarks').innerHTML = "";  
        }
        
    
        if( !confirm('Confirm Booking?') ) 
            event.preventDefault();
    }
</script>
<style type="text/css">
    /* example styles for validation form demo */
    .err {
    color: red;
    }
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
    }
</style>
<?php $this->session->unset_userdata('success'); ?>
<script type="text/javascript">
    $("#booking_city").select2({
         tags: true
    });
</script>