<style>
    .dataTables_filter{
        float: right;
    }
    .pagination{
        float: right;
    }
</style>
<div id="page-wrapper" >
    <div>
        <h3>Invoice Details </h3>
        <hr>
        <div class="stocks_table">
            <table class="table table-responsive table-hover table-bordered table-striped" id="inventory_ledger">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Invoice Id</th>
                        <th>Invoice Date</th>
                        <th>Invoice Type</th>
                        <th>Part Number</th>
                        <th>Description</th>
                        <th>HSN Code</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Taxable Value</th>
                        <th>GST Rate</th>
                        <th>GST Tax Amount</th>
                        <th>Total Amount</th>
                        <th>Type</th>
                        <th>From GST Number</th>
                        <th>To GST Number</th>
                        <th>Sub Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($invoice_details)){ foreach ($invoice_details as $key => $value){ ?>
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo $value['invoice_id'];?></td>
                        <td><?php echo date("d-M-Y", strtotime($value['create_date'])); ?></td>                        
                        <td><?php echo $value['type_code'];?></td>
                        <td><?php echo $value['part_number'];?></td>
                        <td><?php echo $value['description'];?></td>
                        <td><?php echo $value['hsn_code']; ?></td>
                        <td><?php echo $value['qty']; ?></td>
                        <td><?php echo $value['rate']; ?></td>
                        <td><?php echo $value['taxable_value']; ?></td>
                        <td><?php echo $value['gst_rate']; ?></td>
                        <td><?php echo $value['gst_tax_amount']; ?></td>
                        <td><?php echo $value['total_amount']; ?></td>
                        <td><?php echo $value['type']; ?></td>
                        <td><?php echo $value['from_gst']; ?></td>
                        <td><?php echo $value['to_gst']; ?></td>
                        <td><?php echo $value['sub_category']; ?></td>
                    </tr>
                    <?php } }?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        var time = moment().format('D-MMM-YYYY');
        $(document).ready(function() {
            $('#inventory_ledger').DataTable( {
                "processing": true,
                "serverSide": false,
                "pageLength": 50,
                dom: 'lBfrtip',
                "buttons": [
                {
                extend: 'excel',
                text: 'Export',
                exportOptions: {
                  columns: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16]
                },
                title: 'inventory_ledger_details_'+time,
                },
                ],
            } );
        } );
           
    </script>
    
    
</div>