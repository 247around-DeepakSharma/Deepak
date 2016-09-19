<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="<?php echo base_url()?>js/custom_js.js"></script>

<div id="page-wrapper">
  <div class="row">
      <div class="container">
      <div class="clear">
       <div class="panel panel-info">
           <div class="panel-heading"><b><?php if(isset($selected_brands_list)){echo "Edit Partner";}else{echo "Add Partner";}?></b></div>
       </div>
<hr>
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
    <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url()?>employee/partner/process_add_edit_partner_form" method="POST">

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
            <label  for="company_name" class="col-md-4">Company Name:</label>
        <div class="col-md-8">
            <input  type="text" class="form-control" id="company_name" name="company_name" value = "<?php if (isset($query[0]['company_name'])){echo $query[0]['company_name'];}?>" >
              <?php echo form_error('company_name'); ?>
        </div>
        </div>
            
        <div  class="form-group <?php if( form_error('public_name') ) { echo 'has-error';} ?>">
            <label  for="public_name" class="col-md-4">Public Name:</label>
        <div class="col-md-8">
            <input  type="text" class="form-control" id="public_name" name="public_name" value = "<?php if (isset($query[0]['public_name'])){echo $query[0]['public_name'];}?>" >
                <?php echo form_error('public_name'); ?>
        </div>
        </div>

        <div  class="form-group <?php if( form_error('address') ) { echo 'has-error';} ?>">
            <label  for="address" class="col-md-4">Address:</label>
        <div class="col-md-8">
            <input  type="text" class="form-control"  name="address" value = "<?php if (isset($query[0]['address'])){echo $query[0]['address'];}?>" >
              <?php echo form_error('address'); ?>
        </div>
        </div>
        
        <div class="form-group">
         <label  for="address" class="col-md-4">Landmark:</label>
         <div class="col-md-8">
            <input  type="text" class="form-control" value = "<?php if (isset($query[0]['landmark'])){echo $query[0]['landmark'];}?>" name="landmark" >
         </div>
        </div>
        </div>
   
    <div class="col-md-6">    
    <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
      <label for="state" class="col-md-4">State:</label>
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
      <label for="state" class="col-md-4">District:</label>
      <div class="col-md-8">
         <select class="district form-control" name ="district" onChange="getPincode()">
            <option selected disabled>Select District</option>

            <option <?php if (isset($query[0]['district'])){ echo "selected";}?>><?php if (isset($query[0]['district'])){echo $query[0]['district'];}?></option>
         </select>
          <?php echo form_error('district'); ?>
      </div>
   </div>
        
    <div class="form-group ">
      <label for="state" class="col-md-4">Pincode:</label>
      <div class="col-md-8">
          <select class="pincode form-control" name ="pincode"  >
            <option selected disabled>Select Pincode</option>
            <option <?php if (isset($query[0]['pincode'])){ echo "selected";}?>><?php if (isset($query[0]['pincode'])){echo $query[0]['pincode'];}?></option>
         </select>
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
            <label  for="primary_contact_name" class="col-md-4">Primary Contact Name:</label>
            <div class="col-md-8">
              <input  type="text" class="form-control"  name="primary_contact_name" value = "<?php if (isset($query[0]['primary_contact_name'])){echo $query[0]['primary_contact_name'];}?>">
              <?php echo form_error('primary_contact_name'); ?>
            </div>
          </div>

          <div class="form-group <?php if( form_error('primary_contact_email') ) { echo 'has-error';} ?>">
            <label for="primary_contact_email" class="col-md-4">Primary Contact Email:</label>
            <div class="col-md-8">
              <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php if (isset($query[0]['primary_contact_email'])){echo $query[0]['primary_contact_email'];}?>">
              <?php echo form_error('primary_contact_email'); ?>
            </div>
          </div>
        
        </div>
        <div class="col-md-6">
          <div class="form-group <?php if( form_error('primary_contact_phone_1') ) { echo 'has-error';} ?>">
            <label for="primary_contact_phone_1" class="col-md-4">Primary Contact Ph.No. 1:</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php if (isset($query[0]['primary_contact_phone_1'])){echo $query[0]['primary_contact_phone_1'];}?>" >
              <?php echo form_error('primary_contact_phone_1'); ?>
            </div>
          </div>


          <div class="form-group <?php if( form_error('primary_contact_phone_2') ) { echo 'has-error';} ?>">
            <label for="primary_contact_phone_2" class="col-md-4">Primary Contact Ph.No. 2:</label>
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
            <label for="owner_name" class="col-md-4">Owner Name:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="owner_name" value = "<?php if (isset($query[0]['owner_name'])){echo $query[0]['owner_name'];}?>" >
              <?php echo form_error('owner_name'); ?>
            </div>
          </div>



          <div class="form-group <?php if( form_error('owner_email') ) { echo 'has-error';} ?>">
            <label for="owner_email" class="col-md-4">Owner Email:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="owner_email" value = "<?php if (isset($query[0]['owner_email'])){echo $query[0]['owner_email'];}?>" >
              <?php echo form_error('owner_email'); ?>
            </div>
          </div>
        </div>
        <div class="col-md-6">    

          <div class="form-group <?php if( form_error('owner_phone_1') ) { echo 'has-error';} ?>">
            <label for="owner_phone_1" class="col-md-4">Owner Ph. No. 1:</label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php if (isset($query[0]['owner_phone_1'])){echo $query[0]['owner_phone_1'];}?>">
              <?php echo form_error('owner_phone_1'); ?>
            </div>
          </div>

          <div class="form-group <?php if( form_error('owner_phone_2') ) { echo 'has-error';} ?>">
            <label for="owner_phone_2" class="col-md-4">Owner Ph. No. 2:</label>
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
            <label  for="summary_email_to" class="col-md-4">To:</label>
            <div class="col-md-8">
              <input style="width:200px;" type="text" class="form-control"  name="summary_email_to" value = "<?php if (isset($query[0]['summary_email_to'])){echo $query[0]['summary_email_to'];}?>">
              <?php echo form_error('summary_email_to'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('summary_email_cc') ) { echo 'has-error';} ?>">
            <label for="summary_email_cc" class="col-md-4">cc:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="summary_email_cc" value = "<?php if (isset($query[0]['summary_email_cc'])){echo $query[0]['summary_email_cc'];}?>">
              <?php echo form_error('summary_email_cc'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('summary_email_bcc`') ) { echo 'has-error';} ?>">
            <label for="summary_email_bcc" class="col-md-4">Bcc:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="summary_email_bcc" value = "<?php if (isset($query[0]['summary_email_bcc'])){echo $query[0]['summary_email_bcc'];}?>">
              <?php echo form_error('summary_email_bcc'); ?>
            </div>
          </div>
          </div>
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Invoice Email</b></div>
            </div>

          <div class="col-md-4 form-group <?php if( form_error('invoice_email_to') ) { echo 'has-error';} ?>">
            <label for="invoice_email_to" class="col-md-4">To:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="invoice_email_to" value = "<?php if (isset($query[0]['invoice_email_to'])){echo $query[0]['invoice_email_to'];}?>">
              <?php echo form_error('invoice_email_to'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('invoice_email_cc') ) { echo 'has-error';} ?>">
            <label for="invoice_email_cc" class="col-md-4">cc:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="invoice_email_cc" value = "<?php if (isset($query[0]['invoice_email_cc'])){echo $query[0]['invoice_email_cc'];}?>">
              <?php echo form_error('invoice_email_cc'); ?>
            </div>
          </div>

          <div class="col-md-4 form-group <?php if( form_error('invoice_email_bcc`') ) { echo 'has-error';} ?>">
            <label for="invoice_email_bcc" class="col-md-4">Bcc:</label>
            <div class="col-md-8">
              <input type="text" class="form-control"  name="invoice_email_bcc" value = "<?php if (isset($query[0]['invoice_email_bcc'])){echo $query[0]['invoice_email_bcc'];}?>">
              <?php echo form_error('invoice_email_bcc'); ?>
            </div>
          </div>
          
        </div>
        <div class="clear clear_bottom">
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
                    phone_1: {
                        required: true,
                        minlength: 10
                    },
                    phone_2: {
                        minlength: 10
                    },
                    primary_contact_phone_1: {
                        required: true,
                        minlength: 10
                    },
                    primary_contact_phone_2: {
                        minlength: 10
                    },
                    owner_phone_1: {
                        required: true,
                        minlength: 10
                    },
                    owner_phone_2: {
                        minlength: 10
                    },
                    state: "required",
                    primary_contact_name: "required",
                    owner_name: "required",
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
                    owner_email: "Please fill correct email"
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