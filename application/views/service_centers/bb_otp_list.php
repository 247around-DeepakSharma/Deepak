<div id="page-wrapper" >
    <div class="container1" >
        <div class="panel panel-info" >
            <div class="panel-heading" style='height:auto;overflow:hidden'>
                <div class='col-md-6'><b>BB OTP LIST</b></div>
            </div>  
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="bb_otp_list_table" class="table  table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Sn.</th>
                                    <th class="text-center">Agent Name</th>
                                    <th class="text-center">Agent Phone</th>
                                    <th class="text-center">City</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">OTP</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $StartRowCount = 0;
                                $totalAmount = 0;
                                $TotalCashInoviceInst = 0;
                                if (!empty($otp_detail)) {
                                    foreach ($otp_detail as $row) {
                                        ?>
                                            <tr>
                                                <td class="text-center"><?php echo ++$StartRowCount; ?></td>
                                                <td class="text-center"><?php echo $row['agent_name']; ?></td>
                                                <td class="text-center"><?php echo $row['agent_phone']; ?></td>
                                                <td class="text-center"><?php echo $row['city']; ?></td>
                                                <td class="text-center"><?php echo $row['date']; ?></td>
                                                <td class="text-center"><?php echo $row['time']; ?></td>
                                                <td class="text-center"><?php echo $row['otp']; ?></td>                                        
                                            </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#bb_otp_list_table').DataTable({
            "processing": true,
            "serverSide": false,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    title: 'bb_otp_list_<?php echo date('Ymd-His'); ?>',
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
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
            "pageLength": 50,
            "language": {
                "emptyTable": "No Data Found",
                "searchPlaceholder": "Search by any column."
            },
        });
    });
</script>
<style>
    #bb_otp_list_table label
    {
        float: right !important;
    }
    #bb_otp_list_table .input-sm
    {
        width: 272px !important;
    }
    .dataTables_length label
    {
        float:left;
    }
    .dt-buttons
    {
        float:left;
        margin-left:85px;
    }
    .paging_simple_numbers
    {
        width: 45%;
        float: right;
        text-align: right;
    }
    .dataTables_info
    {
        width: 45%;
        float: left;
        padding-top: 30px;
    }
</style>