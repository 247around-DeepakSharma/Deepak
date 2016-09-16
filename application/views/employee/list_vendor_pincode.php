<style>
.search{
	margin-top:30px;
}
.col-md-4{
	margin-bottom: 20px;
}
</style>
<div id="page-wrapper" >
   <div class="container" >
      	<div class="panel panel-info" style="margin-top:20px;">
         	<div class="panel-heading"><center>Delete Vendor Pincode Mappings</center></div>
         	<div class="panel-body">
        		  <?php if(isset($delete)){
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Vendor pincodes have been deleted</strong>
                    </div>';
                    }
                    if(isset($not_found)){
                      ?>
                      <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Vendor not mapped to Pincode '

                        <?php   
                        foreach($not_found as $value) {
                          echo $value.' ';
                        }
                        ?>
                    '</strong>
                    </div>
                    
                    <?php }
                    if(isset($no_input)){
                      echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>No input selected</strong>
                    </div>';
                    }
                    ?>
         		<div class="row">
               <form name="myForm" class="form-horizontal" id ="vendor_form"   method="POST" enctype="form-data">
         			<div class="container">
         			<div class="col-md-12">


         			<?php for($i=0;$i<5;$i++) {?>		
         			<div class="col-md-4">
         				<div class="col-md-2" style="margin-left:-40px;">
         				<input type="checkbox" name='choice' value='<?php echo ($i+1)?>' id="check_<?php echo $i?>"/>
         				</div>

         				<div class="form <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-3" style="margin-left:-20px;">Appliance</label>
                           <div class="col-md-7" style="margin-left:90px">
                              <select type="text" class="form-control"  id="service_id_<?php echo $i?>" name="service_id[]" value = "<?php echo set_value('service_id'); ?>">
                                           <option value="">Select Appliance</option>
                                            <?php foreach ($appliance as $key => $value) { ?>
                                           <option  value=<?php echo $value->id;?> > <?php echo $value->services; } ?></option>
                               </select>

                           	</div>
                           	  <?php echo form_error('service_id'); ?>
                      	</div>
                    </div>
                    <div class="col-md-4">

                        <div class="form <?php if( form_error('pincode') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Pincode</label>
                           <div class="col-md-8">
                              <input type="text" class="form-control" id="pincode_<?php echo $i?>" name="pincode[]" value = "" placeholder="Please enter pincode" >
                            <?php echo form_error('pincode'); ?>
                           </div>
                           
                        </div>

                    </div>
                    <div class="col-md-4">

                        <div class="form <?php if( form_error('vendor_id') ) { echo 'has-error';} ?>">
                           <label for="name" class="col-md-4">Vendor</label>
                           <div class="col-md-8">
                               <select type="text" class="form-control"  id="vendor_<?php echo $i?>" name="vendor_id[]">
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendor_details as $key => $values) { ?>
                                           <option  value=<?= $values['id']; ?> > <?php echo $values['name']; } ?></option>
                               </select>
                               <?php echo form_error('vendor_id'); ?>
                           </div>
                           
                        </div>

                	</div>

                	<?php }?>

             		</div>
         			</div>

         			<div class="form search">
                          <center>
                         <input type="submit" id="submitform" class="btn btn-info " value="Delete"/>
                         </center>
                    </div>

          		</form>
         	</div>
        	</div>
    	</div>
    </div>
</div>

<script type="text/javascript">

$(document).ready(function(){
  $("#check_0").on('change', function() {
     if($(this).is(':checked')){
            $("#service_id_0,#pincode_0,#vendor_0").attr('required',true);
       }else{
            $("#service_id_0,#pincode_0,#vendor_0").removeAttr('required');
      }    
  });

  $("#check_1").on('change', function() {
       if($(this).is(':checked')){
            $("#service_id_1,#pincode_1,#vendor_1").attr('required',true);
       }else{
            $("#service_id_1,#pincode_1,#vendor_1").removeAttr('required');
      }    
  });

  $("#check_2").on('change', function() {
        if($(this).is(':checked')){
            $("#service_id_2,#pincode_2,#vendor_2").attr('required',true);
       }else{
            $("#service_id_2,#pincode_2,#vendor_2").removeAttr('required');
      }    
  });

  $("#check_3").on('change', function() {
        if($(this).is(':checked')){
            $("#service_id_3,#pincode_3,#vendor_3").attr('required',true);
       }else{
            $("#service_id_3,#pincode_3,#vendor_3").removeAttr('required');
      }    
  });

  $("#check_4").on('change', function() {
        if($(this).is(':checked')){
            $("#service_id_4,#pincode_4,#vendor_4").attr('required',true);
       }else{
            $("#service_id_4,#pincode_4,#vendor_4").removeAttr('required');
      }    
  });
});

</script>