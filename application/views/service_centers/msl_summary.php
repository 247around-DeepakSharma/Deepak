<div class="container" style="width:96%">
    <?php if(isset($msl_security)){ ?>
        <div class="row">
            <h1>MSL Security Details</h1>
            <div class="table-responsive">
                <table class="table table-stripped table-bordered" id="table_msl_security">
                    <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Category</th>
                            <th>Sub - Category</th>
<!--                            <th>Parts Count</th>-->
                            <th>Invoice ID</th>
                            <th>Invoice Amount</th>
                            <th>Balance Amount</th>
                            <th>Invoice Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($msl_security)>0) {?>
                            <?php foreach($msl_security as $key=>$item){ ?>
                                <tr>
                                    <td><?php echo ++$key; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td><?php echo $item['sub_category']; ?></td>
<!--                                    <td><?php //echo $item['parts_count']; ?></td>-->
                                    <td><a href="<?php echo S3_WEBSITE_URL;?>invoices-excel/<?php echo $item['invoice_file_main'];?>"><?php echo $item['invoice_id']; ?></a></td>
                                    <td><?php echo $item['total_amount_collected']; ?></td>
                                    <td><?php echo $item['amount']; ?></td>
                                    <td><?php echo $item['invoice_date']; ?></td>
                                </tr>
                            <?php } ?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan=7><div class="text-center">No Data.</div></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>this</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php } ?>
    <?php if(isset($msl_spare)){ ?>
        <div class="row">
            <h1>MSL Spare Details</h1>
            <div class="table-responsive">
                <table class="table table-stripped table-bordered" id="table_msl_spare">
                    <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Category</th>
                            <th>Sub - Category</th>
                            <th>Parts Count</th>
                            <th>Invoice ID</th>
                            <th>Invoice Amount</th>
                            <th>Balanced Amount</th>
                            <th>Invoice Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>this</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="row">
        <h1>OOW Consumed Parts</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="table_oow_parts">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>Booking ID</th>
                        <th>Parts Requested Type</th>
                        <th>Parts Requested</th>
                        <th>Model Number</th>
                        <th>Date of Request</th>
                        <th>Sell Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>0</th>
                        <th>0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        </div>
    <?php } ?>
</div>
<script>
    $(document).ready(function(){
        $("#table_msl_security").dataTable({
            "drawCallback":function(){
                $('[data-toggle="tooltip"]').tooltip();
                var api = this.api();
                $( api.column( 5 ).footer() ).html(
                    api.column( 5 ).data().reduce( function ( a, b ) {
                        return (parseFloat(a) + parseFloat(b)).toFixed(2);
                    }, 0 )
                );
            }
        });
        $("#table_msl_spare").dataTable({
            ordering: false,
            serverSide:true,
            ajax:{
                type:"POST",
                url:'<?php echo base_url(); ?>employee/service_centers/ajax_get_msl_spare_details'
            },
            "drawCallback":function(){
                $('[data-toggle="tooltip"]').tooltip();
                var api = this.api();
                $( api.column( 6 ).footer() ).html(
                    api.column( 6 ).data().reduce( function ( a, b ) {
                        return (parseFloat(a) + parseFloat(b)).toFixed(2);
                    }, 0 )
                );
            }
        });
        $('#table_oow_parts').dataTable({
            ordering: false,
            serverSide: true,
            ajax: {
                type:"POST",
                url:'<?php echo base_url(); ?>employee/service_centers/ajax_get_msl_parts_consumed_in_oow'
            },
            "drawCallback":function(){
                $('[data-toggle="tooltip"]').tooltip();
                var api = this.api();
                $( api.column( 6 ).footer() ).html(
                    api.column( 6 ).data().reduce( function ( a, b ) {
                        return (parseFloat(a) + parseFloat(b)).toFixed(2);
                    }, 0 )
                );
                $( api.column( 7 ).footer() ).html(
                    api.column( 7 ).data().reduce( function ( a, b ) {
                        return (parseInt(a) + parseInt(b));
                    }, 0 )
                );
            }
        });
    });
</script>
