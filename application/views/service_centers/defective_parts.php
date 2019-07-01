<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
      <?php if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
               ?> 
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective Parts Need To Be Shipped</h1>
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
                            <th class="text-center">Age of Pending</th>
                            <th class="text-center">Parts Received</th>
                            <th class="text-center">Parts Code </th>
                             <th class="text-center">Amount</th>
                            <th class="text-center">Remarks By Partner</th>
                            <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                            <th class="text-center" >Challan<input type="checkbox" id="selectall_challan_file" > </th>                            
                            <th class="text-center">Update</th>
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
                                        <?php if(!is_null($row['service_center_closed_date'])){  $age_shipped = date_diff(date_create($row['service_center_closed_date']), date_create('today'));   echo $age_shipped->days. " Days";} ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['parts_shipped']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['part_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['challan_approx_value']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['remarks_defective_part_by_partner'])){  echo $row['remarks_defective_part_by_partner']; } else { echo $row['remarks_by_partner'];} ?>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-control checkbox_address" onclick="remove_select_all()" name="download_address[]"  value="<?php echo $row['id'];?>" />
                                    </td>
                                    <td>

                                      <?php if(!$partner_on_saas){ ?>
                                             <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan()" name="download_challan[]"  value="<?php echo $row['challan_file'];?>" />
                                     <?php  }else{ ?>

                                      <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan()" name="download_challan[<?php echo $row['partner_id'];  ?>][]"  value="<?php echo $row['id']?>" />

                                      <?php } ?>
                                       
                                    </td>                                  
                      
                                    <td>
                                         <a href="<?php echo base_url() ?>service_center/update_defective_parts/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                    </td>

                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
                      
                      <center> 
                          <input type= "submit"  class="btn btn-danger" onclick='return check_checkbox()' style="text-align: center; background-color:#2C9D9C; border-color: #2C9D9C;"  value ="Print Shipment Address / Challan File" >
                      </center>
                  </form>

                        </div>
                   
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
</div>

<script>
function check_checkbox(){
   
    var flag =0;
    //$('.checkbox_address').each(function (i) {
       
        var d_m = $('.checkbox_address:checked');
        if(d_m.length > 0){
            flag = 1;  
       }
       
       if(flag === 0){
           var c_m = $('.checkbox_challan:checked');
           if(c_m.length > 0){
               flag = 1;  
           }
       }

    //});
        
    if(flag ===0 ){
        alert("Please Select Atleast One Checkbox");
        return false;
    }
}

$("#selectall_address").change(function(){
       var d_m = $('.checkbox_challan:checked');
        if (d_m.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
        }
       
       $(".checkbox_address").prop('checked', $(this).prop("checked"));
});

$("#selectall_challan_file").change(function () {
        var d_m = $('.checkbox_address:checked');
        if (d_m.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }
        $(".checkbox_challan").prop('checked', $(this).prop("checked"));
    });
    
function remove_select_all(){
    $('#selectall_address').prop('checked', false); 
    var d_m = $('.checkbox_challan:checked');
    if (d_m.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
    }
}

function remove_select_all_challan(){
    $('#selectall_challan_file').prop('checked', false); 
    var d_m = $('.checkbox_address:checked');
    if (d_m.length > 0 || d_m_d.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('#selectall_address').prop('checked', false);
    }
}

</script>
