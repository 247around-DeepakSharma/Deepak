<style>
    .select2-selection{
        border-radius: 4.5px !important;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h1>
                        Custom Reports
                    </h1>                    
                    <div class="clearfix"></div>
                </div>
                <h2 id="msg_holder" style="text-align:center;color: #108c30;font-weight: bold;"></h2>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <table class="table table-bordered table-condensed" width="50%">
                        <thead>
                            <tr>
                                <th width="10%">S.No.</th>
                                <th width="70%">Description</th>
                                <th width="20%">Download Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            foreach ($reports as $id => $data) { ?>
                                <tr>
                                    <td><?php echo $sno; ?></td>
                                    <td style="font-size:15px;"><?php echo $data['subject']; ?></td>
                                    <td><a href="<?php echo base_url() . "employee/reports/download_custom_report/" . $data['tag']; ?>"><span style="color:blue;font-size:30px;" class="glyphicon glyphicon-download"></span></a></td>
                                </tr>
                            <?php $sno++; ?>
                            <?php } ?>        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>