<?php $offset = $this->uri->segment(7);  ?>
<script>
    $(function(){

      $('#dynamic_select').bind('change', function () {
          var url = $(this).val();
          if (url) {
              window.location = url;
          }
          return false;
      });
    });

function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");

        if (confirm_call == true) {

             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);

                }
            });
        } else {
            return false;
        }

}

</script>


<div id="page-wrapper" >
    <div class="">
        <div class="row">
<!--            <div class="col-md-6 services">
                <select class="form-control" id="services" name="services">
                    <option selected disabled>Select services</option>
                        <?php foreach ($services as $key => $values) { ?>
                            <option  value="<?php echo $values['services']; ?>">
                                <?php echo $values['services'];}?>
                            </option>
                </select>
            </div>-->
            <?php  if($this->uri->segment(3) == 'view_queries' || $this->uri->segment(3) == 'finduser'){ $status = $this->uri->segment(4); $pv = $this->uri->segment(5);; ?>
            <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status."/".$pv; ?>" <?php if($this->uri->segment(6) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status."/".$pv.'/100/0'?>" <?php if($this->uri->segment(6) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status."/".$pv.'/200/0'?>" <?php if($this->uri->segment(6) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/booking/view_queries/'.$status."/".$pv.'/500/0'?>" <?php if($this->uri->segment(6) == 500){ echo 'selected';}?>>500</option>
                    <!--<option value="<?php echo base_url().'employee/booking/view_queries/'.$status."/".$pv.'/0/All'?>" <?php if($this->uri->segment(7) == 'All'){ echo 'selected';}?>>All</option>-->

                </select>
            </div>
            <?php } ?>
             <div class="input-filter-container"><label for="input-filter">Search:</label> <input type="search" id="input-filter" size="15" placeholder="search"></div>
            <div style="margin-left:10px;margine-right:5px;">
                <h1 align="left"><b><?php if($status == "FollowUp"){ echo "Pending Queries"; } else if($Bookings[0]->current_status == "FollowUp"){ echo "Pending Queries";} else { echo "Cancelled Queries"; } ?> </b></h1>
                    <?php
                    if (!empty($this->session->flashdata('success'))) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
                }
                 if (!empty($this->session->flashdata('error'))) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('error') . '</strong>
                   </div>';
                }
                ?>
                <div id="queries_data">
                <table class="table table-bordered table-hover table-striped">

                    <thead>
                    <tr>
                    <th>S No.</th>
                    <th >Booking Id</th>

                    <th>User Name</th>
                    <th >Phone No.</th>
                    <th >Service Name</th>
                    <th >Booking Date/Time</th>
                    <?php if($status != "Cancelled"){?>
                    <th  >Status</th>
                     <?php } ?>
                    <th >City</th>

                    <th >Query Remarks</th>
                     
                    <?php if($status != "Cancelled"){ ?>
                    <?php if($p_av == PINCODE_NOT_AVAILABLE){ ?>
                    <th >Pincode</th>
                    <?php } else { ?>
                    <th >Vendor Status</th>
                   <?php } } ?>
                    <th  >Call</th>
                    <th >View</th>
                     <?php if($status != "Cancelled"){?>
                    <th >Update</th>

                    <th >Cancel</th>
                    <?php } if($status == "Cancelled"){ ?>
                     <th >Un-Cancel</th>
                    <?php } ?>

                    </tr>

                    </thead>
                    <?php $count = 1; if($offset ==0){ $offset = 1;} else { $offset = $offset+1; } ?>
                    <?php foreach($Bookings as $key =>$row){?>

                    <tr <?php if($row->internal_status == "Missed_call_confirmed"){ ?> style="background-color:rgb(162, 230, 162); color:#000;"<?php } ?> >
                    <td><?php echo $offset; ?></td>

                    <td><?= $row->booking_id; ?></td>
                    <input type="hidden" id="<?php echo "service_id_".($key +1); ?>"  value="<?php echo $row->service_id;?>"/>
                    <input type="hidden" id="<?php echo "pincode_".($key +1); ?>" value="<?php echo $row->booking_pincode; ?>" />
                    <td><a href="<?php echo base_url(); ?>employee/user/finduser/0/0/<?php echo $row->phone_number; ?>"><?php echo $row->customername; ?></a></td>
                    <td><a href="<?php echo base_url();?>employee/user/finduser/0/0/<?php echo $row->phone_number;?>"><?php echo $row->booking_primary_contact_no; ?></a></td>
             
                    <td><?= $row->services;  ?></td>
                   
                    <td ><?= $row->booking_date; ?> / <?= $row->booking_timeslot; ?></td>
                    <?php if($status !="Cancelled"){ ?>
                    <td  id="status_<?php echo $row->booking_id; ?>">
                        <?php
                            echo $row->current_status;
                            if ($row->current_status != $row->internal_status){
                                echo " (" . $row->internal_status . ")";
                            }
                        ?>
                    </td>
                    <?php } ?>
                     <td ><?= $row->city; ?></td>


                    <td ><?= $row->query_remarks; ?></td>
                    <?php  if($status != "Cancelled"){  if($p_av == PINCODE_NOT_AVAILABLE){?>
                    <td><a href="javascript:void(0)" style="color: red;" onclick='form_submit("<?php echo $row->booking_id?>")'><?php print_r($row->booking_pincode); ?></a></td>
                    <?php } else if($p_av == PINCODE_ALL_AVAILABLE || $p_av == PINCODE_AVAILABLE ){ ?>
                    <td >
                        
                        <select id="<?php  echo "av_vendor". ($key+1); ?>" style="max-width:100px;">
                            <option>Vendor Available</option>
                            
                        </select>
                        
                        <a href="javascript:void(0)" style="color: red; display:none" id="<?php echo "av_pincode".($key +1); ?>" onclick='form_submit("<?php echo $row->booking_id?>")'><?php print_r($row->booking_pincode); ?></a>
                    
                    </td>
                    <?php } }  ?>

                    <td ><button type="button" onclick="outbound_call(<?php echo $row->booking_primary_contact_no; ?>)" class="btn btn-sm btn-info"><i class = 'fa fa-phone fa-lg' aria-hidden = 'true'></i></button>
                     </td>

                    <td >
                        <?php echo "<a class='btn btn-sm btn-primary' "
                        . "href=" . base_url() . "employee/booking/viewdetails/$row->booking_id target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                        ?>
                    </td>
 <?php if($status !="Cancelled"){ ?>
                    <td  ><?php
                        echo "<a class='btn btn-small btn-success btn-sm' href=".base_url()."employee/booking/get_edit_booking_form/$row->booking_id title='Update'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                        ?>
                    </td>

                    <td >
                        <?php
                        echo "<a class='btn btn-small btn-warning btn-sm' href=".base_url()."employee/booking/get_cancel_form/$row->booking_id/FollowUp title='Cancel'> <i class='fa fa-times' aria-hidden='true'></i></a>";
                        ?>
                    </td>
                    <?php } if($status == "Cancelled"){  ?>
                     <td>
                        <?php echo "<a class='btn btn-sm btn-warning' "
                        . "href=" . base_url() . "employee/booking/open_cancelled_query/$row->booking_id title='open'><i class='fa fa-calendar' aria-hidden='true'></i></a>";
    ?>
                    </td>
                    <?php } ?>
                    </tr>
                    <?php $count++; $offset++;
                    }?>

                </table>
                 <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
                </div></div>
        </div>
    </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<script src="<?php echo base_url();?>js/jquery.filtertable.min.js"></script>

<script>

    $(document).ready(function() {
        $('table').filterTable({ // apply filterTable to all tables on this page
            inputSelector: '#input-filter' // use the existing input instead of creating a new one
        });
        <?php if($status  != "Cancelled" && $p_av != PINCODE_NOT_AVAILABLE){ ?>
        var total_booking = Number(<?php echo count($Bookings); ?>);
        for(var c = 1; c<= total_booking; c++  ){
            var index = c;
            var  service_id = $("#service_id_"+ c).val();
            var pincode = $("#pincode_"+ c).val();
            if(pincode !== ""){
                get_vendor(pincode, service_id, index);
            } else {
                $("#av_vendor"+index).css("display","none");
                 $("#av_pincode"+index).css("display","inherit");
            }
            
            
        }
        <?php } ?>
    });
    
    function get_vendor(pincode, service_id, index){
        $.ajax({
                type:"POST",
                url:"<?php echo base_url()?>employee/vendor/get_vendor_availability/"+pincode+"/"+service_id,
                
                success: function(data){
                    if(data !== ""){
                       $("#av_vendor"+index).html(data); 
                    } else {
                        $("#av_vendor"+index).css("display","none");
                        $("#av_pincode"+index).css("display","inherit");
                    }
                    
                }
        });
        
    }
    
   
    function load_vendor_details(div){
        var vendor_id = $("#vendor_avalilabe"+div).val();
        document.location.href= '<?php echo base_url(); ?>employee/vendor/viewvendor/'+vendor_id;
    }

    function form_submit(booking_id){
        $.ajax({
                type:"POST",
                data:{booking_id:booking_id},
                url:"<?php echo base_url()?>employee/vendor/get_add_vendor_to_pincode_form",
                success: function(data){
                    $("#page-wrapper").html(data);
                }
        });
    }
</script>
<style>
    /* generic table styling */
    table { border-collapse: collapse; }
    td { padding: 5px; }

    td { border-bottom: 1px solid #ccc; }
    /* filter-table specific styling */
    td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
    /* special filter field styling for this example */
    .input-filter-container { position: absolute; top: 7em; right: 1em; border: 2px solid #66f; background-color: #eef; padding: 0.5em; }
</style>

<!--<script>
    $(document).ready(function(){
        $('#services').change(function(){
        var services = $('#services').val();
        var status = '<?php echo $status ;?>';
        var pv = '<?php echo $pv; ?>';
        $.ajax({
                 type: 'POST',
                 data:{service : services},
                 url: '<?php echo base_url(); ?>employee/booking/view_queries_ajax'+'/'+status+'/'+pv+'/'+services,
                 success: function (data) {
                  $('#queries_data').html(data);

                 }
               });
        }); 
     });
        $('.custom_pagination a').click(function(){
           alert("dss");
        }); 
</script>-->
