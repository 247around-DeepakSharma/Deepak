<style>
    .highcharts-credits,.highcharts-button-symbol{display:none}
</style>


<div class="right_col" role="main">
    <div class="partner_specific_spare_part_dashboard">
        <div class="page-title">
            <div class="title_left">
                <h3><?php echo $partner_name; ?> Spare Reports</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Spare Details by Status <span class="badge badge-info" data-toggle="popover" data-content="Below figures show data about <?php echo $partner_name; ?> spare according to their current status"><i class="fa fa-info"></i></span></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif1" src="" style="display: none;"></center></div>
                    <div class="x_content">
                        <div id="spare_details_by_status" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Spare Snapshot <span class="badge badge-info" data-toggle="popover" data-content="Below figures show data about total spare parts, partner out of tat, sf out of tat count( partner out of tat count start after 30 days of parts shipped by partner. sf out of tat days count start after 7 days of booking completion by sf)"><i class="fa fa-info"></i></span></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12"><center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif"></center></div>
                    <div class="x_content">
                        <div id="spare_snapshot" style="width:100%; height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row" style="margin-top:10px;">
            <!-- SF Spare Parts Details -->
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Service Center Spare Parts Details <span class="badge badge-info" data-toggle="popover" data-content="Below table show data about pending defective spares on sf which is out of tat by sf wise for <?php echo $partner_name; ?>( Out of tat days count start after 7 days of booking completion by sf)"><i class="fa fa-info"></i></span></h2>
                        <div class="nav navbar-right panel_toolbox">
                            <div class="pull-right">
                                <a href="<?php echo base_url();?>employee/dashboard/sf_oot_spare_full_view/<?php echo $partner_id; ?>/<?php echo $partner_name; ?>" class="btn btn-sm btn-success" target="_blank">Show All</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12">
                        <center><img id="loader_gif3" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                    <div class="x_content">
                        <div id="spare_details_by_sf" style="width:100%; display: none;" >
                            <table id="spare_details_by_sf_table" class="table table-bordered table-responsive" width="100%">
                                <thead>
                                    <th>S.No.</th>
                                    <th>Service Center</th>
                                    <th>Defective Spare Need to be Shipped (OOT)</th>
                                </thead>
                                <tbody id="spare_details_by_sf_table_data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End SF Spare Parts Details-->
        </div>
        
        <div class="row" style="margin-top:10px;">
            <!-- SF Spare Parts Details -->
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Spare Parts Booking</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div role="tabpanel"> 
                            <div class="col-md-12">
                                <ul class="nav nav-tabs" role="tablist" >
                                    <li role="presentation" class="active"><a href="#spare_parts_requested" aria-controls="spare_parts_requested" role="tab" data-toggle="tab">Parts Requested</a></li>
                                    <li role="presentation"><a href="#shipped" aria-controls="shipped" role="tab" data-toggle="tab">Partner Shipped Part</a></li>
                                    <li role="presentation"><a href="#delivered" aria-controls="delivered" role="tab" data-toggle="tab">SF Received Part</a></li>
                                    <li role="presentation"><a href="#defective_part_pending" aria-controls="defective_part_pending" role="tab" data-toggle="tab">Defective Part Pending</a></li>
                                    <li role="presentation"><a href="#defective_part_rejected_by_partner" aria-controls="defective_part_rejected_by_partner" role="tab" data-toggle="tab">Defective Part Rejected By Partner</a></li>
                                    <li role="presentation"><a href="#defective_part_shipped_by_SF" aria-controls="defective_part_shipped_by_SF" role="tab" data-toggle="tab">Defective Part Shipped By SF</a></li>
                                    <li role="presentation"><a href="#defective_part_shipped_by_SF_approved" aria-controls="defective_part_shipped_by_SF" role="tab" data-toggle="tab">Approved Defective Part By Admin</a></li>

                                </ul>
                            </div>
                            <div class="tab-content" id="tab-content">
                                <center style="margin-top:30px;"> <img style="width: 60px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            <!-- End SF Spare Parts Details-->
        </div>
    </div>
</div>
<!-- /page content -->

<script>
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
    var partner_name = '<?php echo $partner_name; ?>';
    var partner_id = '<?php echo $partner_id; ?>'; 

    $(document).ready(function () {
        
        get_partner_spare_data_by_status();
        
        //sf spare status
        get_partner_spare_details_by_sf();
        
        //partner spare snapshot
        get_partner_spare_snapshot();
        
        //spare parts booking
        spare_booking_on_tab();
        
        $('[data-toggle="popover"]').popover({
            placement : 'right',
            trigger : 'hover'
        });
        
    });
    
    //this function is used to call ajax request
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    function get_partner_spare_data_by_status(){
        var url = baseUrl + '/employee/dashboard/get_partner_specific_count_by_status';
        $('#loader_gif1').css('display', 'inherit');
        $('#loader_gif1').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $('#spare_details_by_status').hide();
        $.ajax({
            type: 'POST',
            url: url,
            data: {partner_id: partner_id},
            success: function (response) {
                $('#loader_gif1').attr('src', "");
                $('#loader_gif1').css('display', 'none');
                $('#spare_details_by_status').show();
                var data = $.parseJSON(response);
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'spare_details_by_status',
                        type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            borderWidth: 0,
                            showInLegend: true
                        }
                    },
                    series: [{
                            name: 'Bookings',
                            colorByPoint: true,
                            data: data
                        }],
                    drilldown: {
                        series: []
                    }
                });
            }
        });
    }
    
    //this function is used to get the spare details for sf
    function get_partner_spare_details_by_sf(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_spare_details_by_sf';
        data = {is_show_all:0,partner_id:partner_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_spare_parts_by_sf_table(response);
        });
    }
    
    function create_spare_parts_by_sf_table(response){
        obj = JSON.parse(response);
        $('#loader_gif3').hide();
        $('#spare_details_by_sf').fadeIn();
        var table_body_html = '';
        $.each(obj, function (index,val) {
            table_body_html += '<tr>';
            table_body_html += '<td>' + (Number(index)+1) +'</td>';
            table_body_html += '<td>' +val['name'] +'</td>';
            table_body_html += '<td>' +val['oot_defective_parts_count'] +'</td>';
            table_body_html += '</tr>';
        });
        $('#spare_details_by_sf_table_data').html(table_body_html);
    }
    
    function spare_booking_on_tab(){
        $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/inventory/spare_part_booking_on_tab',
         data:{partner_id:partner_id},
         success: function (response) {
          $("#tab-content").html(response);   
         }
       });
    }
    
    function get_partner_spare_snapshot(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_partner_spare_snapshot';
        data = {partner_id:partner_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_partner_spare_snapshot_chart(response);
        });
    }
    
    $(document).on("click", ".open-adminremarks", function () {
        
        var booking_id = $(this).data('booking_id');
        var url = $(this).data('url');
        $('#modal-title').text(booking_id);
        $('#textarea').val("");
        $("#url").val(url);
        $button_text = $(this).text();
        if($button_text === "Approve Invoice"){
            $("#charges").css("display","block");
             var charge = $(this).data('charge');
            $("#charges").val(charge);
        } else {
            $("#charges").css("display","none");
            $("#charges").val(0);
        }

    });
    
    function reject_parts(){
      var remarks =  $('#textarea').val();
      //var booking_id = $('#modal-title').text();
      var courier_charge = $('#charges').val();
      if(remarks !== ""){
        $('#reject_btn').attr('disabled',true);
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{remarks:remarks,courier_charge:courier_charge},
            success: function(data){
                $('#reject_btn').attr('disabled',false);
                if(data === "Success"){
                  //  $("#"+booking_id+"_1").hide()
                    $('#myModal2').modal('hide');
                    alert("Updated Successfully");
                    location.reload();
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } else {
          alert("Please Enter Remarks");
      }
    }
    
    function create_partner_spare_snapshot_chart(response) {
        var data = JSON.parse(response);
        var spare_status = data.status.split(',');
        var spare_count = JSON.parse("[" + data.spare_count + "]");
        $('#loader_gif2').hide();
        $('#spare_snapshot').fadeIn();
        rm_based_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'spare_snapshot',
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: spare_status
            },
            labels: {
                items: [{
                    html: 'Amount',
                    style: {
                        left: '500px',
                        top: '-6px',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }]
            },
            tooltip: {
                formatter: function() {
                    return this.x + '<br> Count: ' + this.y + '<br>' + ' Amount(Rs.): ' + data.spare_amount[this.x];
                }
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        formatter: function() {return this.y + ' / (Rs. ' + data.spare_amount[this.x] + ')'; },
                        enabled: true,
                        crop: false,
                        overflow: 'none'
                        }
                    }
            },
            series: [{
                type: 'column',
                name: 'Count',
                data: spare_count
            }]
        });
    
    }
</script>