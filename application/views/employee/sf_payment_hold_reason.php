<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
   .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
<div class="row">
<div  class = "panel panel-info" style="margin:20px;">
    <div class="panel-heading" style='height:auto;overflow:hidden'>
        <div class='col-md-6'>SF PAYMENT HOLD REASON</div>
        <div class='col-md-6' style='text-align:right'>
           <button onclick='open_sf_form()' class='btn btn-primary btn-sm' id='open_sf_form'>Add Reason</button> 
            
    </div></div>
   <div class="panel-body"  id='div_sf_payment_hold_reason_add' style='height:0px;overflow:hidden;padding:0px'>
      <div class='col-md-12'>
       <form name="rm_state_mapping" class="form-horizontal" id ="form_add_sf_payment_hold_reason" method="POST">
         <br>
         <div class='col-md-12' id='response_div' style='display:none;padding:0px'>
            <div class="alert alert-success" id='response_div_s'>
               <strong>Success!</strong> Payment hold reason added successfully.
            </div>
            <div class="alert alert-danger" id='response_div_e'>
               <strong>Error!</strong> <span id='response_div_e_span'></span>
            </div>
         </div>
         <br>
         <div class="row1" >
            <div class="col-md-12" >
               <div class="col-lg-3 col-md-3"></div>
               <div class="col-lg-6 col-md-6">
                  <div class="col-md-12">
                     <div  class="form-group">
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
                  <div class="col-md-12" style='padding: 0px;margin-bottom:10px'>
                     <div class="col-md-12" style='padding: 0px'>
                        <label  for="reason" class="col-md-12">Payment hold reason *</label>
                        <div class="col-md-12">                    
                           <textarea id='reason' name='reason' class='form-control' style='height:200px;resize: none;'></textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </form>
      <div class="panel-footer" align='center' style='height:auto;overflow:hidden'>		 	
         <input type="Submit" id='submitsfproduct' value="Submit" class="btn btn-primary" onclick="validate_form()">
      </div>
      </div>
   </div>
</div>

<div >
   <div class="panel panel-info" style="margin: 20px;">
      <div class="panel-heading" >
        SF PAYMENT HOLD LIST <img src='<?php echo base_url() ?>images/loader.gif' style='width:20px;display:none' id='imgloader'>
      </div>
      <div class="panel-body">
         <div class="row">
            <div class="col-md-12"  id='payment_hold_reson_list_div'>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {
    $('#annual_charges_report').DataTable({
     "processing": true, 
     "serverSide": false,  
     "dom": 'lBfrtip',
     "buttons": [
     {
      extend: 'excel',
      text: '<span class="fa fa-file-excel-o"></span>  Export',
      title: 'sf_payment_hold_list_<?php echo date('Ymd-His'); ?>',
      footer: true
     }  
     ],            
     "order": [],            
     "ordering": true,     
     "deferRender": true,
     //"searching": false,
     //"paging":false
     "pageLength": 10,
   "language": {                
      "emptyTable":     "No Data Found",
      "searchPlaceholder": "Search by any column."
     },
    });
   });
</script>
<style>
   #annual_charges_report_filter label
   {
   float: right !important;
   }
   #annual_charges_report_filter .input-sm
   {
   width: 272px !important;    
   }
   .dataTables_length label
   {
   float:left;
   }
   .dt-buttons
   {
   float:left;
   margin-left:85px;
   }
   .paging_simple_numbers
   {
   width: 45%;
   float: right;
   text-align: right;
   }
   .dataTables_info
   {
   width: 45%;
   float: left;
   padding-top: 30px;
   }
</style>
<script>
   $('#service_center').select2();
   
   function delete_payment_hold_reason(idtodelete)
   {
   
	   datastring="idtodelete="+idtodelete;
	   $.ajax({
	   method: 'post',
	   data: datastring,
	   url: "<?php echo base_url() ?>employee/invoice/sf_payment_hold_reason_delete",
	   beforeSend()
	   {
			$("#botton"+idtodelete).html("<i class='fa fa-spinner fa-spin' style='font-size: 17px;'></i>");
			$("#botton"+idtodelete).css('pointer-events','none');
			$("#botton"+idtodelete).css('opacity','none');
	   },
	   success: function(data)
	   {
			$("#rowid"+idtodelete).addClass('strikeout1');
                        $("#botton"+idtodelete).hide();
                        $("#span_"+idtodelete).html('Inactive');
                        $("#span_"+idtodelete).css('color','#c9302c');
                        bring_payment_hold_reson_list_div();
				   
	   }
	   });
   }
   
   
   function validate_form()
   {
   
   	var reason				=	$("#reason").val().trim();
   	var service_center		=	$("#service_center").val().trim();
   	var submit				=	true;
   	if(service_center=='')
   	{
   		alert('Please select service center.');
   		submit = false;
   return false;
   	}
   	if(reason=='')
   	{
   		alert('Please enter reason');
   		submit = false;
   return false;
   	}
   
   	if(submit==true)
   	{
   		var datastring=$("#form_add_sf_payment_hold_reason").serialize();
   		$.ajax({
   			method: 'post',
   			data: datastring,
   			url: "<?php echo base_url(); ?>employee/invoice/process_submit_sf_payment_hold_reason",
   			beforeSend: function()
   			{
				$("#submitsfproduct").val('Submitting.....');   			
				$("#submitsfproduct").prop('disabled',true);
				$("#response_div").hide();
				$("#response_div_s").hide();
				$("#response_div_e").hide();   
   			},
   			success: function(result)
   			{   			
				$("#submitsfproduct").val('Submit');
				var returndata=JSON.parse(result);
				if(returndata.status!='error')
				{
					$('#service_center').select2();
					$("#service_center").select2("val", "");		
					$('#form_add_sf_payment_hold_reason').trigger("reset");
					$("#response_div_s").show();
				}
				else
				{
					$("#response_div_e").show();
					$("#response_div_e_span").html(returndata.msg);
				}

				$("#response_div").show();				
				$("#submitsfproduct").prop('disabled',false);
				bring_payment_hold_reson_list_div();
   
   			}
   		});
   	}
   
   }
   $(document).ready(function(){
   bring_payment_hold_reson_list_div();		
   });
   
   function bring_payment_hold_reson_list_div()
   {
    var datastring ="bring_payment_hold_reson_list_div=bring_payment_hold_reson_list_div";
    $.ajax({
   			method: 'post',
   			data: datastring,
   			url: "<?php echo base_url(); ?>employee/invoice/sf_payment_hold_reason_list",
   			beforeSend: function()
   			{ 
				 $("#imgloader").show();
   			},
   			success: function(result)
   			{   			
   				 $("#payment_hold_reson_list_div").html(result);
   				 $("#imgloader").hide();
   			}
   		});
   }
   
   function open_sf_form()
   {
    // $("#div_sf_payment_hold_reason_add").css('height','auto');
     $("#div_sf_payment_hold_reason_add").toggle(function(){
        $(this).animate({height:2},10);
    },function(){
        $(this).animate({height:480},10);
    });
   }
   $(document).ready(function(){
      open_sf_form(); 
   });

</script>