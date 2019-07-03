<style>
    .col-md-2 {
        width: 16.666667%;
    }
    .tile_count .tile_stats_count, ul.quick-list li {
        white-space: normal;
    }
    .select2-selection--multiple{
        min-height: 38px !important;
        border: 1px solid #aaa !important;
    }
</style>

<div class="right_col" role="main">
    <div class="row">

        <div class="clearfix"></div>


        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Not Assigned Booking Report</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="tab-content" style="margin-top: 10px;">
                        <div class="tab-pane fade in active" id="tab1">
<!--                            <form action="<?php echo base_url() ?>employee/dashboard/download_tat_report" method="post">
                                <input type="hidden" value='<?php echo json_encode($state); ?>' name="data">
                                <input type="submit" value="Download CSV" class="btn btn-primary" style="background: #405467;border: none;">
                            </form>-->
                            
                            <?php foreach ($full_view_data as $rm_name => $rm_data) { ?>
                            
                            <table class="table table-striped table-bordered jambo_table bulk_action" id="tat_state_table">
                                <caption class="text-info" style="font-size: 16px;font-weight: bold;"><?= $rm_name; ?></caption>
                                <thead>
                                    <tr style="background: #405467;color: #fff;margin-top: 5px;">
                                        <th>States</th>
                                        <th>Number of Bookings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rm_data as $record) { ?>
                                        <tr>
                                            <td width="50%"><?= $record['state']; ?></td>
                                            <td width="50%"><?= $record['number_of_bookings']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            
                            <?php } ?>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="clearfix"></div>

    </div>

    <!-- END -->
</div>
<script>
    $(document).ready(function () {
        var state_table = $('#tat_state_table').DataTable({
            "pageLength": 1000,
            dom: 'Bfrtip',
            buttons: ['csv'],
            "ordering": false
        });
        // state_table.Columns[""].DataType = typeof(int);
        var sf_table = $('#tat_sf_table').DataTable({
            "pageLength": 1000,
            dom: 'Bfrtip',
            buttons: ['csv'],
            "ordering": false
        });
    });
    $('#request_type').select2({
        allowClear: true
    });
    $('#service_id').select2({
        allowClear: true
    });
    $('#partner_id').select2({
        allowClear: true
    });
    $('#free_paid').select2({
        allowClear: true
    });
    $('#upcountry').select2({
        allowClear: true
    });
    $('#status').select2({
        allowClear: true
    });
    $(function () {
        $('input[name="daterange_completed_bookings"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>
<style>
    .dataTables_length{
        display: none;
    }
    .dt-buttons, .dataTables_filter{
        display: none;
    }
    .dataTables_filter{
        margin-top: -38px;
    }
    .select2-selection--multiple{
        width: 170px !important; 
    }
</style>