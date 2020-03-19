<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main">

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Bulk Warranty Checker</h2>
                    <div class="right_holder" style="float:right;margin-right:10px;">

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="container" style="margin: 0px 10px;" class="form_container">
                        <!-- Form to upload excel file starts here -->
                        <a class="btn btn-info btn-sm" style="float:right" target="_blank" href='https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/bulk_warrenty_checker_sample_file.xlsx'>Download Sample File</a>
                        <div class="col-md-6">
                            <form class="form-horizontal" id="fileinfo" name="fileinfo" method="POST" enctype="multipart/form-data"  action="<?php echo base_url(); ?>partner/inventory/bulk_warranty_checker">
                                <input type="hidden" name="redirect_url" id="redirect_url" value="check_warranty">                          
                                <div style="padding-top: 10px;">
                                    <label for="excel" class="col-md-4">Upload File</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control" name="file" required="" accept=".xlsx, .xls" required>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12" style='margin-top:10px'>                                 
                                        <input type="submit" class="btn btn-sm btn-primary" id="submit_btn" value="Upload">

                                    </div>
                                </div>

                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (!empty($warrentyStatus)) {
        ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Uploaded Booking ID Status</h2>
                        <div class="right_holder" style="float:right;margin-right:10px;">
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div id="container" style="margin: 0px 10px;" class="form_container">
                            <table class="table table-bordered table-hover table-striped data" id="bank_transaction_table" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>BookingID</th>
                                        <th>Status</th>                                
                                    </tr>
                                </thead>
                                <tbody> 
    <?php
    $count = 0;
    foreach ($warrentyStatus['warrenty_status'] as $key => $value) {
        ?>
                                        <tr>
                                            <td><?php echo ++$count; ?></td>
                                            <td><?php echo $key; ?></td>
                                            <td><?php echo $value; ?></td>
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
    <?php
}
?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>File Upload History</h2>
                    <div class="right_holder" style="float:right;margin-right:10px;">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="container" style="margin: 0px 10px;" class="form_container">


                        <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Download</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .select2-container{
        width: 192px !important;
    }
    #add_state .select2-container{
        width: 307px !important;
    }
</style>
<script>
    $(document).ready(function () {
        $('#bank_transaction_table').DataTable({
            "processing": true,
            "serverSide": false,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    title: 'bulk_warranty_checker_<?php echo date('Ymd-His'); ?>',
                    footer: true,
                    exportOptions: {
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
            "pageLength": 25,
            "language": {
                "emptyTable": "No Data Found",
                "searchPlaceholder": "Search by any column."
            },
        });
        show_upload_file_history();
    });
    function show_upload_file_history() {
        table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            pageLength: 10,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function (d) {
                    d.file_type = '<?php echo BULK_CHECK_WARRANTY_STATUS; ?>', d.partner_id = '<?php echo $partner_id ?>';
                }
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3, 4],
                    "orderable": false
                }
            ]
        });
    }
</script>
<?php
if (!empty($errormessage)) {
    ?>
    <script>
        $(document).ready(function () {
            alert("<?php echo $errormessage; ?>");
        });
    </script>
    <?php
}
?>