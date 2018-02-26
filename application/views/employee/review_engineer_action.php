<div class="container-fluid">

    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('success')) {
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

<?php //print_r($data); ?>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Booking ID</th>
                                <th class="text-center">SF Name</th>
                                <th class="text-center">Engineer Name</th>
                                <th class="text-center">Amount Due</th>
                                <th class="text-center" >Amount Paid  </th>
                                <th class="text-center" >Broken  </th>
                                <th class="text-center" >Remarks  </th>
                                <th class="text-center" >Status</th>
                                <th class="text-center" >Booking Pincode</th>
                                <th class="text-center" >Engineer Address</th>

                            </tr>
                        </thead>

                        <tbody>
                        <tbody>
<?php foreach ($data as $key => $row) { ?>
                                <tr>
                                    <td class="text-center">
    <?php echo $key + 1; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $row->booking_id; ?>"> <?php echo $row->booking_id; ?> </a>
                                    </td>
                                    <td class="text-center"><?php echo $row->sf_name;?></td>
                                    <td class="text-center">
                                    <?php if (!empty($row->engineer_name)) {
                                        echo $row->engineer_name[0]['name'];
                                    } ?>
                                    </td>

                                    <td class="text-center">
                                        <i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row->amount_due; ?>
                                    </td>
                                    <td class="text-center"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $row->amount_paid ?></td>
                                    <td class="text-center"><?php if($row->is_broken ==1){ echo "Yes"; } else { echo "No";} ?></td>
                                    <td class="text-center"><?php echo $row->remarks ?></td>
                                    <td class="text-center"><?php echo $row->status ?></td>
                                    <td class="text-center"><?php echo $row->booking_address; ?></td>
                                    <td class="text-center" <?php if($row->booking_pincode != $row->en_pincode){?> style="color: red;" <?php } ?>><?php echo $row->en_address; ?></td>
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


