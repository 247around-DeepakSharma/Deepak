<table class="table table-striped table-bordered table-hover" style="font-size:13px">
    <thead>
        <tr>
            <th >Booking Cancelled In <?php echo date("M", strtotime($cancel_booking[2][0]['month'])); ?></th>
            <td class="text-center"><?php echo $cancel_booking[2][0]['cancel_booking']; ?></td>
            <th >Amount Lost in <?php echo date("M", strtotime($cancel_booking[2][0]['month'])); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $cancel_booking[2][0]['lose_amount']; ?></td>
            <th >Booking Completed In <?php echo date("M", strtotime($eraned_details[2][0]['month'])); ?></th>
            <td class="text-center"><?php echo $eraned_details[2][0]['total_booking']; ?></td>
            <th >Amount Earned In <?php echo date("M", strtotime($eraned_details[2][0]['month'])); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf("%.2f", $eraned_details[2][0]['earned']); ?></td>
            <?php if($this->session->userdata('is_upcountry') == 1){ ?>
            <th >Upcountry In <?php echo date("M", strtotime($eraned_details[2][0]['month'])); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php if (!empty($upcountry[2])) {
                echo sprintf("%.2f", $upcountry[2][0]['total_upcountry_price']);
                } else {
                echo "0.00";
                } ?></td>
            <?php } ?>
        </tr>
        <tr>
            <th >Booking Cancelled In <?php echo date("M", strtotime($cancel_booking[1][0]['month'])); ?></th>
            <td class="text-center"><?php echo $cancel_booking[1][0]['cancel_booking']; ?></td>
            <th >Amount Lost in <?php echo date("M", strtotime("last month")); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $cancel_booking[1][0]['lose_amount']; ?></td>
            <th >Booking Completed In <?php echo date("M", strtotime($eraned_details[1][0]['month'])); ?></th>
            <td class="text-center"><?php echo $eraned_details[1][0]['total_booking']; ?></td>
            <th >Amount Earned In <?php echo date("M", strtotime($eraned_details[1][0]['month'])); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf("%.2f", $eraned_details[1][0]['earned']); ?></td>
            <?php if($this->session->userdata('is_upcountry') == 1){ ?>
            <th >Upcountry In <?php echo date("M", strtotime($eraned_details[1][0]['month'])); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php if (!empty($upcountry[1])) {
                echo sprintf("%.2f", $upcountry[1][0]['total_upcountry_price']);
                } else {
                echo "0.00";
                } ?></td>
            <?php } ?>
        </tr>
        <tr>
            <th >Booking Cancelled In <?php echo date("M", strtotime($cancel_booking[0][0]['month'])); ?></th>
            <td class="text-center"><?php echo $cancel_booking[0][0]['cancel_booking']; ?></td>
            <th >Amount Lost in <?php echo date('M'); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $cancel_booking[0][0]['lose_amount']; ?></td>
            <th >Booking Completed In <?php echo date("M"); ?></th>
            <td class="text-center"><?php echo $eraned_details[0][0]['total_booking']; ?></td>
            <th>Amount Earned In <?php echo date("M"); ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo sprintf("%.2f", $eraned_details[0][0]['earned']); ?></td>
            <?php if($this->session->userdata('is_upcountry') == 1){ ?>
            <th >Upcountry Earned In <?php echo date("M") ?></th>
            <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php if (!empty($upcountry[0])) {
                echo sprintf("%.2f", $upcountry[0][0]['total_upcountry_price']);
                        
                } else {
                echo "0.00";
                        } ?></td>
            <?php } ?>
        </tr>
    </thead>
</table>