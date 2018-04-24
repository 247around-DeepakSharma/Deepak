<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Inventory Ledger</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="stocks_table">
                        <table class="table table-responsive table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Receiver Name</th>
                                    <th>Receiver Type</th>
                                    <th>Sender Name</th>
                                    <th>Sender Type</th>
                                    <th>Part Name</th>
                                    <th>Part Description</th>
                                    <th>Quantity</th>
                                    <th>Booking Id</th>
                                    <th>Agent Name</th>
                                    <th>Agent Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($brackets as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $value['receiver']; ?></td>
                                        <td><?php echo $value['receiver_entity_type']; ?></td>
                                        <td><?php echo $value['sender']; ?></td>
                                        <td><?php echo $value['sender_entity_type']; ?></td>
                                        <td><?php echo $value['part_name']; ?></td>
                                        <td><?php echo $value['description']; ?></td>
                                        <td><?php echo $value['quantity']; ?></td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>partner/booking_details/<?php echo $value['booking_id']; ?>">
                                                <?php echo $value['booking_id']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $value['agent_name']; ?></td>
                                        <td><?php echo $value['agent_type']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if (!empty($links)) { ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if (isset($links)) {
                            echo $links;
                        } ?></div> <?php } ?>
                    </div>
                </div>
            </div>     
        </div>
    </div>
</div>