<div id="page-wrapper"> 
  <div class="container-fluid">
    <div class="row">
    	<h1 class="page-header" style="color:Blue;">
      	Add New Brands 
    	</h1>
    	<form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/booking/process_add_new_brand_form" >
    	  <table class="table table-striped table-bordered" style="width:500px;">
    	  	<tr>
    	  	  <th>S. No.</th>
    	  	  <th>Appliance</th>
    	  	  <th>Brand</th>
    	  	</tr>
    	  	<?php $count = 1; ?>
            <?php for($i=1;$i<=10;$i++){?>
        	  <tr>
        		<td><?php echo $count++;?>.</td>
        		<td width="200px;">
        			<select type="text" class="form-control"  id="new_brand" name="new_brand[]" 
        				value="<?php echo set_value('new_brand'); ?>" onchange="assign(this.value)">
        				<option>Select</option>
        				<?php foreach($services as $key => $values) {?>
        			  	<option  value=<?=$values->id;?>>
        				<?php echo $values->services; }?>
        			  	</option>
        			</select>
        		</td>
        		<td width="200px;">
        			<input type="text" name="brand_name[]" value="<?php echo set_value('brand_name'); ?>">
        		</td>
        	  </tr>
        	<?php }?>
    	  </table>
    	  <center>
          	<div><input type="Submit" value="Save" class="btn btn-primary btn-lg">
          	<input type="Reset" value="Cancel" class="btn btn-danger btn-lg"></div>
          </center>
    	</form>
    </div>
  </div>
</div>
