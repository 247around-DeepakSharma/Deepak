<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   
      <a href="<?php echo base_url(); ?>partner/download_spare_parts" title ="Download All Booking" class='btn btn-md btn-warning  pull-right' style="margin-right: 40px;margin-top:15px; margin-bottom: 15px;"><i class="fa fa-download" aria-hidden="true"></i></a>
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
       <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" style="font-size:24px;"><i class="fa fa-money fa-fw"></i> Pending Spares On <?php echo $this->session->userdata('partner_name')?></h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                    <form target="_blank"  action="<?php echo base_url(); ?>partner/print_all" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">Age of Requested</th>
                            <th class="text-center">Parts Required</th>
                            <th class="text-center">Model Number</th>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Problem Description</th>
                            <th class="text-center">Update</th>
                            <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                            <th class="text-center" >Courier Manifest <input type="checkbox" id="selectall_manifest" ></th>
                            
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php $sn_no1 = 1; foreach($spare_parts as $key =>$row){?>
                                <tr style="text-align: center;">
                                    <td>
                                        <?php echo $sn_no1; ?>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                         <a  style="color:black" href="<?php echo base_url();?>partner/booking_details/<?php echo $row['booking_id'];?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
                                    <td>
                                        <?php echo $row['age_of_request']. " days"; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['parts_requested']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['model_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['serial_number']; ?>
                                    </td>
                                    
                                    <td>
                                        <?php echo $row['remarks_by_sc']; ?>
                                    </td>
                                    
                                    <td>
                                        <a href="<?php echo base_url() ?>partner/update_spare_parts_form/<?php echo $row['booking_id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-control checkbox_address" name="download_address[]" onclick='check_checkbox(1)' value="<?php echo $row['booking_id'];?>" />
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-control checkbox_manifest" name="download_courier_manifest[]" onclick='check_checkbox(0)' value="<?php echo $row['booking_id'];?>" />
                                    </td>
                                   
                                </tr>
                                <?php $sn_no1++; } ?>
                            </tbody>
                        </table>
                        
                            <input type= "submit"  class="btn btn-danger btn-md col-md-offset-4" style="background-color:#2C9D9C; border-color: #2C9D9C;" name="download_shippment_address" value ="Print Address/Courier Mainfest" >
                           
                        </div>
                    </form>
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="custom_pagination" style="margin-left: 16px; margin-bottom:40px;" > <?php if(isset($links)) echo $links; ?></div>
</div>

<script type="text/javascript">
     $(".shipped_date").datepicker({dateFormat: 'yy-mm-dd'});
//     $(document).ready(function() {
//          $('body').popover({
//           selector: '[data-popover]',
//           trigger: 'click hover',
//           placement: 'auto',
//           delay: {
//               show: 50,
//               hide: 100
//           }
//        });
//     });
 $(document).ready(function() {
$('body').popover({
           selector: '[data-popover]',
           trigger: 'click hover',
           placement: 'auto',
           delay: {
               show: 50,
               hide: 100
           }
        });
         } );
         
    $("#selectall_address").change(function(){
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        if(d_m.length > 0){
            $('.checkbox_manifest').prop('checked', false); 
            $('#selectall_manifest').prop('checked', false); 
        }
       $(".checkbox_address").prop('checked', $(this).prop("checked"));
    });
   $("#selectall_manifest").change(function(){
       var d_m = $('input[name="download_address[]"]:checked');
       if(d_m.length > 0){
            $('.checkbox_address').prop('checked', false); 
            $('#selectall_address').prop('checked', false); 
        }
     $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
  });
  
  function check_checkbox(number){
      
      if(number === 1){
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        if(d_m.length > 0){
            $('.checkbox_manifest').prop('checked', false); 
            $('#selectall_manifest').prop('checked', false); 
        }
          
      } else if(number === 0){
         var d_m = $('input[name="download_address[]"]:checked');
        if(d_m.length > 0){
             $('.checkbox_address').prop('checked', false); 
             $('#selectall_address').prop('checked', false); 
         }
      }
      
  }
</script>

 <?php $this->session->unset_userdata('success'); ?>
