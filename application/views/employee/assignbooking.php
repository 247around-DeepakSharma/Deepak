    <div id="page-wrapper">
        <div class="">
            <div class="row">
                <div style="width:auto;margin:50px;">
                    <h2><b>Assign Booking to Service Center</b></h2>
                    <form id="myForm" class="form-horizontal"  method="POST"  onsubmit="return submitForm();" name="fileinfo">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>Serial No.</th>
                                <th>Booking Id</th>
                                <th>Customer Name</th>
                                <th>Appliance</th>
                                <th>Booking Date</th>
                                <th>Booking Address</th>
                                <th>Pincode</th>
                                <th>Service Center</th>
                            </tr>
                            <?php $count = 0; ?>
                            <?php foreach($data as $key =>$row){?>
                            <tr>
                                <td>
                                    <?php echo $count+1; ?>.</td>
                                <td>
                                    <?=$row['booking_id'];?>
                                </td>
                                <td>
                                    <?=$row['name'];?>
                                </td>
                                <td>
                                    <?=$row['services'];?>
                                </td>
                                <td>
                                    <input type="hidden" id="particular_booking_date<?php echo $count; ?>" name="particular_booking_date<?php echo $count; ?>" value="<?= $row['booking_date']; ?>">
                                    <?= $row['booking_date']; ?>
                                </td>
                                <td>
                                    <?= $row['booking_address']; ?>
                                </td>
                                <td>
                                    <?= $row['booking_pincode']; ?>
                                </td>
                            <input type="hidden" name="sf_status[<?= $row['booking_id']; ?>]" value="<?php echo $results[$count][0]['sf_status']; ?>" />

                                <td style="width:200px;">
                                    <select type="text" class="js-example form-control" id="service_center<?php echo $count; ?>" name="service_center[<?= $row['booking_id']; ?>]" value="<?php echo set_value('service_center'); ?>">
                                        <option selected disabled>Select</option>
                                        <option value=""> Select</option>
                                        <?php foreach($results[$count] as $key => $values) {?>
                                            <option value=<?=$values['id'];?>>
                                                <?php echo $values['name'];
                                        } ?>
                                            </option>
                                            <?php echo form_error('service_center'); ?>
                                    </select>
                                </td>
                            </tr>
                            <?php $count++; }?>
                            <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                           
                        </table>
                        <center>
                            <input type="hidden" name="agent_id" value="<?php echo  $this->session->userdata('id'); ?>" />
                            <input type="hidden" name="agent_name" value="<?php echo  $this->session->userdata('employee_id'); ?>" />
                            <div id="loading">
                                <input type="Submit" id="submit_button" value="Save" class="btn btn-primary btn-lg">
<!--                                <input type="Reset" value="Cancel" class="btn btn-danger btn-lg">-->
                            </div>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
   $(".js-example").select2();
//   $('#myForm').one('submit', function() {
//   
//});
</script>

<script>
function submitForm() {
   
  var no_item =0;
  $("select.js-example").each(function (i) {
     if($(this).val() === null || $(this).val() ===""){
         
     }  else{
         no_item = no_item+1;
     }
    });
    if(no_item < 30){
        $(this).find('input[type="submit"]').attr('disabled','disabled');
        
        var html = "<img src='<?php echo base_url(); ?>images/loader.gif' />";
        $('#submit_button').hide();
        $('#loading').append(html);
        var fd = new FormData(document.getElementById("myForm"));
        fd.append("label", "WEBUPLOAD");
        $.ajax({
            url: "<?php echo base_url() ?>employee/vendor/process_assign_booking_form",
            type: "POST",
            data: fd,
            processData: false,  // tell jQuery not to process the data
            contentType: false   // tell jQuery not to set contentType
        }).done(function( data ) {
            alert(data);
           location.reload();

        });
  } else{
     alert("Please Assign 30 Bookings");
     return false;
  }

  return false;
   
}
</script>