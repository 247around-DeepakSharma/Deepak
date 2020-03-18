<table id="annual_charges_report" class="table  table-striped table-bordered">
    <thead>
        <tr>
            <th class="text-center">Sn.</th>
            <th class="text-center">Service Center Name</th>
            <th class="text-center">Payment hold reason</th>
            <th class="text-center">Create date</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $StartRowCount = 0;
        $totalAmount = 0;
        $TotalCashInoviceInst = 0;

        foreach ($payment_hold_reason_list as $row) {  //print_r($row);
            if($row['status']==0)
            {
                $class_tr   =   'strikeout1';
                $dispay_bt  =   'none';
                $status     =   "<span id='span_".$row['id']."' style='color:#c9302c'>Inactive</span>";
            }
            else
            {
                 $class_tr='';
                 $dispay_bt  =   '';
                 $status     =   "<span id='span_".$row['id']."' style='color:#4cae4c'>Active</span>";
            }
            ?>
            <tr id='rowid<?php echo $row['id']; ?>' class='<?php echo $class_tr; ?>'>
                <td class="text-center tdfirstrow" ><?php echo ++$StartRowCount; ?></td>
                <td class="text-center"><?php echo $row['name']; ?></td>
                <td class="text-center"><?php echo $row['payment_hold_reason']; ?></td>
                <td class="text-center"><?php echo $this->miscelleneous->get_formatted_date($row['create_date']); ?></td>
                <td class="text-center"><?php echo $status; ?></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm open-adminremarks" onclick="delete_payment_hold_reason(<?php echo $row['id']; ?>)" id='botton<?php echo $row['id']; ?>' style='display:<?php echo $dispay_bt; ?>'><i class="fa fa-trash" style="font-size: 17px;"></i></button></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        $('#annual_charges_report').DataTable({
            "processing": true,
            "serverSide": false,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    title: 'sf_payment_hold_list_<?php echo date('Ymd-His'); ?>',
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3,4],
                        modifier: {
                            // DataTables core
                            order: 'index', // 'current', 'applied', 'index',  'original'
                            page: 'all', // 'all',     'current'
                            search: 'none'     // 'none',    'applied', 'removed'
                        }
                    }
                }
            ],
            "order": [],
            "ordering": true,
            "deferRender": true,
            //"searching": false,
            //"paging":false
            "pageLength": 50,
            "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
            "language": {
                "emptyTable": "No Data Found",
                "searchPlaceholder": "Search by any column."
            },
        });
    });
</script>
<style>
table {
    border-collapse: collapse;
}

td {
    position: relative;
    padding: 5px 10px;
}

tr.strikeout td:before {
    content: " ";
    position: absolute;
    top: 50%;
    left: 0;
    border-bottom: 1px solid #111;
    width: 100%;
    opacity:.1;
}
</style>