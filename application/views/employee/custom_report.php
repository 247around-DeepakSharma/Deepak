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
                                <th width="10%">S. No.</th>
                                <th width="30%">Description</th>
                                <th width="20%">Department</th>
                                <th width="20%">Period</th>
                                <th width="20%">Download Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            foreach ($reports as $id => $data) { ?>
                                <tr>
                                    <td><?php echo $sno;?></td>
                                    <td style="font-size:15px;"><?php echo $data['subject']; ?></td>
                                    <td style="font-size:15px;"><?php echo $data['department']; ?></td>
                                    <td>
                                    <!-- Show date picker if period column is 1 for this query else leave it blank -->
                                    <?php if($data['date_filter'] == 1) { ?>
                                        <input type="text" class="form-control" style="font-size: 15px; 
                                               background-color:#fff;" id="period_<?php echo $sno;?>" 
                                               name="period_<?php echo $sno;?>" readonly='true' >
                                    <?php } else { ?>
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar" style='color:grey;background:#ddd;cursor: not-allowed;'></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($data['date_filter'] == 1) { ?>
                                        <a onclick='return download_report("<?php echo $data['tag']; ?>", <?php echo $sno; ?>, "1");' id="dload_<?php echo $sno;?>">
                                            <span style="color:blue;font-size:30px;" class="glyphicon glyphicon-download"></span>
                                        </a>
                                        <?php } else { ?>
                                            <a onclick='return download_report("<?php echo $data['tag']; ?>", <?php echo $sno; ?>, "0");' id="dload_<?php echo $sno;?>">
                                                <span style="color:blue;font-size:30px;" class="glyphicon glyphicon-download"></span>
                                            </a>
                                        <?php } ?>                                        
                                    </td>
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

<script type="text/javascript">
    $(function() {
        $("[id^=period]").daterangepicker({
            locale: {
               format: 'YYYY-MM-DD'
            },
            startDate: '<?php echo date("Y-m-d", strtotime("-1 month")) ?>',
            endDate: '<?php echo date('Y-m-d', strtotime('-1 day')); ?>'
        },function(start,end,label){
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    
    function download_report(tag, sequence_no, date_filter = 0)
    {
        var period = "";
        if(date_filter == 1){
            period = $("#period_"+sequence_no).val();
        }
        window.open("<?php echo base_url(); ?>employee/reports/download_custom_report/"+tag+"/"+period); 
    }
</script>
