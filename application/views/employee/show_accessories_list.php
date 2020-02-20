<div id="page-wrapper" >
    <div class="container1" >
        <div class="panel panel-info" >
            <div class="panel-heading" style='height:auto;overflow:hidden'>
                <div class='col-md-6'>ACCESSORIES LIST</div>
                <div class='col-md-6' style='text-align:right'>
                    <a href='<?php echo base_url(); ?>employee/accessories/add_accessories' class='btn btn-primary btn-sm'>Add Accessories</a></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="annual_charges_report" class="table  table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Sn.</th>
                                    <th class="text-center">Appliance</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Basic Charge</th>
                                    <th class="text-center">HSN Code</th>
                                    <th class="text-center">Tax Rate</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete / Restore</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $StartRowCount = 0;
                                $totalAmount = 0;
                                $TotalCashInoviceInst = 0;


                                foreach ($product_list as $row) {  //print_r($row);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo ++$StartRowCount; ?></td>
                                        <td class="text-center"><?php echo $row['services']; ?></td>
                                        <td class="text-center"><?php echo $row['product_name']; ?></td>
                                        <td class="text-center"><?php echo $row['description']; ?></td>
                                        <td class="text-center"><?php echo $row['basic_charge']; ?></td>
                                        <td class="text-center"><?php echo $row['hsn_code']; ?></td>
                                        <td class="text-center"><?php echo $row['tax_rate']; ?></td>
                                        <td class="text-center"><a type="button" class="btn btn-info" href="<?php echo base_url() . "employee/accessories/edit_accessories/" . $row['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a></td>

                                        <td class="text-center">
                                            <?php
                                            $deactivateshow = 'none';
                                            $activateshow = 'none';
                                            if ($row['status'] == 1) {
                                                $deactivateshow = 'initial';
                                            } else {
                                                $activateshow = 'initial';
                                            }
                                            ?>
                                            <a type="button" class="btn btn-sm btn-danger open-adminremarks" onclick="update_accessories_status(<?php echo $row['id']; ?>, 0)" id='btn_<?php echo $row['id']; ?>' title='delete' style='display:<?php echo $deactivateshow; ?>'><i class="fa fa-trash"></i></a>

                                            <a type="button" class="btn btn-sm btn-success open-adminremarks" onclick="update_accessories_status(<?php echo $row['id']; ?>, 1)" id='btn_s_<?php echo $row['id']; ?>' title='Restore' style='display:<?php echo $activateshow; ?>'><i class="fa fa-undo"></i></a>
                                        </td>
                                    </tr>
                                    <?php
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
        $('#annual_charges_report').DataTable({
            "processing": true,
            "serverSide": false,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    title: 'accessories_list_<?php echo date('Ymd-His'); ?>',
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
            //"searching": false,
            //"paging":false
            "pageLength": 50,
            "language": {
                "emptyTable": "No Data Found",
                "searchPlaceholder": "Search by any column."
            },
        });
    });

    function update_accessories_status(idtodelete, status)
    {

        datastring = "idtodelete=" + idtodelete + "&status=" + status;
        $.ajax({
            method: 'post',
            data: datastring,
            url: "<?php echo base_url() ?>employee/accessories/update_accessories_status",
            beforeSend()
            {
                $("#btn_" + idtodelete).html("<i class='fa fa-spinner fa-spin' style='font-size: 17px;'></i>");
                $("#btn_" + idtodelete).css('pointer-events', 'none');
                $("#btn_" + idtodelete).css('opacity', '.5');

                $("#btn_s_" + idtodelete).html("<i class='fa fa-spinner fa-spin' style='font-size: 17px;'></i>");
                $("#btn_s_" + idtodelete).css('pointer-events', 'none');
                $("#btn_s_" + idtodelete).css('opacity', '.5');
            },
            success: function (data)
            {
                $("#btn_" + idtodelete).html("<i class='fa fa-trash' ></i>");
                $("#btn_" + idtodelete).css('pointer-events', '');
                $("#btn_" + idtodelete).css('opacity', '');

                $("#btn_s_" + idtodelete).html("<i class='fa fa-undo' ></i>");
                $("#btn_s_" + idtodelete).css('pointer-events', '');
                $("#btn_s_" + idtodelete).css('opacity', '');

                $("#btn_" + idtodelete).hide();
                $("#btn_s_" + idtodelete).hide();
                if (status == 1)
                {
                    $("#btn_" + idtodelete).show();
                } else
                {
                    $("#btn_s_" + idtodelete).show();
                }

            }
        });
    }
</script>
<style>
    #annual_charges_report_filter label
    {
        float: right !important;
    }
    #annual_charges_report_filter .input-sm
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