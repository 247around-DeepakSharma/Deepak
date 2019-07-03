<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Partner Summary Report</h2>
                    <div class="clearfix"></div>
                </div>
                <h2 id="msg_holder" style="text-align:center;color: #108c30;font-weight: bold;"></h2>
                <div class="x_content"></div>
                <table class="table table-bordered table-hover table-condensed" style="background: #fff;">
                    <thead>
                    <th>S.No.</th>
                    <th>Date</th>
                    <th>Download Report</th>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 1;
                        foreach ($summaryReports as $value) {
                            ?> 
                            <tr >
                                <td><?php echo $sn; ?></td>
                                <td><?php echo date("d-m-Y", strtotime($value['date'])); ?></td>
                                <td> <a class="btn btn-success" style="background: #2c9d9c;" href="<?php echo base_url(); ?>employee/partner/download_custom_summary_report/<?php echo $value['file_name'] ?>">Download</a></td>
                            </tr>
                            <?php
                            $sn++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

