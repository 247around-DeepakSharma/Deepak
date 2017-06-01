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
                                                <input type="text" class="form-control get_required" id="booking-id_1" name="booking_id[]">
                                            </div>
                                        </th>
                                        <th>
                                            <select class="form-control" id="partner_1" name="partner[]" required>
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
                                <center><input type="submit" value="Reassign Partner" onclick="return check_validation()" class="btn btn-md btn-primary" /></center>
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
                    </script>
                <?php } else { ?><h2><b>Re-Assign Vendor</b></h2>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>employee/vendor/process_reassign_vendor_form" >
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th style="width:5%">Serial No.</th>
                                <th style="width:45%">Booking Id</th>
                                <th style="width:50%">Service Center</th>
                            </tr>
                            <?php $count = 1; ?>		
                            <tr>
                                <td><?php
                                    echo $count;
                                    $count++;
                                    ?>.</td>
                                <td><input type="text" class="form-control" value= "<?php echo $booking_id; ?>" name="booking_id" placeholder="Please Enter Booking Id Here.." required></td>
                                <td>
                                    <select type="text" class="form-control" id="service_center" name="service" value="<?php echo set_value('service_center'); ?>" required>
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
                            </tr>

                        </table>
                        <center>
                            <div><input type="Submit" value="Save" class="btn btn-primary btn-lg">
                                <input type="Reset" value="Cancel" class="btn btn-danger btn-lg"></div>
                        </center>
                    </form> <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
<?php if (isset($type)) { ?> $('#partner').select2();<?php } else { ?>$('#service_center').select2();
<?php } ?>
</script>
<?php $this->session->unset_userdata('error'); ?>
<?php $this->session->unset_userdata('success'); ?>