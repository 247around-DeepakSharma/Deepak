<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
 
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective Parts Shipped</h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <form target="_blank"  action="<?php echo base_url(); ?>employee/service_centers/print_partner_address_challan_file" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">Age of Shipped</th>
                            <th class="text-center">Parts Shipped</th>
                            <th class="text-center">Parts Code Shipped</th>
                            <th class="text-center">Shipped Date</th>
                            <th class="text-center">Courier Name</th>
                            <th class="text-center">AWB No</th>
                             <th class="text-center" >Courier Charges <i style="color: red;" class="fa fa-info-circle infocourier" data-toggle="tooltip"  title="Courier Charges Split On Every Booking"></i></th>
                            <th class="text-center">Remarks By SF</th>
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php  foreach($spare_parts as $key =>$row){ ?>
                               <tr style="text-align: center;<?php if(!is_null($row['remarks_defective_part_by_partner'])){ echo "color:red"; }?>">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                        <?php if(!is_null($row['defective_part_shipped_date'])){  $age_shipped = date_diff(date_create($row['defective_part_shipped_date']), date_create('today'));   echo $age_shipped->days. " Days";} ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php   if(empty($row['defective_part_shipped'])){echo $row['defective_part_shipped'];}else{echo $row['defective_part_shipped'];}   ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['part_number']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo date_format(date_create($row['defective_part_shipped_date']),"d-M-Y"); ?>
                                    </td>
                                    <td> <?php echo strtoupper($row['courier_name_by_sf']); ?></td> 
                                    <td><a style="cursor: pointer;" onclick="get_awb_details('<?php echo $row['courier_name_by_sf']; ?>','<?php echo $row['awb_by_sf']; ?>','<?php echo DEFECTIVE_PARTS_SHIPPED; ?>','')"><?php echo $row['awb_by_sf']; ?></a></td>
                                    <td>
                                        <?php echo $row['courier_charges_by_sf']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['remarks_defective_part_by_sf'])){  echo $row['remarks_defective_part_by_sf']; } else { echo $row['remarks_by_partner'];} ?>
                                    </td>
                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
 
                  </form>

                        </div>
                   
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
</div>
     <!-- model -->
    <div id="gen_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="gen_model_title"></h4>
                </div>
                <div class="modal-body" id="gen_model_body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

 
 <div class="loader hide"></div>
 <style>
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>

<script>
    function get_awb_details(courier_code,awb_number,status,id){
        if(courier_code && awb_number && status){
           $('.loader').removeClass('hide');
            $.ajax({
                method:"POST",
                data : {courier_code: courier_code, awb_number: awb_number, status: status},
                url:'<?php echo base_url(); ?>courier_tracking/get_awb_real_time_tracking_details',
                success: function(res){
                    $('.loader').addClass('hide');
                    $('#gen_model_title').html('<h3> AWB Number : ' + awb_number + '</h3>');
                    $('#gen_model_body').html(res);
                    $('#gen_model').modal('toggle');
                }
            });
        }else{
            alert('Something Wrong. Please Refresh Page...');
        }
    }


$(".infocourier").click(function(){
swal("Courier charges shown are equally divided in every booking which are sent in defective courier.");
});


</script>
<style type="text/css">
  .sweet-alert {

    width: 700px !important;
    left: 46% !important;
  }
  .modal-lg {
    /* width: 1300px; */
    width: 50% !important;
}
.form-control{
    margin-bottom: 10px;
}
.input-group{
   margin-bottom: 10px; 
}
</style>
