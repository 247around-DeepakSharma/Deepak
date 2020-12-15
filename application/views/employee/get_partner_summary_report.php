<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">
                    <b> Download Summary Report</b>
                    <?php
                    // DO not show Download Real Time Summary Report Button , If Partner is not selected
                    $display = "";
                    if(empty($this->session->userdata('partner_id'))) { 
                        $display = "disabled"; 
                    } ?>
                    <i class="fa fa-info-circle text-success" style="float:right;margin-left:5px;padding-top:5px;" title="Select any Partner to download Real Time Summary Report"></i>                    
                    <a id="download_realtime_summary_report" href="<?php echo base_url() . "employee/partner/download_real_time_summary_report/" . $this->session->userdata('partner_id') ?>" class="btn btn-success" style="float:right;" <?php echo $display; ?>>Download Real Time Summary Report</a>
                    <div class="clear"></div>
                    
                </h3>
                <div class='panel'>
                    <div class='panel-body' style='padding:0px !important;'>
                        <form>
                            <div class="row">
                            <div class="col-md-2">
                                <label for="partner_id" style='margin-left:0% !important;'>Partner</label>
                                <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                            </div> 
                            <div class="col-md-2"> 
                                <label class="control-label" for="daterange">Registration Date</label><br>
                                <?php
                                $endDate = date('Y/m/d');
                                $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                                //$startDate = date('Y/m/d', strtotime("-".(date('d')-1)." days"));
                                $dateRange = $startDate . " - " . $endDate;
                                ?>
                                <input style="border-radius: 5px;"  type="text" placeholder="Registration Date" class="form-control" id="create_date" value="" name="create_date"/>
                            </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" for="daterange">Completion Date</label><br>
                                        <?php
                                        $endDate = date('Y/m/d');
                                        $startDate = date('Y/m/d', strtotime('-1 day', strtotime($endDate)));
                                        $dateRange = $startDate." - ".$endDate;
                                        ?>
                                        <input style="border-radius: 5px;"  type="text" placeholder="Completion Date" class="form-control" id="completion_date" value="" name="completion_date"/>
                                    </div>
                                </div> 
                            <div class="col-md-2">
                                <label for="Status">Status</label><br>
                                <select class="form-control" id="status" name="status">
                                    <option value="All">All</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="Status">States</label><br>
                                <select class="form-control" id="state"  name="state" multiple="">
                                    <?php
                                    foreach ($states as $state) {
                                        ?>
                                        <option value="<?php echo $state['state'] ?>"><?php echo $state['state']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                
                            </div>
                               
                                <div class="col-md-2">
                                    <input type="button" class="btn btn-success" style="margin-top:10%;float:left; border: 1px solid #2a3f54;background: #2a3f54;margin-bottom: 0px;" value="Generate Report" onclick="generate_summary_report()">
                                </div>
                           
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-condensed" style="margin-top: 28px;border: 2px solid #4b9c7a;" id="summary_report_table">
                    <thead style="background: #4b9c7a;color: #fff;font-size: 15px;">
                        <tr>
                            <th>Filters</th>
                            <th>Create Date</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($summaryReportData)) {
                            foreach ($summaryReportData as $summaryReport) {
                                $finalFilterArray = array();
                                $filterArray = json_decode($summaryReport['filters'], true);
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
                                    <td> <?php echo $summaryReport['create_date'] ?></td>
                                    <td> <a class="btn btn-success" style="background: #2a3f54;" href="<?php echo base_url(); ?>employee/partner/download_custom_summary_report/<?php echo $summaryReport['url'] ?>">Download</a></td>
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

<script>
    $("#state").select2({
        placeholder: "All",
        allowClear: true
    });
    $("#status").select2();
    $("#modal_service_id").select2({
        placeholder: "Select Appliance",
        allowClear: true
    });
    $('input[name="create_date"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY/MM/DD',
            cancelLabel: 'Clear',
            maxDate: 'now'
        }
    });
     $('input[name="completion_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear',
                 maxDate: 'now'
            }
        });
    $('input[name="create_date"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));

    });
    $('input[name="completion_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));  
    });

    $(document).ready(function () {
        
       $('#partner_id').on('change', function(){
            var partner_id = $(this).val();
            $('#download_realtime_summary_report').attr('disabled', true);
            if(partner_id != '' && partner_id != null && partner_id != 'All') {
                var url = '<?php echo base_url()."employee/partner/download_real_time_summary_report/"?>'+partner_id;
                $('#download_realtime_summary_report').attr('href', url);
                $('#download_realtime_summary_report').attr('disabled', false);
                var dataUrl = '<?php echo base_url()."employee/booking/get_summary_report_data/"?>'+partner_id;
                $.ajax({
                    type: 'POST',
                    url: dataUrl,
                    data: {is_wh: true},
                    success: function (response) {
                        $('#summary_report_table').children('tbody').html(response);
                    }
                });
            }
        }); 
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data: {is_wh: true},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id').select2();
                $('#partner_id').trigger('change');
            }
        });
    });
    
    function generate_summary_report(){
    
        var partnerID = $('#partner_id').val();
        if(partnerID == '' || partnerID == null) {
            alert('Please select partner.');
            return false;
        }

         var create_date = $('#create_date').val();
         var completion_date = $('#completion_date').val(); 
        if((create_date == '' || create_date == null) && (completion_date == '' || completion_date == null)) {
            alert('Please select a date range.');
            return false;
        }
        var create_date = $('#create_date').val();
        var dateArray = create_date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        
        if(diffDays>90){
            alert("Maximum range allowed is 3 months");
            return false;
        }  
        
        var completion_date = $('#completion_date').val();
        if(completion_date != '') {
            var completionDateArray = completion_date.split(" - ");
            var completionStartDate = completionDateArray[0];
            var completionEndDate =   completionDateArray[1];
            var completionStartDateObj = new Date(completionStartDate);
            var completionEndDateObj = new Date(completionEndDate);
            var completionTimeDiff = Math.abs(completionEndDateObj.getTime() - completionStartDateObj.getTime());
            var completiondiffDays = Math.ceil(completionTimeDiff / (1000 * 3600 * 24)); 

            if(completiondiffDays > 90){
                alert("Maximum range allowed is 3 months");
                return false;
            }        
        }
        
        var status = $('#status').val();
        var state = getMultipleSelectedValues('state');
        if(!state){
            state = 'All';
        }
        var cur_date = "<?php echo date("Y-m-d")?>";
        var table = document.getElementById("summary_report_table").getElementsByTagName('tbody')[0];
        var row = table.insertRow(0);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = " Registration Date :"+create_date+", Completion Date : "+completion_date+", Status : "+status+", State : "+state;
        cell2.innerHTML = cur_date;
        cell3.innerHTML = '<img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="width: 15%;">';
        $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/partner/create_and_save_partner_report/'+partnerID,
        data: {Date_Range: create_date,Completion_Date_Range: completion_date,Status: status,State: state},
        success: function (response) {
            var obj = JSON.parse(response);
            if(obj.response === "SUCCESS"){
                cell3.innerHTML = '<a class="btn btn-success" style="background: #2a3f54;" href="'+obj.url+'">Download</a>';
            }
            else{
                alert("Something Went Wrong Please Try Again");
                location.reload();
            }
        }
        });
       
    }
</script>
