<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="<?php echo base_url()?>js/custom_js.js"></script>
<style type="text/css">
    #booking_form .form-group label.error {
        margin:4px 0 5px !important;
        width:auto !important;
    }
</style>
<div id="page-wrapper">
  <div class="row">
      <div class="clear">
       <div class="panel panel-info">
           <div class="panel-heading"><b><?php if(isset($query[0]['id'])){echo "EDIT PARTNER";}else{echo "ADD PARTNER";}?></b>
               <div class="pull-right" style="margin-top:-7px;">
                   <?php if(isset($query[0]['id'])){?>
                   <a href="<?php echo base_url()?>employee/partner/get_partner_login_details_form/<?php echo $query[0]['id']?>" class="btn btn-primary"><b>MANAGE LOGIN</b></a>
                   <?php }?>
               </div>
           </div>
      
           <br>
      <?php if($this->session->flashdata('success')){
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success').'</strong>
                    </div>';
                    }
                    if($this->session->flashdata('error')){
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error').'</strong>
                    </div>';
                    }
                    ?>
           <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url()?>employee/partner/process_add_edit_partner_form" method="POST" enctype="multipart/form-data">

          <div>
              <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php if (isset($query[0]['id'])){echo $query[0]['id'];}?>">
              <?php echo form_error('id'); ?>
          </div>
        
    <div class="col-md-12">
        
            <div class="panel panel-default">
                <div class="panel-heading"><b>Company Information</b></div>
            </div>
        <div class="col-md-6">
        <div  class="form-group <?php if( form_error('company_name') ) { echo 'has-error';} ?>">
            <label  for="company_name" class="col-md-4">Company Name *</label>
        <div class="col-md-8">
            <input  type="text" class="form-control" id="company_name" name="company_name" value = "<?php if (isset($query[0]['company_name'])){echo $query[0]['company_name'];}?>" >
              <?php echo form_error('company_name'); ?>
        </div>
        </div>
            
        <div  class="form-group <?php if( form_error('public_name') ) { echo 'has-error';} ?>">
            <label  for="public_name" class="col-md-4">Public Name *</label>
        <div class="col-md-8">
            <input  type="text" class="form-control" id="public_name" name="public_name" value = "<?php if (isset($query[0]['public_name'])){echo $query[0]['public_name'];}?>" >
                <?php echo form_error('public_name'); ?>
        </div>
        </div>

        <div  class="form-group <?php if( form_error('address') ) { echo 'has-error';} ?>">
            <label  for="address" class="col-md-4">Address *</label>
        <div class="col-md-8">
            <input  type="text" class="form-control"  name="address" value = "<?php if (isset($query[0]['address'])){echo $query[0]['address'];}?>" >
              <?php echo form_error('address'); ?>
        </div>
        </div>
        
        <div class="form-group">
         <label  for="address" class="col-md-4">Landmark </label>
         <div class="col-md-8">
            <input  type="text" class="form-control" value = "<?php if (isset($query[0]['landmark'])){echo $query[0]['landmark'];}?>" name="landmark" >
         </div>
        </div>
        <div  class="form-group <?php if( form_error('partner_type') ) { echo 'has-error';} ?>">
            <label  for="partner_type" class="col-md-4">Type *</label>
        <div class="col-md-8">
            <select name="partner_type" class="form-control" >
                <option selected disabled>Select Partner Type</option>
                <option value="OEM" <?php if (isset($results['partner_code'][0])){ if($results['partner_code'][0]['partner_type']== OEM){ echo "selected";}}?>>OEM</option>
                <option value="ECOMMERCE" 
                    <?php if (isset($results['partner_code'][0])){ if($results['partner_code'][0]['partner_type']=="ECOMMERCE"){ echo "selected";}}?> >ECOMMERCE</option>
                
            </select>
             <?php echo form_error('partner_type'); ?>
        </div>
        </div>
            
            <div class="form-group">
                <label  for="upcountry_rate" class="col-md-4">Up-Country </label>
                <div class="col-md-1">
                    
                    <input type="checkbox" name="is_upcountry" id="upcountry" style="zoom:1.5" 
                        <?php if(isset($query)){ if($query[0]['is_upcountry'] ==1){ echo "checked";}} ?>/>
                </div>
                <div class="col-md-3">
                    <input  type="number" class="form-control" value = "<?php
                    if (isset($query[0]['upcountry_rate'])) {
                        echo $query[0]['upcountry_rate'];
                    }
                    ?>" name="upcountry_rate" id="upcountry_rate" <?php if(isset($query)){ if($query[0]['is_upcountry'] ==1){ } else {echo "disabled";}}else{ echo "disabled";} ?> placeholder="Enter KM's">
                </div>
                <div class="col-md-4">
                    <span><i>[Enter Rate per KM]</i></span>
                </div>
            </div>
        </div>
   
    <div class="col-md-6">    
    <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
      <label for="state" class="col-md-4">State *</label>
      <div class="col-md-8">
         <select class=" form-control" name ="state" id="state" onChange="getDistrict()" placeholder="Select State">
            <option disabled="disabled" selected="selected"> Select State</option>
            <?php
               foreach ($results['select_state'] as $state) {
               ?>
            <option value = "<?php echo $state['state']?>"
            <?php if (isset($query[0]['state'])){
                if(strtolower(trim($query[0]['state']))  == strtolower(trim($state['state']))){
                  echo "selected";
                }
             } ?>
             >
               <?php echo $state['state'];?>
            </option>
            <?php } ?>
         </select>
         <?php echo form_error('state'); ?>
      </div>
    </div>
    <div class="form-group <?php if( form_error('district') ) { echo 'has-error';} ?>">
      <label for="state" class="col-md-4">District *</label>
      <div class="col-md-8">
         <select class="district form-control" name ="district" id="district" onChange="getPincode()">
            <option <?php if (isset($query[0]['district'])){ echo "selected";}?>><?php if (isset($query[0]['district'])){echo $query[0]['district'];}?></option>
         </select>
          <?php echo form_error('district'); ?>
      </div>
   </div>
        
    <div class="form-group ">
      <label for="state" class="col-md-4">Pincode</label>
      <div class="col-md-8">
          <select class="pincode form-control" name ="pincode"  id="pincode">
            <option <?php if (isset($query[0]['pincode'])){ echo "selected";}?>><?php if (isset($query[0]['pincode'])){echo $query[0]['pincode'];}?></option>
         </select>
      </div>
    </div>
        
    <div class="form-group ">
      <label for="partner_code" class="col-md-4">Partner Code</label>
      <div class="col-md-8">
          <select class="form-control" name ="partner_code"  id="partner_code">
              <option value="" disabled="" selected="">Select Partner Code</option>
              <?php
              //Checking for Edit Parnter
              if (isset($query[0]['id'])) {
                  foreach (range('A', 'Z') as $char) {
                      $code = "S" . $char;
                      if (!in_array($code, $results['partner_code_availiable']) || isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code)) {
                          ?>
                          <option value="<?php echo $code; ?>" <?php
                          if (isset($results['partner_code'][0]['code']) && ($results['partner_code'][0]['code'] == $code )) {
                              echo "selected=''";
                          }
                          ?>><?php echo $code; ?></option>
                                  <?php
                              }
                          }
                      } else {// New Partner Addition
                          foreach (range('A', 'Z') as $char) {
                              $code = "S" . $char;
                              if (!in_array($code, $results['partner_code'])) {
                                  ?>
                          <option value="<?php echo $code; ?>" ><?php echo $code; ?></option>
                          <?php
                      }
                  }
              }
              ?>
         </select>
      </div>
    </div>
        
        <div class="form-group <?php if( form_error('contract_file') ) { echo 'has-error';} ?>">
            <label for="contract_file" class="col-md-4">Contract File</label>
            <div class="col-md-6">
                <input type="file" class="form-control"  name="contract_file">
              <?php echo form_error('contract_file'); ?>
            </div>
            <div class="col-md-1">
                <?php
                $src = base_url() . 'images/no_image.png';
                $image_src= $src;
                if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['contract_file'];
                    $image_src = base_url().'images/view_image.png';
                }?>
                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src; ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                <?php if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) { ?>
                    <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>,'<?php echo $query[0]['contract_file'] ?>','contract_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                <?php } ?>
            </div>
          </div>
        
    </div>
    </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>POC Details</b></div>
            </div>
           
        <div class="col-md-6">
          <div class="form-group <?php if( form_error('primary_contact_name') ) { echo 'has-error';} ?>">
            <label  for="primary_contact_name" class="col-md-4">Primary Contact Name</label>
            <div class="col-md-8">
              <input  type="text" class="form-control"  name="primary_contact_name" value = "<?php if (isset($query[0]['primary_contact_name'])){echo $query[0]['primary_contact_name'];}?>">
              <?php echo form_error('primary_contact_name'); ?>
            </div>
          </div>

          <div class="form-group <?php if( form_error('primary_contact_email') ) { echo 'has-error';} ?>">
            <label for="primary_contact_email" class="col-md-4">Primary Contact Email</label>
            <div class="col-md-8">
              <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php if (isset($query[0]['primary_contact_email'])){echo $query[0]['primary_contact_email'];}?>">
              <?php echo form_error('primary_contact_email'); ?>
            </div>
          </div>
        
        </div>
        <div class="col-md-6">
          <div class="form-group <?php if( form_error('primary_contact_phone_1') ) { echo 'has-error';} ?>">
            <label for="primary_contact_phone_1" class="col-md-4">Primary Contact Ph.No. 1</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php if (isset($query[0]['primary_contact_phone_1'])){echo $query[0]['primary_contact_phone_1'];}?>" >
              <?php echo form_error('primary_contact_phone_1'); ?>
            </div>
          </div>


          <div class="form-group <?php if( form_error('primary_contact_phone_2') ) { echo 'has-error';} ?>">
            <label for="primary_contact_phone_2" class="col-md-4">Primary Contact Ph.No. 2</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php if (isset($query[0]['primary_contact_phone_2'])){echo $query[0]['primary_contact_phone_2'];}?>">
              <?php echo form_error('primary_contact_phone_2'); ?>
            </div>
          </div>
        </div>   
        </div>
        
        <div class="col-md-12">
             <div class="panel panel-default">
                <div class="panel-heading"><b>Owner Details</b></div>
            </div>
        <div class="col-md-6">    
            
          <div class="form-group <?php if( form_error('owner_name') ) { echo 'has-error';} ?>">
            <label for="owner_name" class="col-md-4">Owner Name</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="owner_name" value = "<?php if (isset($query[0]['owner_name'])){echo $query[0]['owner_name'];}?>" >
              <?php echo form_error('owner_name'); ?>
            </div>
          </div>



          <div class="form-group <?php if( form_error('owner_email') ) { echo 'has-error';} ?>">
            <label for="owner_email" class="col-md-4">Owner Email</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="owner_email" value = "<?php if (isset($query[0]['owner_email'])){echo $query[0]['owner_email'];}?>" >
              <?php echo form_error('owner_email'); ?>
            </div>
          </div>
            
            <div class="form-group <?php if( form_error('owner_alternate_email') ) { echo 'has-error';} ?>">
            <label for="owner_alternate_email" class="col-md-4">Owner Alternate Email</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="owner_alternate_email" value = "<?php if (isset($query[0]['owner_alternate_email'])){echo $query[0]['owner_alternate_email'];}?>" >
              <?php echo form_error('owner_alternate_email'); ?>
            </div>
          </div>
        </div>
        <div class="col-md-6">    

          <div class="form-group <?php if( form_error('owner_phone_1') ) { echo 'has-error';} ?>">
            <label for="owner_phone_1" class="col-md-4">Owner Ph. No. 1</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php if (isset($query[0]['owner_phone_1'])){echo $query[0]['owner_phone_1'];}?>">
              <?php echo form_error('owner_phone_1'); ?>
            </div>
          </div>

          <div class="form-group <?php if( form_error('owner_phone_2') ) { echo 'has-error';} ?>">
            <label for="owner_phone_2" class="col-md-4">Owner Ph. No. 2</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="owner_phone_2" name="owner_phone_2" value = "<?php if (isset($query[0]['owner_phone_2'])){echo $query[0]['owner_phone_2'];}?>">
              <?php echo form_error('owner_phone_2'); ?>
            </div>
          </div>
        </div>  
        </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Summary Email</b></div>
            </div>
            
          <div class="col-md-6 form-group <?php if( form_error('summary_email_to') ) { echo 'has-error';} ?>">
            <label  for="summary_email_to" class="col-md-4">To</label>
            <div class="col-md-6">
              <input type="text" class="form-control"  name="summary_email_to" value = "<?php if (isset($query[0]['summary_email_to'])){echo $query[0]['summary_email_to'];}?>">
              <?php echo form_error('summary_email_to'); ?>
            </div>
          </div>

          <div class="col-md-6 form-group <?php if( form_error('summary_email_cc') ) { echo 'has-error';} ?>">
            <label for="summary_email_cc" class="col-md-4">cc</label>
            <div class="col-md-6">
              <input type="text" class="form-control"  name="summary_email_cc" value = "<?php if (isset($query[0]['summary_email_cc'])){echo $query[0]['summary_email_cc'];}?>">
              <?php echo form_error('summary_email_cc'); ?>
            </div>
          </div>

          </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Invoice Email</b></div>
            </div>

          <div class="col-md-6 form-group <?php if( form_error('invoice_email_to') ) { echo 'has-error';} ?>">
            <label for="invoice_email_to" class="col-md-4">To</label>
            <div class="col-md-6">
              <input type="text" class="form-control"  name="invoice_email_to" value = "<?php if (isset($query[0]['invoice_email_to'])){echo $query[0]['invoice_email_to'];}?>">
              <?php echo form_error('invoice_email_to'); ?>
            </div>
          </div>

          <div class="col-md-6 form-group <?php if( form_error('invoice_email_cc') ) { echo 'has-error';} ?>">
            <label for="invoice_email_cc" class="col-md-4">cc</label>
            <div class="col-md-6">
              <input type="text" class="form-control"  name="invoice_email_cc" value = "<?php if (isset($query[0]['invoice_email_cc'])){echo $query[0]['invoice_email_cc'];}?>">
              <?php echo form_error('invoice_email_cc'); ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Registration Details</b></div>
            </div>

          <div class="col-md-6 form-group <?php if( form_error('agreement_start_date') ) { echo 'has-error';} ?>">
            <label for="agreement_start_date" class="col-md-4">Partnership Start Date</label>
            <div class="col-md-6">
              <div class="input-group input-append date" >
                  <?php 
                    if(isset($query[0]['agreement_start_date'])){
                        if($query[0]['agreement_start_date'] != "0000-00-00"){
                            $aggrement_date = $query[0]['agreement_start_date'];
                        }else{
                            $aggrement_date =  date("Y-m-d",strtotime($query[0]['create_date']));
                        }
                        
                    }else{
                            $aggrement_date = date("Y-m-d");
                    }
                  ?>
                  <input type="text" class="form-control"  name="agreement_start_date" id="agreement_start_date" value = "<?php echo $aggrement_date;?>">
              <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <?php echo form_error('agreement_start_date'); ?>
            </div>
          </div>

          <div class="col-md-6 form-group <?php if( form_error('agreement_end_date') ) { echo 'has-error';} ?>">
            <label for="agreement_end_date" class="col-md-4">Partnership End Date</label>
            <div class="col-md-6">
              <div class="input-group input-append date" >
              <input type="text" class="form-control"  name="agreement_end_date" id="agreement_end_date" value = "<?php if (isset($query[0]['agreement_end_date'])){echo $query[0]['agreement_end_date'];}?>">
              <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <?php echo form_error('agreement_end_date'); ?>
            </div>
          </div>
            
          </div>
               
         <div class="col-md-12">
             
             <div class="col-md-12 form-group <?php if( form_error('pan') ) { echo 'has-error';} ?>">
            <label for="pan" class="col-md-3">PAN No.</label>
            <div class="col-md-4" style="width:25%">
                <input type="text" class="form-control"  name="pan" value = "<?php if (isset($query[0]['pan'])){echo $query[0]['pan'];}?>" placeholder="PAN Number">
            </div>
            <div class="col-md-4">
                <input type="file" class="form-control"  name="pan_file">
            </div>
            <div class="col-md-1">
                <?php
                $src = base_url() . 'images/no_image.png';
                $image_src = $src;
                if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) {
                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['pan_file'];
                    $image_src = base_url().'images/view_image.png';
                }?>
                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src; ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                <?php if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) { ?>
                    <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>,'<?php echo $query[0]['pan_file'] ?>','pan_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                <?php } ?>
            </div>
            </div>
         </div>
               
           <div class="col-md-12"> 
             <div class="col-md-12 form-group <?php if( form_error('registration_no') ) { echo 'has-error';} ?>">
            <label for="registration_no" class="col-md-3">Registration No.</label>
            <div class="col-md-4" style="width:25%">
                <input type="text" class="form-control"  name="registration_no" value = "<?php if (isset($query[0]['registration_no'])){echo $query[0]['registration_no'];}?>" placeholder="Company Registration No">
            </div>
            <div class="col-md-4">
                <input type="file" class="form-control"  name="registration_file">
            </div>
            <div class="col-md-1">
                <?php
                $src = base_url() . 'images/no_image.png';
                $image_src  = $src;
                if (isset($query[0]['registration_file']) && !empty($query[0]['registration_file'])) {
                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['registration_file'];
                    $image_src = base_url().'images/view_image.png';
                }?>
                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                <?php if (isset($query[0]['registration_file']) && !empty($query[0]['registration_file'])) { ?>
                    <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>,'<?php echo $query[0]['registration_file'] ?>','registration_file')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                <?php } ?>
            </div>
            </div>
             
         </div>
               
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Partner Operation Region</b></div>
            </div>
            <?php foreach ($results['services'] as $value) { 
                //Checking Operation regions if Present for User Edit
                $operation_region_state = [];
                if(!empty($results['partner_operation_region'])){
                    foreach($results['partner_operation_region'] as $val){
                        if($val['service_id'] == $value->id){
                            $operation_region_state[] = $val['state'];
                        }
                    }
                }
                
                ?>
                <div class="col-md-12 form-group">  
                    <div class="col-md-3"><?php echo $value->services ?></div>
                    <select name ="select_state[<?php echo $value->id?>][]" class=" col-md-4 select_state" multiple="multiple">
                        <option value="all">ALL</option>
                        <?php foreach ($results['select_state'] as $val) { ?>
                            <option value="<?php echo $val['state'] ?>" <?php echo (isset($operation_region_state) && in_array($val['state'],$operation_region_state))?'selected="selected"':''?> ><?php echo $val['state'] ?></option>
                        <?php } ?>
                    </select>
                </div>

            <?php } ?>
        </div>
        
        <div class="clear clear_bottom">
            <br>
            <center><input type="Submit" value="<?php if (isset($query[0]['id'])){echo "Update Partner";}else{echo "Save Partner";}?>" class="btn btn-primary" >
            <?php echo "<a class='btn btn-small btn-primary' href=".base_url()."employee/partner/viewpartner>Cancel</a>";?>
            </center>
        </div>    
        </form>
      </div>
   </div>
  </div>
</div>
<script type="text/javascript">
    
  $('.select_state').select2({
    placeholder: "Select State",
    allowClear: true
  });
  $('#state').select2({
      placeholder: "Select State"
  });
  $('#district').select2({
      placeholder: "Select City"
  });
  $('#pincode').select2({
      placeholder: "Select Pincode"
  });
  
  $("#agreement_start_date").datepicker({dateFormat: 'yy-mm-dd'});
  $("#agreement_end_date").datepicker({dateFormat: 'yy-mm-dd', minDate: 0});

  
  //Check for upcountry
   $("#upcountry").change(function(){
      if(this.checked){
          $("#upcountry_rate").attr('required', true);
          $("#upcountry_rate").attr('disabled', false);
      }else{
          $("#upcountry_rate").attr('required', false);
          $("#upcountry_rate").attr('disabled', true);
         // $("#upcountry_rate").val('');
      }
      
   });
   
  
  function getDistrict(){
     var state = $("#state").val();
     var district = $(".district").val();
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict/1',
       data: {state: state, district: district},
       success: function (data) {
         $(".district").html(data);
         if(district !=""){
           getPincode();
         }
       }
     });
   }
   function getPincode(){
      var district = $(".district").val();
      var pincode = $(".pincode").val();
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/vendor/getPincode/1',
        data: {pincode: pincode, district: district},
        success: function (data) {
          $(".pincode").html(data);
       }
     });
   }
   
  $(function() {
    var state = $("#state").val();
    if(state  !=""){
        getDistrict();
    }
  });
  
  $(function() {
    $('#username').on('keypress', function(e) {
        if (e.which == 32)
            return false;
    });
});
  
  function remove_image(vendor_id,file_name,type){
            var c  = confirm('Do you want to permanently remove photo?');
            if(c){
             $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/partner/remove_uploaded_image',
                        data: {id: vendor_id,file_name:file_name,type:type},
                        success: function (data) {
                             location.reload();
                            }
                    });
                 }else{
                    return false;
                 }
        }
        
</script>
<script type="text/javascript">

   (function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            $("#booking_form").validate({
                rules: {
                    company_name: "required",
                    public_name: "required",
                    address: "required",
                    district: "required",
                    username: "required",
                    partner_type: "required",
                    phone_1: {
                        required: true,
                        minlength: 10,
                        number: true
                    },
                    phone_2: {
                        minlength: 10,
                        number: true
                    },
                    primary_contact_phone_1: {
                        minlength: 10,
                        number: true
                    },
                    primary_contact_phone_2: {
                        minlength: 10,
                        number: true
                    },
                    owner_phone_1: {
                        minlength: 10,
                        number: true
                    },
                    owner_phone_2: {
                        minlength: 10,
                        number: true
                    },
                    state: "required",
                    email: {

                        email: true
                    },
                    primary_contact_email: {

                        email: true
                    },
                    owner_email: {

                        email: true
                    }
                },
                messages: {
                    company_name: "Please enter your Company Name",
                    public_name: "Please enter your Public Name",
                    address: "Please enter Address",
                    district: "Please Select District",
                    partner_type: "Please Select Partner Type",
                    state: "Please Select State",
                    primary_contact_phone_1: "Please fill correct phone number",
                    primary_contact_phone_2: "Please fill correct phone number",
                    owner_phone_1: "Please fill correct phone number",
                    owner_phone_2: "Please fill correct phone number",
                    primary_contact_name: "Please fill Name",
                    owner_name: "Please fill Name",
                    primary_contact_email: "Please fill correct email",
                    owner_email: "Please fill correct email",
                    username: "Please fill User Name",
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