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
                    <h2>Reports</h2>
                    <div class="clearfix"></div>
                </div>
                    <h2 id="msg_holder" style="text-align:center;color: #108c30;font-weight: bold;"></h2>
                <div class="x_content">
                                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-1" role="tab" data-toggle="tab" aria-expanded="true">
                                    Summary Reports
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true">
                                    Serviceability Report
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-3" role="tab" data-toggle="tab" aria-expanded="true">
                                    Downloads
                                </a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="tabs-1">
                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title" style="border-bottom: none;">
                                           
                                            <form style="border: 2px solid #4b9c7a;padding: 10px 0px;">
                        <div class="form-group col-md-4"> 
                            <label class="control-label" for="daterange">Registration Date</label><br>
                            <?php
                            $endDate = date('Y/m/d');
                            $startDate = date('Y/m/d', strtotime("-".(date('d')-1)." days"));
                            $dateRange = $startDate." - ".$endDate;
                            ?>
                            <input style="border-radius: 5px;"  type="text" placeholder="Registration Date" class="form-control" id="create_date" value="<?php echo $dateRange ?>" name="create_date"/>
                        </div>
                        
                        <div class="form-group col-md-4">
                        <label for="Status">Status</label><br>
                        <select class="form-control" id="status" name="status">
                               <option value="All">All</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                <div class="form-group col-md-4">
                        <label for="Status">States</label><br>
                        <select class="form-control" id="state"  name="state" multiple="">
                                <?php
                                foreach($states as $state){
                                    ?>
                                <option value="<?php echo $state['state'] ?>"><?php echo $state['state'];  ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                                <div class="form-group">
                                    <input type="button" class="btn btn-success" style="float:right; border: 1px solid #2a3f54;background: #2a3f54;margin-bottom: 0px;" value="Generate Report" onclick="generate_summary_report()">
                                    </div>
                    </form>
                                            <hr>
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
                                foreach($summaryReportData as $summaryReport){
                                    $finalFilterArray = array();
                                    $filterArray = json_decode($summaryReport['filters'],true);
                                    foreach($filterArray as $key=>$value){
                                        if($key == "Date_Range"){
                                            $dArray = explode(" - ",$value);
                                            $key  = "Registration Date";
                                            $startTemp = strtotime($dArray[0]);
                                            $endTemp = strtotime($dArray[1]);
                                            $startD = date('d-F-Y',$startTemp);
                                            $endD = date('d-F-Y',$endTemp);
                                            $value = $startD." To ".$endD;
                                        }
                                        $finalFilterArray[] = $key." : ". $value; 
                                        
                                    }
                                    ?>
                                <tr>
                                    <td> <?php echo implode(", ", $finalFilterArray); ?></td>
                                    <td> <?php echo $summaryReport['create_date'] ?></td>
                                    <td> <a class="btn btn-success" style="background: #2a3f54;" href="<?php echo base_url(); ?>employee/partner/download_custom_summary_report/<?php echo $summaryReport['url']?>">Download</a></td>
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
                            <div class="tab-pane" id="tabs-2">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title" style="border-bottom: none;">
                                            <a style="float: right;background: #2a3f54;border: #2a3f54;" class="btn btn-success" href="<?php echo base_url(); ?>partner/download_sf_list_excel">Download Service Center List</a>
                                            <p style="font-size: 15px;padding: 5px 12px;background: #4b9c7a;color: #fff;width: 40%;border-radius: 3px;">Please Select Column For  Serviceability Report </p>
                                            <form>
                                    <div id="appliance_id_holder">
                                        <select class="form-control" id="modal_service_id" name="service_id[]"   style="width:40%;"> 
                                            <option value="">Select Appliance</option>
                                            <?php
                                            foreach($services as $service){
                                                ?>
                                            <option value="<?php echo $service->id ?>"><?php echo $service->services ?> </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        </div>
                                <div class="checkbox"><label><input type="checkbox" name="pincode_opt" id="pincode_opt" value="">Pincode</label></div>
                                <div class="checkbox"><label><input type="checkbox" name="city_opt" id="city_opt" value="">City</label></div>
                                <div class="checkbox"><label><input type="checkbox" name="state_opt" id="state_opt" value="">State</label></div>
                                    <div class="text-left">
                                        <button type="button" style="background: #2a3f54;border-color: #2a3f54;" class="btn btn-success" onclick="process_serviceability_report()">Export
                                            <img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="width: 23%;display:none;"></button>
                                        <div class="btn btn-default" data-dismiss="modal">Cancel</div>
                                    </div>
                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                              <div class="tab-pane" id="tabs-3">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title" style="border-bottom: none;">
                                            <a style="float: left;background: #2a3f54;border: #2a3f54;" class="btn btn-success" href="<?php echo base_url(); ?>partner/upcountry_report">Download Upcountry Details</a>
                                           <a style="float: left;background: #2a3f54;border: #2a3f54;" class="btn btn-success" href="<?php echo base_url(); ?>partner/download_spare_part_shipped_by_partner">Download All Shipped Spare Parts</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
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
        $('input[name="create_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));  
            
        });
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
        function get_report(){
           var dateRange = $('input[name="create_date"]').val();
            var dateArray = dateRange.split(" - ");
            var startDate = dateArray[0];
            var endDate =   dateArray[1];
            var startDateObj = new Date(startDate);
            var endDateObj = new Date(endDate);
            var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            if(diffDays>365){
                alert("Please select date range with in less then a year Range");
            }
            else{
               var status = $('#status').val();
               var state = $('#state').val();
               if(!state){
                   state = new Array("all"); 
               }
               stateList = state.join();
                var data = {startDate: startDate, endDate: endDate,status:status,state:stateList};
                url =  '<?php echo base_url(); ?>employee/partner/create_and_send_partner_report/<?php echo $this->session->userdata('partner_id'); ?>';
                var post_request = 'POST'; 
                sendAjaxRequest(data,url,post_request).done(function(response){
                    $("#msg_holder").text(response);
                });
            }
        }
        function showHideApplianceForm(value){
                if(value){
                    document.getElementById('appliance_id_holder').style.display='block';
                }
                else{
                    document.getElementById('appliance_id_holder').style.display='none';
                }
            }
            function process_serviceability_report(){
                var service_opt = 0;
                var pincode_opt = 0;
                var city_opt = 0;
                var state_opt =0;
                if ($('#pincode_opt').is(":checked")){
                    pincode_opt = 1;
                }
                if ($('#city_opt').is(":checked")){
                    city_opt = 1;
                }
                if ($('#state_opt').is(":checked")){
                    state_opt = 1;
                }
                var service_id = getMultipleSelectedValues('modal_service_id');
                if(service_id && (pincode_opt === 1 || city_opt ===  1 || state_opt === 1)){
                      send_csv_request(pincode_opt,state_opt,city_opt,service_id);
                }
                else{
                     alert("Please Select atleast 1 option and Appliance");
                }
            }
    function getMultipleSelectedValues(fieldName){
       fieldObj = document.getElementById(fieldName);
       var values = [];
       var length = fieldObj.length;
       for(var i=0;i<length;i++){
          if (fieldObj[i].selected == true){
              values.push(fieldObj[i].value);
          }
       }
      return values.toString();
   }
    function send_csv_request(pincode_opt,state_opt,city_opt,service_id){
        $("#loader_gif_title").show();
        if(!service_id){
            service_id = 'all';
        }
        $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>partner/serviceability_list',
        data: {appliance_opt: 1,pincode_opt: pincode_opt,state_opt: state_opt,city_opt: city_opt,service_id: service_id},
        success: function (response) {
             $("#loader_gif_title").hide();
            var jsondata = JSON.parse(response);
            if(jsondata['response'] === "success"){
                    window.location.href = jsondata['path'];
              }
        }
        });
        
    }   
    function generate_summary_report(){
        var create_date = $('#create_date').val();
        var dateArray = create_date.split(" - ");
        var startDate = dateArray[0];
        var endDate =   dateArray[1];
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var timeDiff = Math.abs(endDateObj.getTime() - startDateObj.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        if(diffDays>365){
            alert("Please select date range with in less then a year Range");
        }
        else{
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
            cell1.innerHTML = " Registration Date :"+create_date+", Status : "+status+", State : "+state;
            cell2.innerHTML = cur_date;
            cell3.innerHTML = '<img id="loader_gif_title" src="<?php echo base_url(); ?>images/loadring.gif" style="width: 15%;">';
            $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/create_and_save_partner_report/<?php echo $this->session->userdata('partner_id'); ?>',
            data: {Date_Range: create_date,Status: status,State: state},
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
    }
</script>