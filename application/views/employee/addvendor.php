<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="<?php echo base_url()?>js/custom_js.js"></script>


<div id="page-wrapper">
  <div class="row">
      <div style="margin:20px;">
<h1 style="color:red;"><?php if(isset($selected_brands_list)){echo "Edit Vendor";}else{echo "Add Vendor";}?></h1>
<hr>
        <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url()?>employee/vendor" method="POST">

          <div>
              <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php if (isset($query[0]['id'])){echo $query[0]['id'];}?>">
              <?php echo form_error('id'); ?>
          </div>

          <div>
          <p style="color:blue;"><b>Company Information:</b></p>
      <div  class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
         <label  for="name" class="col-md-1">Name:</label>
         <div class="col-md-4">
            <input  type="text" class="form-control" id="name" name="name" value = "<?php if (isset($query[0]['name'])){echo $query[0]['name'];}?>" >
              <?php echo form_error('name'); ?>
            </div>
          </div>

      <div  class="form-group <?php if( form_error('address') ) { echo 'has-error';} ?>">
         <label  for="address" class="col-md-1">Address:</label>
         <div class="col-md-4">
            <input  type="text" class="form-control"  name="address" value = "<?php if (isset($query[0]['address'])){echo $query[0]['address'];}?>" >
              <?php echo form_error('address'); ?>
            </div>
          </div>
          </div>
   <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
      <label for="state" class="col-md-1">State:</label>
      <div class="col-md-4">
         <select class=" form-control" name ="state" id="state" onChange="getDistrict()" placeholder="Select State">
            <option disabled="disabled" selected="selected"> Select State</option>
            <?php 
               foreach ($results['select_state'] as $state) {    
               ?>
            <option value = "<?php echo $state['state']?>"
            <?php if (isset($query[0]['state'])){ 
                if($query[0]['state'] == $state['state']){
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
      <label for="state" class="col-md-1">District:</label>
      <div class="col-md-4">
         <select class="district form-control" name ="district" onChange="getPincode()">
            <option selected disabled>Select District</option> 

            <option <?php if (isset($query[0]['district'])){ echo "selected";}?>><?php if (isset($query[0]['district'])){echo $query[0]['district'];}?></option>
         </select>
          <?php echo form_error('district'); ?>
      </div>
   </div>
      <div class="form-group ">
      <label for="state" class="col-md-1">Pincode:</label>
      <div class="col-md-4">
          <select class="pincode form-control" name ="pincode"  >
            <option selected disabled>Select Pincode</option>
            <option <?php if (isset($query[0]['pincode'])){ echo "selected";}?>><?php if (isset($query[0]['pincode'])){echo $query[0]['pincode'];}?></option>
         </select>
      </div>
   </div>
   <div  class="form-group">
         <label  for="address" class="col-md-1">Landmark:</label>
         <div class="col-md-4">
            <input  type="text" class="form-control" value = "<?php if (isset($query[0]['landmark'])){echo $query[0]['landmark'];}?>" name="landmark" >
         </div>
      </div>
          <div>
   <div class="form-group <?php if( form_error('registration_number') ) { echo 'has-error';} ?>">
      <label  for="registration_number" class="col-md-1">Registration No.:</label>
      <div class="col-md-4">
         <input type="text" class="form-control"  name="registration_number" value = "<?php if (isset($query[0]['registration_number'])){echo $query[0]['registration_number'];}?>">
              <?php echo form_error('registration_number'); ?>
            </div>
          </div>
              <div style="float:left;width:90%;" class="form-group <?php if( form_error('non_working_days') ) { echo 'has-error';} ?>">
            <label for="non_working_days" class="col-md-2">Non Working Days:</label>
            <div class="col-md-12">
              <?php foreach($days as $key => $day){?>
          <label for="non_working_days" >
          <input type="checkbox" name="day[]" value ="<?php echo $day;?>"
          <?php if(isset($selected_non_working_days)){if(in_array($day, $selected_non_working_days))echo "checked";}
            ?> >
          <?php echo $day;?> &nbsp;&nbsp;&nbsp;
          </label>
          <?php } ?>
            </div>
          </div>
            <div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('phone_1') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="phone_1" class="col-md-2">Phone No. 1:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="phone_1" name="phone_1" value = "<?php if (isset($query[0]['phone_1'])){echo $query[0]['phone_1'];}?>">
              <?php echo form_error('phone_1'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('phone_2') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="phone_2" class="col-md-2">Phone No. 2:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="phone_2" name="phone_2" value = "<?php if (isset($query[0]['phone_2'])){echo $query[0]['phone_2'];}?>">
              <?php echo form_error('phone_2'); ?>
            </div>
          </div>
          </div>


          <div>
          <div style="float:left;width:33%;" class="form-group <?php if( form_error('email') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="email" class="col-md-2">Email:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="email" value = "<?php if (isset($query[0]['email'])){echo $query[0]['email'];}?>">
              <?php echo form_error('email'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('service_tax_no') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="service_tax_no" class="col-md-2">Service Tax No.</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="service_tax_no" value = "<?php if (isset($query[0]['service_tax_no'])){echo $query[0]['service_tax_no'];}?>">
              <?php echo form_error('service_tax_no'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('remarks') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="remarks" class="col-md-2">Remarks:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="remarks" value = "<?php if (isset($query[0]['remarks'])){echo $query[0]['remarks'];}?>">
              <?php echo form_error('remarks'); ?>
            </div>
          </div>
          </div>

          <div style="float:left;">
          <p style="color:blue;"><b>POC Details:</b></p>
          <div style="width:33%;" class="form-group <?php if( form_error('primary_contact_name') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="primary_contact_name" class="col-md-2">Primary Contact Name:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="primary_contact_name" value = "<?php if (isset($query[0]['primary_contact_name'])){echo $query[0]['primary_contact_name'];}?>">
              <?php echo form_error('primary_contact_name'); ?>
            </div>
          </div>

          <div>
          <div style="float:left;width:33%;" class="form-group <?php if( form_error('primary_contact_email') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="primary_contact_email" class="col-md-2">Primary Contact Email:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="primary_contact_email" value = "<?php if (isset($query[0]['primary_contact_email'])){echo $query[0]['primary_contact_email'];}?>">
              <?php echo form_error('primary_contact_email'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('primary_contact_phone_1') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="primary_contact_phone_1" class="col-md-2">Primary Contact Ph.No. 1:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php if (isset($query[0]['primary_contact_phone_1'])){echo $query[0]['primary_contact_phone_1'];}?>" >
              <?php echo form_error('primary_contact_phone_1'); ?>
            </div>
          </div>
          
          
          <div style="float:left;width:33%;" class="form-group <?php if( form_error('primary_contact_phone_2') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="primary_contact_phone_2" class="col-md-2">Primary Contact Ph.No. 2:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php if (isset($query[0]['primary_contact_phone_2'])){echo $query[0]['primary_contact_phone_2'];}?>">
              <?php echo form_error('primary_contact_phone_2'); ?>
            </div>
          </div>
          </div>

          <div style="float:left;">
          <p style="color:blue;"><b>Owner Details:</b></p>
          <div style="width:33%;" class="form-group <?php if( form_error('owner_name') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="owner_name" class="col-md-2">Owner Name:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="owner_name" value = "<?php if (isset($query[0]['owner_name'])){echo $query[0]['owner_name'];}?>" >
              <?php echo form_error('owner_name'); ?>
            </div>
          </div>
          


          <div style="float:left;width:33%;" class="form-group <?php if( form_error('owner_email') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="owner_email" class="col-md-2">Owner Email:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="owner_email" value = "<?php if (isset($query[0]['owner_email'])){echo $query[0]['owner_email'];}?>" >
              <?php echo form_error('owner_email'); ?>
            </div>
          </div>
          

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('owner_phone_1') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="owner_phone_1" class="col-md-2">Owner Ph. No. 1:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php if (isset($query[0]['owner_phone_1'])){echo $query[0]['owner_phone_1'];}?>">
              <?php echo form_error('owner_phone_1'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('owner_phone_2') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="owner_phone_2" class="col-md-2">Owner Ph. No. 2:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control" id="owner_phone_2" name="owner_phone_2" value = "<?php if (isset($query[0]['owner_phone_2'])){echo $query[0]['owner_phone_2'];}?>">
              <?php echo form_error('owner_phone_2'); ?>
            </div>
          </div>

          <div style="float:left;">
          <p style="color:blue;"><b>Bank Details:</b></p>
          <div style="width:33%;" class="form-group <?php if( form_error('bank_name') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="bank_name" class="col-md-2">Bank Name:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="bank_name" value = "<?php if (isset($query[0]['bank_name'])){echo $query[0]['bank_name'];}?>">
              <?php echo form_error('bank_name'); ?>
            </div>
          </div>
          
          <div>
          <div style="float:left;width:33%;" class="form-group <?php if( form_error('bank_account') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="bank_account" class="col-md-2">Bank Account:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="bank_account" value = "<?php if (isset($query[0]['bank_account'])){echo $query[0]['bank_account'];}?>">
              <?php echo form_error('bank_account'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('ifsc_code') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="ifsc_code" class="col-md-2">IFSC Code:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="ifsc_code" value = "<?php if (isset($query[0]['ifsc_code'])){echo $query[0]['ifsc_code'];}?>">
              <?php echo form_error('ifsc_code'); ?>
            </div>
          </div>

          <div style="float:left;width:33%;" class="form-group <?php if( form_error('beneficiary_name') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="beneficiary_name" class="col-md-2">Beneficiary Name:</label>
            <div class="col-md-2">
              <input style="width:200px;" type="text" class="form-control"  name="beneficiary_name" value = "<?php if (isset($query[0]['beneficiary_name'])){echo $query[0]['beneficiary_name'];}?>">
              <?php echo form_error('beneficiary_name'); ?>
            </div>
          </div>
          </div>

          <div style="float:left;width:90%;" class="form-group <?php if( form_error('appliance') ) { echo 'has-error';} ?>">
            <label for="appliance" class="col-md-2" style="color:blue;">Appliance:</label>
            <div class="col-md-12">

              <?php foreach($results['services'] as $key => $appliance){?>
          <label for="Appliance" >
          <input type="checkbox" name="appliances[]" value ="<?php echo $appliance->services;?>"
          <?php if(isset($selected_appliance_list)){if(in_array($appliance->services, $selected_appliance_list))echo "checked";}
            ?> >
          <?php echo $appliance->services;?> &nbsp;&nbsp;&nbsp;
          </label>
          <?php } ?>
            </div>
          </div>
          
          <div style="float:left;width:90%;" class="form-group <?php if( form_error('brand') ) { echo 'has-error';} ?>">
            <label for="brand" class="col-md-2" style="color:blue;">Brands:</label>
            <div class="col-md-12">
            <?php foreach($results['brands'] as $key => $brands){
               ?>
            <label for="Brand" >
            <input type="checkbox" name="brands[]" value ="<?php 
           echo $brands->brand_name;?>"
           <?php if(isset($selected_brands_list)){if(in_array($brands->brand_name, $selected_brands_list))echo "checked";}
            ?>>
          <?php echo $brands->brand_name;?> &nbsp;&nbsp;&nbsp;
          </label>
          <?php } ?>
          </div>
          </div>
            <center>

               <div class="col-md-2"><input type="Submit" value="<?php if (isset($query[0]['id'])){echo "Update Vendor";}else{echo "Save Vendor";}?>" class="btn btn-primary" ></div>
            </center>
           <div style="float:left;"><?php echo "<a class='btn btn-small btn-primary' href=".base_url()."employee/vendor/viewvendor>Cancel</a>";?></div>
        </form>
      </div>
  </div>
</div>
<script type="text/javascript">
    /*$(".js-example-placeholder-single").select2({
      placeholder: "Select a state",
      allowClear: true
}); */
  function getDistrict(){
     var state = $("#state").val();
     var district = $(".district").val();     
    // alert(district);
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict',
       data: {state: state, district: district},
       success: function (data) {
        // console.log(data);
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
        url: '<?php echo base_url(); ?>employee/vendor/getPincode',
        data: {pincode: pincode, district: district},
        success: function (data) {
          //console.log(data);
          $(".pincode").html(data);          
       }
     });
   }

  // $("#district_option").select2();
  $(function() {
    var state = $("#state").val();
    if(state  !=""){
        getDistrict();
    }
  });
</script>
<style type="text/css">
  /* example styles for validation form demo */




#booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
}
</style>