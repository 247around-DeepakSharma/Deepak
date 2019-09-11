<div class="container-fluid">

   <div class="row" style="margin-top: 10px;">
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
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Review Bookings Completed by Technicians</h1>
            </div>
             
             <?php //print_r($data);?>
            <div class="panel-body">
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Engineer Name</th>
                            <th class="text-center">Amount Due</th>
                            <th class="text-center" >Amount Paid  </th>
                            <th class="text-center" >Remarks  </th>
                            <th class="text-center" >Status</th>
                            <th class="text-center" >Submit</th>
                           </tr>
                       </thead>
                       
                       <tbody>
                           <tbody>
                                <?php  foreach($data as $key =>$row){?>
                               <tr <?php if($row->mismatch_pincode == 1){ echo "style='color:red;'";} ?>>
                                    <td class="text-center">
                                        <?php echo $key +1; ?>
                                    </td>
                                     <td class="text-center">
                                        <?php echo $row->booking_id; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($row->engineer_name)){ print_r($row->engineer_name[0]['name']);} ?>
                                    </td>
                                   
                                     <td class="text-center">
                                          <i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row->amount_due; ?>
                                    </td>
                                    <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row->amount_paid?></td>
                                    <td class="text-center"><?php echo $row->remarks;?></td>
                                    <td class="text-center"><?php echo $row->status; ?></td>
                                    <td class="text-center">
                                        <?php if($row->status == _247AROUND_COMPLETED) {?>
<!--                                        <button onclick="openmodel('<?php //echo $row->booking_id;?>')" class='btn btn-sm btn-success'><i class='fa fa-check' aria-hidden='true'></i></button>-->
                                        <?php
                                            $redirect_url = base_url()."service_center/complete_booking_form/".urlencode(base64_encode($row->booking_id));
                                        ?>
                                        <a href="<?php echo base_url(); ?>service_center/get_sf_edit_booking_form/<?php echo urlencode(base64_encode($row->booking_id));?>/<?php echo urlencode(base64_encode($redirect_url))?>" 
                                           class='btn btn-sm btn-success'><i class='fa fa-check' aria-hidden='true'></i>
                                        </a>
                                        <?php } else { ?>
                                        <a href="<?php echo base_url(); ?>service_center/cancel_booking_form/<?php echo urlencode(base64_encode($row->booking_id)); ?>" 
                                           class='btn btn-sm btn-success'><i class='fa fa-check' aria-hidden='true'></i></a>
                                       <?php  }?>
                                    </td>
                                    
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        </div>
                   
               </div>
           
         </div>
      </div>
   </div>

</div>
<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg container" style="margin-left:0px;width:100%">
        <!-- Modal content-->
        <div class="modal-content" id="modal-content1">
        </div>
    </div>
</div>
<script>
$('.appliance_broken').css('pointer-events','none'); 
 function openmodel(booking_id){
       
       $.ajax({
      type: 'POST',
      url: '<?php echo base_url(); ?>employee/engineer/get_approve_booking_form/'+ booking_id,
      success: function (data) {
         // console.log(data);
       $("#modal-content1").html(data);   
       $('#myModal1').modal('toggle');
    
      }
    });

}
    
//$(document).on("click", ".reject-booking", function () {
//
//});
    
$(document).ready(function() {
    //called when key is pressed in textbox
    $(".cost").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            $(".error_msg").html("Digits Only").show().fadeOut("slow");
            return false;
        }
    });

});


$(document).on('keyup', '.cost', function(e) {

    var price = 0;
    $("input.cost").each(function() {
        price += Number($(this).val());

    });

    $("#grand_total_price").val(price);
});


function onsubmit_form(upcountry_flag, number_of_div) {

    var flag = 0;
    var div_count = 0;
    var is_completed_checkbox = [];
    var serial_number_tmp = [];
   
    $(':radio:checked').each(function(i) {
        div_count = div_count + 1;

        //console.log($(this).val());
        var div_no = this.id.split('_');
        is_completed_checkbox[i] = div_no[0];
        if (div_no[0] === "completed") {
            //if POD is also 1, only then check for serial number.
            if (div_no[1] === "1") {
                var serial_number = $("#serial_number" + div_no[2]).val();
                serial_number_tmp.push(serial_number);
                if (serial_number === "") {

                    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                    flag = 1;
                }

                if (serial_number === "0") {
                    document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                    flag = 1;
                }

                var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
                if (numberRegex.test(serial_number)) {
                    if (serial_number > 0) {
                        flag = 0;
                    } else {
                        document.getElementById('serial_number' + div_no[2]).style.borderColor = "red";
                        flag = 1;
                    }
                }
            }
            var amount_due = $("#amount_due" + div_no[2]).text();
            var price_tags = $("#price_tags" + div_no[2]).text();
            var basic_charge = $("#basic_charge" + div_no[2]).val();
            var additional_charge = $("#extra_charge" + div_no[2]).val();
            var parts_cost = $("#parts_cost" + div_no[2]).val();
            if (Number(amount_due) > 0) {
                var total_sf = Number(basic_charge) + Number(additional_charge) + Number(parts_cost);
                if (Number(total_sf) === 0) {
                    alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                    flag = 1;
                }
                
                if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                    if(Number(basic_charge) < Number(amount_due)){
                       alert("Please fill amount collected from customer, Amount Due: Rs." + amount_due);
                       flag = 1;
                   }
                       
                }
            }
        } else if(div_no[0] === "cancelled"){
            var price_tags = $("#price_tags" + div_no[2]).text();
            var amount_due = $("#amount_due" + div_no[2]).text();
            if(price_tags === '<?php echo REPAIR_OOW_PARTS_PRICE_TAGS;?>'){
                alert("You can not mark as a not delivered of Spare Parts. fill amount collected from customer, Amount Due: Rs." + amount_due);
                flag = 1;
            }
        }
    });
    if (Number(number_of_div) !== Number(div_count)) {
        alert('Please Select All Services Delivered Or Not Delivered.');
        flag = 1;
        return false;
    }
    if ($.inArray('completed', is_completed_checkbox) !== -1) {

    } else {
        alert('Please Select atleast one Completed or Delivered checkbox.');
        flag = 1;
        return false;

    }
    temp = [];
    $.each(serial_number_tmp, function(key, value) {
        if ($.inArray(value, temp) === -1) {
            temp.push(value);
        } else {
            alert(value + " is a Duplicate Serial Number");
            flag = 1;
            return false;
        }
    });

    var is_sp_required = $("#spare_parts_required").val();

    if (Number(is_sp_required) === 1) {
        alert("Ship Defective Spare Parts");
    }

    if (Number(upcountry_flag) === 1) {
        var upcountry_charges = $("#upcountry_charges").val();
        if (Number(upcountry_charges) === 0) {
            flag = 1;
            document.getElementById('upcountry_charges').style.borderColor = "red";
            alert("Please Enter Upcountry Charges which Paid by Customer");
            return false;
        } else if (Number(upcountry_charges) > 0) {
            flag = 0;
            document.getElementById('upcountry_charges').style.borderColor = "green";
        }
    }
    var closing_remarks = $("#closing_remarks").val();
    if (closing_remarks === "") {
        alert("Please Enter Remarks");
        document.getElementById('closing_remarks').style.borderColor = "red";
        flag = 1;
        return false;
    }
    var amount_paid = $("#amount_paid").val();
    var total_amount = $("#grand_total_price").val();
    if(Number(amount_paid) > Number(total_amount)){
        alert("Amount Paid is not match");
        flag = 1;
        return false;
    }
    if (flag === 0) {
       
        $('#submitform').val("Please wait.....");
        return true;

    } else if (flag === 1) {

        return false;
    }
}
</script>