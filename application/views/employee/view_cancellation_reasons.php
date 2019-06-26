<div id="page-wrapper">
    <h1 class="page-header">
        Modify Booking Flag
    </h1>
    <div class="container-fluid">
        <div class="row" style="margin-top:40px; ">
            <div class="col-lg-12">
                <table class="table table-striped table-bordered" id="table1">
                    <thead>
                    <th>S.No.</th>
                    <th>Cancellation Reason</th>
                    <th>
                        Booking Flag
                        <?php
                            $decision_flag_details  =   "<table style='text-align:left;margin-top:5px;margin-bottom:5px;' id='info-table' border=1>
                                                            <tr><th colspan=2 style='padding:5px;'><i class='fa fa-info-circle'></i>&nbsp;&nbsp;Description</th></tr>
                                                            <tr><td>0</td><td>Cancelled the booking directly</td></tr>
                                                            <tr><td>1</td><td>Do not send sms to customer for fake cancellation</td></tr>
                                                            <tr><td>2</td><td>Auto approve on fake cancellation missed call</td></tr>
                                                            <tr><td>3</td><td>Show highlighted on fake_cancellation missed call</td></tr>
                                                            <tr><td>4</td><td>Approve in 2 attamps</td></tr>
                                                        </table>";
                        ?>
                        <i class="fa fa-info-circle text-primary" data-toggle="tooltip" data-html="true" data-placement="right" title="<?= $decision_flag_details; ?>" style="margin-left:5px;"></i>
                    </th>
                    </thead>
                    <tbody>
                        <?php $count = 1;
                        foreach ($data as $value) { ?>
                            <tr>
                                <td><?php echo $count;$count++ ?></td>
                                <td><?php echo $value['reason']; ?></td>
                                <td>
                                    <select id="select<?=$value['id']?>" name="decision_flag" value="<?= $value['decision_flag']; ?>" class="form-control decision_flag" style="width:100px;">
                                    <?php
                                        $arr_values = [0,1,2,3,4];
                                        foreach($arr_values as $rec_value)
                                        {
                                            $selected = ($rec_value == $value['decision_flag']) ? "selected" : "";
                                            echo '<option value="'.$rec_value.'" '.$selected.'>'.$rec_value.'</option>';
                                        }
                                    ?>                            
                                    </select>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>	
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
        $("#table1").dataTable();
        $('.decision_flag').change(function(){
            var id = this.id;
            var flag_value = this.value;
            $.post("<?php echo base_url(); ?>employee/booking/change_booking_cancellation_flag", {id : id, flag_value:flag_value}).done(function(data){
                if(data == 1)
                {
                    $("#"+id).css('border-color', 'green');
                }
                else
                {
                    $("#"+id).css('border-color', 'red');
                }
            });
        });
    });   
    
</script>
<style>
    #info-table td
    {
        padding :5px;
    }
    .dataTables_filter, .dataTables_paginate
    {
        float : right;
    }
</style>