<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
                <?php
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
                }
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
                ?>
                <?php if (isset($type)) { ?> <h2><b>Re-Assign Partner</b></h2>
                    <form method="POST" action="<?php echo base_url(); ?>employee/vendor/process_reassign_partner_form" class="form-inline">
                        <div class="panel-body">
                            <div class="clonedInput" id="clonedInput">
                                <table class="table  table-striped table-bordered">
                                    <tr>
                                        <th style="width: 40%;">
                                            <div class="form-group">
                                                <label for="booking-id">Booking ID:</label>
                                                <input type="text" class="form-control get_required" id="booking-id_1" name="booking_id[]" oninput = "booking_validate(this.id)">
                                            </div>
                                        </th>
                                        <th>
                                            <select class="form-control selectcntrl" id="partner_1" name="partner[]" required>
                                                <option selected disabled>Select Partner</option>
                                                    <?php foreach ($partners as $key => $values) { ?>
                                                    <option  value="<?php echo $values['id']; ?>">
                                                        <?php
                                                        echo $values['public_name'];
                                                    }
                                                    ?>
                                                </option>
                                            <?php echo form_error('partner'); ?>
                                            </select>
                                        </th>

                                        <th class="text-center">
                                            <button class="clone btn btn-sm btn-success" id="add_1">Add New Row</button>
                                        </th>
                                        <th class="text-center">
                                            <button class="remove btn btn-sm btn-danger" id="delete_1">Delete Row</button>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                            <div class="cloned"></div>
                            <div class="col-md-12">
                                <center><img id="loader_gif" src="" style="display: none;width:40px;"></center>
                                <center><input type="submit" value="Reassign Partner" onclick="return check_validation()" class="btn btn-md btn-primary"  id ="submit_btn"/></center>
                            </div>
                        </div>
                    </form>
                    <script>
                        var regex = /^(.+?)(\d+)$/i;
                        var cloneIndex = $(".clonedInput").length + 1;

                        function clone() {
                            $(this).parents(".clonedInput").clone()
                                    .appendTo(".cloned")
                                    .attr("id", "cat" + cloneIndex)
                                    .find("*")
                                    .each(function () {
                                        var id = this.id || "";
                                        var match = id.match(regex) || [];
                                        //console.log(match.length);
                                        if (match.length === 3) {
                                            this.id = match[1] + (cloneIndex);
                                        }
                                        $('#booking-id_' + cloneIndex).val('');
                                    })
                                    .on('click', 'button.clone', clone)
                                    .on('click', 'button.remove', remove);


                            cloneIndex++;
                            return false;
                        }
                        function remove() {
                            var length = $(".clonedInput").length;

                            if (length === 1) {
                                alert("Atleast one row being added");
                                return false;
                            } else {
                                $(this).parents(".clonedInput").remove();
                            }


                            return false;
                        }
                        $("button.clone").on("click", clone);

                        $("button.remove").on("click", remove);
                        
                        function check_validation(){
                            var validation = 1;
                            $('.get_required').each(function (i) {
                                var input_field = $("#" + this.id).val();

                                switch(input_field){
                                    case null:
                                        validation = 0;
                                        alert("Please Enter " + this.id.split('_')[0]);
                                        break;
                                    case typeof this === "undefined":
                                        validation = 0;
                                        alert("Please Enter " + this.id.split('_')[0]);
                                        break;
                                    case "":
                                        validation = 0;
                                        alert("Please Enter " + this.id.split('_')[0]);
                                        break;
                                    case false:
                                        validation = 0;
                                        alert("Please Enter " + this.id.split('_')[0]);
                                }
                            });
                            if(validation ===0){
                                return false;

                            } else if(validation === 1){
                                return true;
                            }
                        }
                       function booking_validate(id){
                           var booking_id = document.getElementById(id).value;
                           var saveData = $.ajax({
                           type: 'POST',
                           url: "<?php echo base_url() ?>employee/vendor/booking_spare_assign_or_not",
                           data:{booking_id:booking_id},
                           dataType: "text",
                           success: function(resultData) {
                               if(resultData == "Success"){
                               alert("Do not reassign the partner of this booking id "+booking_id+" because sapre is involved");
                               $("#submit_btn").attr('disabled',true);
                             }
                             else{
                                $("#submit_btn").attr('disabled',false);
                            }
                        }
                        });
                          saveData.error(function() { alert("Something went wrong"); });
                       }
                    </script>
                <?php } else { ?>
                    <h2><b>Re-Assign Vendor</b></h2>
                    <hr>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>employee/vendor/process_reassign_vendor_form" >
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>Serial No.</th>
                                <th>Booking Id</th>
                                <th>Service Center</th>
                                <th>Select Reason</th>
                                <th>RM Responsible</th>
                                <th>Remarks</th>
                            </tr>
                            <?php $count = 1; ?>		
                            <tr>
                                <td><?php
                                    echo $count;
                                    $count++;
                                    ?>.
                                </td>
                                <td>
                                    <input type="text" class="form-control" value= "<?php echo $booking_id; ?>" name="booking_id" placeholder="Please Enter Booking Id Here.." required="" readonly>
                                </td>
                                <td>
                                    <select class="form-control selectcntrl" id="service_center" name="service" required="">
                                        <option selected disabled>Select Service Center</option>
                                            <?php foreach ($service_centers as $key => $values) { ?>
                                            <option  value="<?php echo $values['id']; ?>">
                                                <?php
                                                echo $values['name'];
                                            }
                                            ?>
                                        </option>
                                    <?php echo form_error('service_center'); ?>
                                    </select>
                                </td>

                                <td>
                                    <select class="form-control" id="reason" name="reason" required="">
                                        <option value="" disabled selected  >Select Reason</option>
                                            <?php foreach ($reassign_reasons as $key => $values) { ?>
                                            <option  value="<?php echo $values['id']; ?>">
                                                <?php
                                                echo $values['reason'];
                                            }
                                            ?>
                                        </option>
                                    </select>
                                </td>

                                 <td>

                                 <?php if(!empty($spare)){ ?>
                                   <input type="checkbox" name="rm_responsible" value="1"   /> RM/ASM Will take care of spare
                                 <?php }  ?>
                                    
                                </td>

                                <td>
                                    <textarea class="form-control" name="remarks" id="remarks" required=""></textarea>
                                </td>
                            </tr>

                        </table>
                        <center>
                            <div>
                                <?php
                                if(!empty($arr_validation_checks)) { ?>
                                    <center><h3 class='text-danger'><?php echo reset($arr_validation_checks);?></h3></center>
                                    
                                <?php } else if(empty($spare)){ ?>
                                    
                                    <input type="Submit" value="Save" class="btn btn-primary btn-lg">  
                                    <input type="Reset" value="Cancel" class="btn btn-danger btn-lg btn-reset">
                                    
                               <?php } else if($this->session->userdata('user_group') == _247AROUND_RM || ($this->session->userdata('user_group') == _247AROUND_ASM)){ ?>
                                    <input type="Submit" value="Save" class="btn btn-primary btn-lg">  
                                    <input type="Reset" value="Cancel" class="btn btn-danger btn-lg btn-reset">

                                <?php } else{ ?>
                                 <center><h3 class='text-danger'>Spare involved in this booking. Only RM/ASM are allowed to perform this action</h3></center>
                                <?php }
                                ?>                                  
                                
                                <?php //} ?>
                            </div>
                        </center>
                    </form> <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
<?php if (isset($type)) { ?> $('#partner').select2();<?php } else { ?>$('#service_center').select2();
<?php } ?>
// Reset selected text in service_centers Select2
$(".btn-reset").click(function(){
    $(".selectcntrl").val('').trigger("change");
});
</script>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>