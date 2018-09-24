    <style>
        .loader_img{
             width: 22px;
        }
        </style>
        <div class="row" style="margin: 0px;">
    <div class="col-md-10 col-md-offset-2" style="margin: 10px 89px;width: 88%;padding: 0px;">
        <div class="x_title" style="background: #2c9d9c; color: #fff;">
                <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">Performance Summary</h2>
                    <div class="clearfix"></div>
                </div>
            <div style="margin-top: 0px;display: flex;font-size: 25px;border: 2px solid #2c9d9c;padding: 10px 210px;border-bottom: none;">
                    <b>Rating:</b> &nbsp;
                    <b><span <?php if($rating > '3.5') { echo "class='text-success'";}else{echo "class='text-danger'";}?>><?php echo $rating; ?> /5</span></b> &nbsp;
                    <div class="sf-escalation">
                        <b> <span style="color:#333;"> | </span> Overall Escalation:</b>
                        <b><span id="sf-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
                    </div>
                    <div class="sf-escalation">
                        <b> <span style="color:#333;"> | </span> Current Month Escalation:</b>
                        <b><span id="sf-cm-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
                    </div>
            </div>
                 <div id="header_summary" style="border: 2px solid #2c9d9c;padding: 19px 12px 0px;">
            <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
            </div>  
    </div>
   
        <div class="col-md-10 col-sm-10 col-xs-10" style="padding: 0px;margin: 10px 90px;border: 2px solid #2c9d9c; width: 88%;background: #fff;">
            <div class="x_title" style="background: #2c9d9c; color: #fff;">
                <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">TAT Reporting<button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="(Booking Completed on x Day / Total Completed Bookings (Within Selected Range))*100">?</button></h2>
                    <div class="clearfix"></div>
                </div>
            <div class="filter_container" style="margin-top: 10px;">
                                <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Appliance</label>
                            <select class="form-control filter_table" id="service_id" name="services">
                                <option value="" selected="selected">All</option>
                                <?php foreach($services as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">  
                            <label for="">Is Free <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Free For Customer">?</button></label>
                            <select class="form-control filter_table" id="free_paid" name="free_paid">
                                <option value="" selected="selected">All</option>
                                <option value="Yes">Yes (In Warranty)</option>
                                <option value="No">No (Out Of Warranty)</option>  
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label for="">Is Upcountry</label>
                            <select class="form-control filter_table" id="upcountry" name="upcountry">
                                <option value="">All</option>
                                <option value="Yes">Yes</option>
                                 <option value="No" selected="selected">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-3" style="    width: 18%;">
                                         <label for="">Booking Completed Date <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="By Default last 30 days">?</button></label>
                                         <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id">
                            </div>
                    <div class="form-group col-md-3">
                                         <label for="">Booking Status</label>
                                        <select class="form-control"  ng-model="status" id="completed_status">
                                            <option value="">All</option>
                                            <option value="Completed" ng-selected="true">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                    </div>
                <button class="btn btn-primary" style="margin-top: 23px;background: #c1591c;border-color: #c1591c;" onclick="fetch_filtered_tat_report()">Apply Filters</button>
             </div>
            <div class="clearfix"></div>
            <hr style="border: 1px solid #2c9d9c;">  
            <div class="col-md-12" id="tat_holder">
                <table class="table table-bordered" id="tat_table">
                    <thead>
                        <tr>
                        <th>Service</th>
                        <th>Day 0</th>
                        <th>Day 1</th>
                        <th>Day 2</th>
                        <th>Day 3</th>
                        <th>Day 4</th>
                        <th>Day 5-Day 7</th>
                        <th>Day 8-Day 15</th>
                        <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Installation</b></td>
                            <td id="Installation_0" style="background: #c1591c;color: #fff;" class="blinking"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_1"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_2"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_3"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_4"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_5"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_8"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Installation_16"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                        </tr>
                        <tr>
                            <td><b>Repair Without Spare</b></td>
                            <td id="Repair_without_part_0" style="background: #c1591c;color: #fff;" class="blinking"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_1"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_2"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_3"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_4"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_5"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_8"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_without_part_16"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                        </tr>
                        <tr>
                            <td><b>Repair With Spare</b></td>
                            <td id="Repair_with_part_0"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_1"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_2"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_3"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_4"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_5"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_8"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_with_part_16"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                        </tr>
                        <tr>
                            <td><b>All Repair</b></td>
                            <td id="Repair_0"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_1"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_2"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_3"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_4"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_5"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_8"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="Repair_16"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                        </tr>
                        <tr>
                            <td><b>Total</b></td>
                            <td id="total_0"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_1"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_2"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_3"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_4"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_5"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_8"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                            <td id="total_16"><center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<script>
    $(document).ready(function () {
        get_header_summary();
        get_sf_tat_report("Installation");
        get_sf_tat_report("Repair_with_part");
        get_sf_tat_report("Repair_without_part");
        get_sf_tat_report("Repair");
        get_sf_tat_report();
        get_escalation_report();
    });
    function get_escalation_report(){
        $.ajax({
                method:'POST',
                url: '<?php echo base_url(); ?>employee/service_centers/get_sf_escalation/<?php echo $this->session->userdata('service_center_id')?>',
                success:function(res){
                    if(res === 'empty'){
                        $('#sf-escalation-value').html('0');
                        $('#sf-cm-escalation-value').html('0');
                    }else{
                        var data = JSON.parse(res);
                        $('#sf-escalation-value').html(data['total_escalation_per']);
                        $('#sf-cm-escalation-value').html(data['current_month_escalation_per']);
                    }


                }
            });
        }
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    function get_sf_tat_report(request_type = ""){
        vendor_id = '<?php echo $this->session->userdata('service_center_id'); ?>';
        service_id = $("#service_id").val();
        free_paid = $("#free_paid").val();
        upcountry = $("#upcountry").val();
        completed_daterange_id = $("#completed_daterange_id").val();
        completed_status = $("#completed_status").val();
        var data = {'vendor_id':vendor_id,'services':service_id,'free_paid':free_paid,'upcountry':upcountry,'daterange_completed_bookings':completed_daterange_id,'status':completed_status,'request_type':request_type};
        url =  '<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/00/1';
        sendAjaxRequest(data,url,"POST").done(function(response){
            if(!request_type){
                request_type = 'total';
            }
            var obj = JSON.parse(response);
            if(obj[0]){
                $("#"+request_type+"_0").text(obj[0]['TAT_0']+' ('+obj[0]['TAT_0_per']+'%)');
                $("#"+request_type+"_1").text(obj[0]['TAT_1']+' ('+obj[0]['TAT_1_per']+'%)');
                $("#"+request_type+"_2").text(obj[0]['TAT_2']+' ('+obj[0]['TAT_2_per']+'%)');
                $("#"+request_type+"_3").text(obj[0]['TAT_3']+' ('+obj[0]['TAT_3_per']+'%)');
                $("#"+request_type+"_4").text(obj[0]['TAT_4']+' ('+obj[0]['TAT_4_per']+'%)');
                $("#"+request_type+"_5").text(obj[0]['TAT_5']+' ('+obj[0]['TAT_5_per']+'%)');
                $("#"+request_type+"_8").text(obj[0]['TAT_8']+' ('+obj[0]['TAT_8_per']+'%)');
                $("#"+request_type+"_16").text(obj[0]['TAT_16']+' ('+obj[0]['TAT_16_per']+'%)');
            }
            else{
                $("#"+request_type+"_0").text("0 (0.00%)");
                $("#"+request_type+"_1").text("0 (0.00%)");
                $("#"+request_type+"_2").text("0 (0.00%)");
                $("#"+request_type+"_3").text("0 (0.00%)");
                $("#"+request_type+"_4").text("0 (0.00%)");
                $("#"+request_type+"_5").text("0 (0.00%)");
                $("#"+request_type+"_8").text("0 (0.00%)");
                $("#"+request_type+"_16").text("0 (0.00%)");
            }
        }); 
    }
     $(function() {
     var d = new Date();
        n = d.getMonth();
        y = d.getFullYear();
        date = d.getDate();
        $('input[name="daterange_completed_bookings"]').daterangepicker({
             timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: y+'-'+n+'-'+date
    });
});
function fetch_filtered_tat_report(){
    get_sf_tat_report("Installation");
    get_sf_tat_report("Repair_with_part");
    get_sf_tat_report("Repair_without_part");
    get_sf_tat_report("Repair");
   get_sf_tat_report();
}
function get_header_summary(){
       $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/service_centers/get_header_summary/',
         success: function (data) {
          $("#header_summary").html(data);   
    
         }
       });
    
    }
    </script>
    <style>
        .blinking{
    animation:blinkingText 0.5s infinite;
}
@keyframes blinkingText{
    0%{     background-color: #008000;    }
    49%{    background-color: #008000; }
    50%{    background-color: #c1591c; }
    99%{    background-color:#c1591c;  }
    100%{   background-color: #008000;    }
}
        </style>

