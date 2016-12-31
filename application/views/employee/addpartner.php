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
           <div class="panel-heading"><b><?php if(isset($query[0]['id'])){echo "Edit Partner";}else{echo "Add Partner";}?></b></div>
      
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
                if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['contract_file'];
                }?>
                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                <?php if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) { ?>
                    <a href="javascript:void(0)" onclick="remove_image(<?php echo $query[0]['id'] ?>,'<?php echo $query[0]['contract_file'] ?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
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
            
          <div class="col-md-4 form-group <?php if( form_error('summary_email_to') ) { echo 'has-error';} ?>">
            <label  for="summary_email_to" class="col-md-4">To</label>
            <div class="col-md-8">
              <input style="width:200px;" type="text" class="form-control"  name="summary_email_to" value = "<?php if (isset($query[0]['summary_email_to'])){echo $query[0]['summary_email_to'];}?>">
              <?php echo form_error('summary_email_to'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('summary_email_cc') ) { echo 'has-error';} ?>">
            <label for="summary_email_cc" class="col-md-4">cc</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="summary_email_cc" value = "<?php if (isset($query[0]['summary_email_cc'])){echo $query[0]['summary_email_cc'];}?>">
              <?php echo form_error('summary_email_cc'); ?>
            </div>
          </div>

          </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Invoice Email</b></div>
            </div>

          <div class="col-md-4 form-group <?php if( form_error('invoice_email_to') ) { echo 'has-error';} ?>">
            <label for="invoice_email_to" class="col-md-4">To</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="invoice_email_to" value = "<?php if (isset($query[0]['invoice_email_to'])){echo $query[0]['invoice_email_to'];}?>">
              <?php echo form_error('invoice_email_to'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('invoice_email_cc') ) { echo 'has-error';} ?>">
            <label for="invoice_email_cc" class="col-md-4">cc</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="invoice_email_cc" value = "<?php if (isset($query[0]['invoice_email_cc'])){echo $query[0]['invoice_email_cc'];}?>">
              <?php echo form_error('invoice_email_cc'); ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Registration Details</b></div>
            </div>

          <div class="col-md-4 form-group <?php if( form_error('username') ) { echo 'has-error';} ?>">
            <label for="username" class="col-md-4">User Name *</label>
            <div class="col-md-8">
                <input type="text" class="form-control"  id="username" name="username" placeholder="Enter User Name" value = "<?php if (isset($results['login_details'][0]['user_name'])){echo $results['login_details'][0]['user_name'];}?>">
              <?php echo form_error('username'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('password') ) { echo 'has-error';} ?>">
            <label for="password" class="col-md-4">Password</label>
            <div class="col-md-8">
                <input type="password" class="form-control"  name="password" placeholder="Enter Password" value = "<?php if (isset($results['login_details'][0]['clear_text'])){echo $results['login_details'][0]['clear_text'];}?>">
              <?php echo form_error('password'); ?>
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
  
  function remove_image(vendor_id,file_name){
            var c  = confirm('Do you want to permanently remove photo?');
            if(c){
             $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/partner/remove_contract_image',
                        data: {id: vendor_id,file_name:file_name},
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
                    phone_1: {
                        required: true,
                        rangelength: [10,10]
                    },
                    phone_2: {
                        rangelength: [10,10]
                    },
                    primary_contact_phone_1: {
                        rangelength: [10,10]
                    },
                    primary_contact_phone_2: {
                        rangelength: [10,10]
                    },
                    owner_phone_1: {
                        rangelength: [10,10]
                    },
                    owner_phone_2: {
                        rangelength: [10,10]
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