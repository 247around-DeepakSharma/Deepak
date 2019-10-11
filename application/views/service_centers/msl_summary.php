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
                            <th>Parts Count</th>
                            <th>Invoice ID</th>
                            <th>Amount</th>
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
                                    <td><?php echo $item['parts_count']; ?></td>
                                    <td><?php echo $item['invoice_id']; ?></td>
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
                            <th>Amount</th>
                            <th>Invoice Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($msl_spare)>0) {?>
                            <?php foreach($msl_spare as $key=>$item){ ?>
                                <tr>
                                    <td><?php echo ++$key; ?></td>
                                    <td><?php echo $item['category']; ?></td>
                                    <td><?php echo $item['sub_category']; ?></td>
                                    <td><?php echo $item['parts_count']; ?></td>
                                    <td><a title="click to get more details" data-toggle="tooltip"><?php echo $item['invoice_id']; ?></a></td>
                                    <td><?php
                                            if($item['sub_category'] == MSL){
                                                if($item['amount'] == 0){
                                                    echo $item['amount'];
                                                }else{
                                                    echo -1 * $item['amount'];
                                                }
                                            }else{
                                                echo $item['amount'];
                                            }
                                        ?></td>
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
    });
</script>
