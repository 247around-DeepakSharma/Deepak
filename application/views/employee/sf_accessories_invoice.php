<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;">
            <div class="panel-heading" style="font-size:130%;">
                <b>
                    <center>SF PRODUCT INVOICE</center>
                </b>
            </div>
            <div class="panel-body">
                
                <form name="rm_state_mapping" class="form-horizontal" id ="sf_product_invoice" method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div  class="form-group <?php
                        if (form_error('rm_asm')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="rm_asm" class="col-md-12">Service Center *</label>							
                            <div class="col-md-12">							
                                <select id="service_center" class="form-control" name ="service_center" required>
                                    <option selected   value=''>Select Service Center</option>
                                    <?php foreach ($sf_list as $key => $value) {										
										?>
                                    <option value ="<?php echo $value['id']; ?>"> <?php echo $value['name']; ?> </option>
                                    <?php  } ?>
                                </select>
                              <?php echo form_error('rm_asm'); ?>
                            </div>
                        </div>
                    </div>


					<div class="col-md-4">
                            <label  for="product" class="col-md-12">Product *</label>
							
                            <div class="col-md-12">							
                                <select id="product" class="form-control" name ="product" required>
                                    <option selected  value=''>Select Product</option>
                                    <?php foreach ($services_name as $key => $value) { 									
										?>
                                    <option value ="<?php echo $value['id']; ?>" > <?php echo $value['product_name']; ?> </option>
                                    <?php  } ?>
                                </select>
                              <?php echo form_error('product'); ?>
                            </div>
                        </div>
                    



					<div class="col-md-4">
                        <div  class="form-group <?php
                        if (form_error('product')) {
                            echo 'has-error';
                        }
                        ?>">
                            <label  for="rm_asm" class="col-md-12">Quantity</label>							
                            <div class="col-md-12">							
                                <select id="quantity" class="form-control" name ="quantity" required>
                                    <option selected  value=''>Select Quantity</option>
                                    <?php foreach ($quantity_list as $key => $value) { 
										
										?>
                                    <option value ="<?php echo $value; ?>" > <?php echo $value; ?> </option>
                                    <?php  } ?>
                                </select>
                              <?php echo form_error('price'); ?>
                            </div>
                        </div>
                    </div>
					</div>
                    
									
					 </form>				
            <div class="panel-footer" align='center'>
                                <input type="Submit" id='submitsfproduct' value="Submit" class="btn btn-primary" onclick="validate_form()">
                        </div>
               
            </div>
        </div>
    </div>


	<script>
	$('#service_center').select2();
	$('#product').select2();
	$('#quantity').select2();
	
	function validate_form()
	{
		var quantity			=	$("#quantity").val().trim();
		var product				=	$("#product").val().trim();
		var service_center		=	$("#service_center").val().trim();
		var submit				=	true;
		if(service_center=='')
		{
			alert('Please select service center.');
			submit = false;
		}
		if(product=='' && submit==true)
		{
			alert('Please select product.');
			submit = false;
		}
		if(quantity=='' && submit==true)
		{
			alert('Please select quantity.');
			submit = false;
		}
		if(submit==true)
		{
			var datastring=$("#sf_product_invoice").serialize();
			$.ajax({
				method: 'post',
				data: datastring,
				url: "<?php echo base_url(); ?>employee/",
				beforeSend: function()
				{
					$("#submitsfproduct").val('Updating....');
					$("#submitsfproduct").css('pointer-events','none');
					$("#submitsfproduct").css('opacity','.6');

				},
				success: function(result)
				{
					alert(data);
					$("#submitsfproduct").val('');
					$("#submitsfproduct").css('pointer-events','');
					$("#submitsfproduct").css('opacity','1');

				}
			});
		}

	}
	</script>
