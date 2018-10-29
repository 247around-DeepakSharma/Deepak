
<div class="row">
    <div class="container col-md-6">

        <span id="success_err" style="color: green; display: none;">Assign successful. </span>
        <table id="customers" class="table table-striped table-bordered table-hover ">
            <tr>
                <th colspan="4" style="text-align: center;"><h4>Spare Parts Search Lists</h4></th>                                        
            </tr>
            <tr>
                <th>Particular</th>
                <th>Parts Ware</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($spare_parts_list as $key => $val) { ?>
                <tr>
                    <td>
                        <?php
                        if ($val['entity_type'] == _247AROUND_PARTNER_STRING) {
                            echo $val['entity_type'];
                        } else {
                            echo WAREHOUSE;
                        }
                        ?>
                    </td>
                    <td><?php echo $val['parts_requested']; ?></td>
                    <td><?php echo $val['status']; ?></td>
                    <td>
                        <?php if ($val['entity_type']==_247AROUND_SF_STRING && $val['status'] == SPARE_PARTS_REQUESTED) { ?> 
                        
                            <form id="move_to_update_spare_parts_<?php echo $val['id']; ?>">
                                <input type="hidden" name="spare_parts_id" id="spare_parts_id" value="<?php echo $val['id']; ?>">
                                <input type="hidden" name="booking_partner_id" id="booking_partner_id" value="<?php echo $val['booking_partner_id']; ?>">
                                <input type="hidden" name="entity_type" id="entity_type" value="<?php echo _247AROUND_PARTNER_STRING; ?>">
                                <input type="hidden" name="booking_id" id="booking_id" value="<?php echo $val['booking_id']; ?>">                       
                            </form>
                            <a class="move_to_update btn btn-md btn-primary" id="<?php echo $val['id']; ?>" href="javascript:void(0);">Assign</a>

                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>   

<script type="text/javascript">

    $(document).ready(function () {
        $(".move_to_update").on('click', function () {
            var squence_id = $(this).attr('id');
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>employee/spare_parts/move_to_update_spare_parts_details",
                data: $("#move_to_update_spare_parts_" + squence_id).serialize(),
                success: function (data) {
                    if (data != '') {
                        $("#success_err").css({"display": "block"});
                        $("#" + squence_id).hide();
                    }
                }

            });

        });

    });

</script>


