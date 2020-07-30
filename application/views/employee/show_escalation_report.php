<div class="container-fluid">
    <div class="row">
        <div class="col-md-7">
            <h2>Escalation Report</h2>
        </div>
    </div> 
    <hr/>
    <div class="row">            
        <div class="col-md-2" style='float:right;'>
            <br/>
            <span class="btn btn-primary" id="generate_report">Generate Report</span>
        </div>
        <div class="col-md-3" style='float:right;width:300px;'>
            <label for="date">Date Range*</label><br/>
            <input type='hidden' name='esDate' id='esDate' value="<?php echo date('Y-m-d'); ?>">
            <input type='hidden' name='eeDate' id='eeDate' value="<?php echo date('Y-m-d'); ?>">
            <div class="input-group input-append date">                
                <input id="date" class="form-control date"  name="date" type="text" value = "<?php echo date('d/m/Y'). ' - '. date('d/m/Y'); ?>" autocomplete='off' onkeydown="return false" >
                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
        <div class="col-md-3" style='float:right;width:400px;'>
            <label for="partner_id">Partner*</label><br/>
            <select class="form-control" id="partner_id" required="" name="partner_id"></select>
        </div>            
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-bordered" id="escalation_report_table">
                <thead style="background: #4b9c7a;color: #fff;font-size: 15px;">
                    <tr>
                        <th>Filters</th>
                        <th>Create Date</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($escalationReportData)) {
                        foreach ($escalationReportData as $escalationReport) {
                            $finalFilterArray = array();
                            $filterArray = json_decode($escalationReport['filters'], true);
                            foreach ($filterArray as $key => $value) {
                                if ($key == "Date_Range") {
                                    $dArray = explode(" - ", $value);
                                    $key = "Registration Date";
                                    $startTemp = strtotime($dArray[0]);
                                    $endTemp = strtotime($dArray[1]);
                                    $startD = date('d-F-Y', $startTemp);
                                    $endD = date('d-F-Y', $endTemp);
                                    $value = $startD . " To " . $endD;
                                }
                                $finalFilterArray[] = $key . " : " . $value;
                            }
                            ?>
                            <tr>
                                <td> <?php echo implode(", ", $finalFilterArray); ?></td>
                                <td> <?php echo date("d-M-Y", strtotime($escalationReport['create_date'])) ?></td>
                                <td> <a class="btn btn-success" style="background: #2a3f54;" href="<?php echo base_url(); ?>employee/partner/download_custom_escalation_report/<?php echo $escalationReport['url'] ?>">Download</a></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loader.gif" style="display: none;"></center>
        </div>
    </div>
</div>
<script type="text/javascript">
    
    $('#date').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
    
    $(function() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data: {},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id').select2();
                $('#partner_id').trigger('change');
            }
        });        
        
        $('#partner_id').on('change', function(){
            var partner_id = $(this).val();
            if(partner_id != '' && partner_id != null) {
                var dataUrl = '<?php echo base_url()."employee/partner/get_escalation_report_data/"?>'+partner_id;
                $.ajax({
                    type: 'POST',
                    url: dataUrl,
                    data: {},
                    beforeSend: function(){
                        $("#loader_gif_title").show();            
                    },
                    success: function (response) {
                        $('#escalation_report_table').children('tbody').html(response);                        
                        $("#loader_gif_title").hide();
                    }
                });
            }
        }); 
        
        $('input[name="date"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear',
                maxDate: 'now'
            }
        },  function(start, end, label) {
                var startDateObj = new Date(start);
                var endDateObj = new Date(end);
                var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 

                var date = startDateObj.getFullYear()+'-'+(("0" + (startDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + startDateObj.getDate()).slice(-2))+' - '+endDateObj.getFullYear()+'-'+(("0" + (endDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + endDateObj.getDate()).slice(-2));
                var esDate = startDateObj.getFullYear()+'-'+(("0" + (startDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + startDateObj.getDate()).slice(-2));
                var eeDate = endDateObj.getFullYear()+'-'+(("0" + (endDateObj.getMonth() + 1)).slice(-2))+'-'+(("0" + endDateObj.getDate()).slice(-2));
                if(diffDays > 92) {
                    alert("Maximum range allowed is 3  month.");
                    return false;
                }
                $("#esDate").val(esDate);
                $("#eeDate").val(eeDate);
        });
        
        $("#generate_report").click(function(){
            var partner_id = $("#partner_id").val();
            var date = $('input[name="date"]').val();
            var esdate = $("#esDate").val();
            var eedate = $("#eeDate").val();
            if(partner_id == "" || partner_id == null) {
                alert("Select partner");
                return false;
            }
            var cur_date = "<?php echo date("Y-m-d")?>";
            var table = document.getElementById("escalation_report_table").getElementsByTagName('tbody')[0];
            var row = table.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.innerHTML = " Escalation Date :"+date;
            cell2.innerHTML = cur_date;
            cell3.innerHTML = '<img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="width: 15%;">';
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/partner/create_and_save_partner_escalation_report',
                data: {esDate: esdate, eeDate: eedate, partner_id: partner_id},
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj.response === "SUCCESS"){
                        cell3.innerHTML = '<a class="btn btn-success" style="background: #2a3f54;" href="'+obj.url+'">Download</a>';
                    }
                    else{
                        alert("Some Error Occured on Server ! Please Try Again");
                        location.reload();
                    }
                }
            });
        });
    });   
    
</script>

