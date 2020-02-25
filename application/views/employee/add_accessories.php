<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
   .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
<div class="row">
   <div  class = "panel panel-info" style="margin:20px;">
      <div class="panel-heading" style='height:auto;overflow:hidden'>
        <div class='col-md-6'>ADD ACCESSORIES</div>
        <div class='col-md-6' style='text-align:right'>
            <a href='<?php echo base_url(); ?>employee/accessories/show_accessories_list' class='btn btn-primary btn-sm'>Show Accessories List</a></div>
      </div>

      <div class="container1">
         <form method="post" action='<?php echo base_url() ?>employee/accessories/process_submit_add_product' id='form_add_product'>
            <br>
            <div class='col-md-12' id='response_div' style='display:none'>
               <div class="alert alert-success" id='response_div_s'>
                  <strong>Success!</strong> Accessories added successfully.
               </div>
               <div class="alert alert-danger" id='response_div_e'>
                  <strong>Error!</strong> <span id='response_div_e_span'></span>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-lg-6">
                  <div class="form-group">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="appliance">Appliance <sup class='mandatory'>*</sup></label>
                        </div>
                        <div class="col-lg-6">
                           <select class="form-control" name="appliance_id" id='appliance_id'>
                              <option value="">Select Appliance Name</option>
                              <?php 
                                 foreach ($services_detail as $row) 
                                 {
                                 
                                 ?>
                              <tr>
                                 <option value="<?php echo $row->id; ?>"><?php echo $row->services; ?></option>
                              </tr>
                              <?php
                                 }
                                 ?>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-lg-6">
                  <div class="form-group">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="product_name">Product Name <sup class='mandatory'>*</sup></label>
                        </div>
                        <div class="col-lg-6">
                           <input type="text" name="product_name" class="form-control" placeholder="Enter Product Name" id='product_name'>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div style='margin:10px;' class='col-md-12'></div>

            <div class="form-group">
               <div class="row">
                  <div class="col-lg-6">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="description">Description <sup class='mandatory'>*</sup></label>
                        </div>
                        <div class="col-lg-6">
                           <textarea name="description" class="form-control" placeholder="Enter Description" id='description'></textarea>
                        </div>
                     </div>
                  </div>

                  <div class="col-lg-6">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="basic_charge">Basic Charge <sup class='mandatory'>*</sup></label>
                        </div>
                        <div class="col-lg-6">
                           <input type="number" name="basic_charge" id='basic_charge' class="form-control numeric" placeholder="Enter Basic Charge">
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div style='margin:10px;' class='col-md-12'></div>
            <div class="row">


               <div class="col-lg-6">
                  <div class="form-group">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="hsn_code">HSN Code <sup class='mandatory'>*</sup></label>
                        </div>
                        <div class="col-lg-6">
                           <select class="form-control"  name="hsn_code" id="hsn_code" >
                              <option value="name">Select HSN Code</option>
                              <?php
                                 foreach ($hsn_code_detail as $row1) 
                                 {
                                 ?>
                              <option value="<?php echo $row1['hsn_code']?>" ><?php echo $row1['hsn_code']?></option>
                              <?php
                                 }
                                 ?>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>


               <div class="col-lg-6">
                  <div class="form-group">
                     <div class="row1">
                        <div class="col-lg-4">
                           <label for="tax_rate">Tax Rate <sup class='mandatory'>*</sup> <span id='tax_loading_form'></span></label>
                        </div>
                        <div class="col-lg-6">
                           <input type="text" id="tax_rate" name="tax_rate" class="form-control" readonly>
                        </div>
                     </div>
                  </div>
               </div>
               <div style='margin:10px;' class='col-md-12'></div>         
			</div>

			</form>


         <div class="panel-footer" align='center'>
            <input type="button" id="submitform" onclick="addproduct()" class="btn btn-primary" value="Add Accessories">
         </div>
         <div class="form-group  col-md-12" >
            <center>
         </div>
         </center>
      </div>
   </div>
</div>

<script type="text/javascript">
$('#appliance_id').select2();
$('#hsn_code').select2();

$("#hsn_code").on('change',function(){
	var hsncode=$("#hsn_code").val();
	
	var datastring="hsncode="+hsncode;
	if(hsncode=='' || hsncode==null)
	{
		$('#tax_rate').val('');
	}
	else
	{
		$.ajax({
		method: 'post',
		data: datastring,
		url: "<?php echo base_url() ?>employee/accessories/calculate_tax",
		beforeSend()
		{
			$("#tax_loading_form").html("<img src='<?php echo base_url() ?>images/loading.gif' style='width:15px'>");
			$("#submitform").prop('disabled',true);
		},
		success: function(datastring)
		{
                        datastring=datastring.trim();
			$("#tax_loading_form").html("<img src='' style='width:15px'>");
			$('#tax_rate').val(datastring);
			$("#submitform").prop('disabled',false);
		}
		});
	}
});
 
function addproduct()
{
	var appliance_id=$("#appliance_id").val().trim();
	var product_name=$("#product_name").val().trim();
	var description=$("#description").val().trim();
	var basic_charge=$("#basic_charge").val().trim();
	var hsn_code=$("#hsn_code").val().trim();
	var tax_rate=$("#tax_rate").val().trim();
	
	var submit=true;
	
	if(appliance_id=='')
	{
		alert('Please select appliance id');
		submit=false;
		return false;
	}
	if(product_name=='')
	{
		alert('Please enter Product name');
		submit=false;
		return false;
	}
	if(description=='')
	{
		alert('Please enter Description');
		submit=false;
		return false;
	}
	if(basic_charge=='')
	{
		alert('Please enter Basic charge');
		submit=false;
		return false;
	}
	else
	{
		if(!$.isNumeric(basic_charge) || basic_charge < 0)
		{
			alert('Please enter valid Basic charge');
			submit=false;
			return false;
		}
	}
	if(hsn_code=='')
	{
		alert('Please select hsn_code');
		submit=false;
		return false;
	}
	if(tax_rate=='')
	{
		alert('tax rate should not be blank');
		submit=false;
		return false;
	}
	if(submit==true)
	{
		var datastring=$("#form_add_product").serialize();
	}

	$.ajax({
	method: 'post',
	data: datastring,
	url: "<?php echo base_url() ?>employee/accessories/process_submit_add_product",
	beforeSend()
	{
		$("#submitform").val("Adding Accessories...");
		$("#submitform").prop('disabled',true);

		$("#response_div").hide();
		$("#response_div_s").hide();
		$("#response_div_e").hide();
	},
	success: function(data)
	{
		var returndata=JSON.parse(data);
		if(returndata.status!='error')
		{
			$('#appliance_id').select2();
			$('#hsn_code').select2();
			$("#appliance_id").select2("val", "");
			$("#hsn_code").select2("val", "");			
			$('#form_add_product').trigger("reset");
			$("#response_div_s").show();
		}
		else
		{
			$("#response_div_e").show();
			$("#response_div_e_span").html(returndata.msg);
		}
		$("#response_div").show();
		$("#submitform").val("Add Accessories");
		$("#submitform").prop('disabled',false);
	}
	});



	
}
           
  </script>
 
 </div>
</div>

 <style>
  .mandatory
  {
	  color: red;
	  font-weight: bold;
  }
  </style>