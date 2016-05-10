<div id="page-wrapper">
  <div class="">
    <div class="row">

          
      <div style="width:60%;margin:50px;">
                      <?php if($this->session->userdata('error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
               }
               ?>
        <h2><b>Re-Assign Vendor</b></h2>
        <form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/vendor/process_reassign_vendor_form" >
          <table class="table table-striped table-bordered">
        	<tr>
           	<th style="width:5%">Serial No.</th>
            <th style="width:45%">Booking Id</th>
        	  <th style="width:50%">Service Center</th>
        	</tr>
        	<?php $count = 1; ?>		
        	  <tr>
        		<td><?php echo $count; $count++;?>.</td>
        		<td><input type="text" class="form-control" value= "<?php echo $booking_id;?>" name="booking_id" placeholder="Please Enter Booking Id Here.." required></td>
        		<td>
        		  <select type="text" class="form-control" id="service_center" name="service" value="<?php echo set_value('service_center'); ?>" onchange="assign(this.value)">
        		  	<option>Select</option>
        			<?php foreach($service_centers as $key => $values) {?>
        			  <option  value=<?php echo $values->id;?>>
        				<?php echo $values->name; }?>
        			  </option>
        			  <?php echo form_error('service_center'); ?>
        		  </select>
        		</td>
        	  </tr>
        	
          </table>
          <center>
          	<div><input type="Submit" value="Save" class="btn btn-primary btn-lg">
          	<input type="Reset" value="Cancel" class="btn btn-danger btn-lg"></div>
          </center>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('#service_center').select2();
</script>
<?php $this->session->unset_userdata('error'); ?>