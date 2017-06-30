<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>

<div class="container-fluid">
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
            <?php if($this->session->flashdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>' . $this->session->flashdata('error') . '</strong>
                </div>';
                }
                ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title" style="font-size: 24px;"><i class="fa fa-money fa-fw"></i> Pending Bookings </h1>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Booking ID</th>
                                    <th>Call Type</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Mobile</th>
                                    <th>City</th>
                                    <th>Booking Date</th>
                                    <th>Edit Booking</th>
                                    <th>Reschedule</th>
                                    <th>Cancel</th>
                                    <th>JobCard</th>
                                    <th>Escalate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $key =>$row){?>
                                <tr>
                                    <td ><?php if($row->is_upcountry == 1 && $row->upcountry_paid_by_customer == 0) { ?>
                                        <i style="color:red; font-size:20px;" onclick="open_upcountry_model('<?php echo $row->booking_id;?>', '<?php echo $row->amount_due;?>')"
                                       class="fa fa-road" aria-hidden="true"></i><?php } ?>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td >
                                        <a style="color:blue;" href="<?php echo base_url();?>partner/booking_details/<?=$row->booking_id?>" target='_blank' title='View'> <?php
                                            echo  $row->booking_id;
                                            
                                            ?></a>
                                    </td>
                                    
                                    <td>
                                        <?php switch ($row->request_type){
                                            case "Installation & Demo":
                                                echo "Installation";
                                                 break;
                                            case "Repair - In Warranty":
                                            case "Repair - Out Of Warranty":
                                                echo "Repair";
                                                 break;
                                            default:
                                                    echo $row->request_type;
                                                    break;
                                            
                                            
                                            }  ?>
                                    </td>
                                    <td><?php echo  $row->appliance_brand; ?></td>
                                    <td><?php if(!empty($row->status)){
                                        switch ($row->status){
                                            case "Delivered":
                                                echo 'Spare Parts Received By SF';
                                                break;
                                            case "Shipped":
                                                echo 'Spare Parts Shipped By '. $this->session->userdata('partner_name');
                                                break;
                                            default :
                                                echo $row->status;
                                                break;
                                            
                                        }
                                    } else { echo $row->current_status;}?></td>
                                    <td> 
                                        <?=$row->customername;?>
                                    </td>
                                    <td>
                                        <?= $row->booking_primary_contact_no; ?>
                                    </td>
                                    <td>
                                        <?= $row->city; ?>
                                    </td>
                                    <td>
                                        <?= $row->booking_date; ?>
                                    </td>
                                    <td style="text-align: center"><a class='btn btn-sm btn-primary' href="<?php echo base_url();?>partner/update_booking/<?=$row->booking_id?>"  title='View' style="background-color:#2C9D9C; border-color: #2C9D9C;"><i class='fa fa-pencil-square-o' aria-hidden='true' ></i></a></td>
                                    <td style="text-align: center">
                                        <a <?php if($row->type == "Query"){ ?> style="pointer-events: none;background: #ccc;border-color:#ccc;" <?php } ?> href="<?php echo base_url(); ?>partner/get_reschedule_booking_form/<?php echo $row->booking_id; ?>" id="reschedule" class="btn btn-sm btn-success" title ="Reschedule"><i class='fa fa-calendar' aria-hidden='true' ></i></a>
                                    </td>
                                    <td style="text-align: center"><a href="<?php echo base_url(); ?>partner/get_cancel_form/Pending/<?php echo $row->booking_id; ?>" class='btn btn-sm btn-danger' title='Cancel'><i class='fa fa-times' aria-hidden='true'></i></a></td>
                                    <td style="text-align: center"><a href="javascript: w=window.open('https://s3.amazonaws.com/bookings-collateral/jobcards-pdf/<?php echo $row->booking_jobcard_filename; ?>'); w.print()" class='btn btn-sm btn-primary btn-sm' target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a></td>
                                    <td style="text-align: center">
                                        <a <?php if($row->type == "Query"){ ?> style="pointer-events: none;background: #ccc;border-color:#ccc;" <?php } ?> href="#" class='btn btn-sm btn-warning open-AddBookDialog' data-id= "<?php echo $row->booking_id;?>" data-toggle="modal" data-target="#myModal" title="Escalate"><i class="fa fa-circle" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end  col-md-12-->
        </div>
            <div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
    </div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Escalate Form</h4>
            </div>
            <div class="modal-body">
                 <center><h4 id ="failed_validation" style="color:red;margin-top: 0px;margin-bottom: 35px;"></h4></center>
                <div class="container">
                   
                    <!--<div class="row">-->
                        <div class="col-md-8">
                            <form class="form-horizontal" action="#" method="POST" target="_blank" >
                            <div class="form-group">
                                <label for="Booking Id" class="col-md-2">Booking Id</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="booking_id" id="ec_booking_id" placeholder = "Booking Id"  readonly>
                                </div>
                            </div>
                            <div class="form-group  <?php if( form_error('escalation_reason_id') ) { echo 'has-error';} ?>">
                                <label for="Service" class="col-md-2">Reason</label>
                                <div class="col-md-6">
                                    <select class=" form-control" name ="escalation_reason_id" id="escalation_reason_id">
                                        <option selected disabled>----------- Select Reason ------------</option>
                                        <?php 
                                            foreach ($escalation_reason as $reason) {     
                                            ?>
                                        <option value = "<?php echo $reason['id']?>">
                                            <?php echo $reason['escalation_reason'];?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <?php echo form_error('escalation_reason_id'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Escalation Remarks" class="col-md-2">Remarks</label>
                                <div class="col-md-6">
                                    <textarea  class="form-control" id="es_remarks" name="escalation_remarks" placeholder = "Remarks" ></textarea>
                                </div>
                            </div>
                            <div class="form-group ">
                                <input type= "submit"  onclick="return form_submit()" class=" col-md-3 col-md-offset-4 btn btn-primary btn-lg" value ="Save" style="background-color:#2C9D9C; border-color:#2C9D9C;">
                            </div>
                        </div>
                    </div>
                <!--</div>-->
            </div>
        </div>
    </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<script>
    $(document).on("click", ".open-AddBookDialog", function () {
        var myBookId = $(this).data('id');
        $(".modal-body #ec_booking_id").val( myBookId );
    });
    
    function form_submit(){
        var escalation_id = $("#escalation_reason_id").val();
        var booking_id = $("#ec_booking_id").val();
        var remarks = $("#es_remarks").val();
        
        if(escalation_id ===  null){
            $("#failed_validation").text("Please Select Any Escalation Reason");
           
        }  else {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>partner/process_escalation/'+booking_id,
                data: {escalation_reason_id: escalation_id,escalation_remarks:remarks},
                success: function (data) {
//                  console.log(data);

                }
              });
            
        }
        $('#myModal').modal('toggle');
        return false;
    }
    
</script>