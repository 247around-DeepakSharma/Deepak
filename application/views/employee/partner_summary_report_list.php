<div id="page-wrapper">
        <div class="container-fluid">
            <div class="page_title">
                        <h3>Partner Summary Reports</h3>
            </div>
            <hr>
            <div class="page_content ">
                <table class="table table-condensed table-hover table-bordered text-center">
                    <thead style="background: #f9f9f9;">
                        <th class="text-center">S.No.</th>
                        <th class="text-center">Partner</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Download Report</th>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($summaryReports as $value) {?> 
                        <tr >
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['public_name']; ?></td>
                            <td><?php echo $value['date']; ?></td>
                            <td> <a class="btn btn-success" style="background: #2c9d9c;" href="<?php echo base_url(); ?>employee/partner/download_custom_summary_report/<?php echo $value['file_name']?>">Download</a></td>
                        </tr>
                        <?php $sn++;}?>
                    </tbody>
                </table>
            </div>
            </div>
    </div>