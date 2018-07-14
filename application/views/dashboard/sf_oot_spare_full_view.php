<div class="right_col" role="main">
    <!-- SF Brackets snapshot Section -->
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <div class="col-md-6">
                        <h2>Service Center Out Of Tat Spare <small> <?php if($partner_id) { echo $partner_name;}?></small></h2>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-12">
                        <center><img id="loader_gif2" src="<?php echo base_url(); ?>images/loadring.gif"></center>
                    </div>
                    <div class="x_content">
                        <div id="spare_details_by_sf" style="width:100%; display: none;" >
                            <table id="spare_details_by_sf_table" class="table table-bordered table-responsive" width="100%">
                                <thead>
                                <th>S.No.</th>
                                <th>Service Center</th>
                                <th>Spare Count</th>
                                </thead>
                                <tbody id="spare_details_by_sf_table_data"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SF Brackets Snapshot Section -->
    
    <!-- Modal -->
    <div id="modalDiv" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="open_model"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>

<script>
    var time = moment().format('D-MMM-YYYY');
    var post_request = 'POST';
    var get_request = 'GET';
    var url = '';
    var partner_id = '<?php echo $partner_id; ?>';
    var partner_name = '<?php echo $partner_name; ?>';
    
    $(document).ready(function () {
        
        //sf spare status
        get_partner_spare_details_by_sf();
        
    });
    
    //this function is used to call ajax request
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    
    //this function is used to get the spare details for sf
    function get_partner_spare_details_by_sf(){
        url =  '<?php echo base_url(); ?>employee/dashboard/get_spare_details_by_sf';
        data = {is_show_all:1,partner_id:partner_id};
        sendAjaxRequest(data,url,post_request).done(function(response){
            create_spare_parts_by_sf_table(response);
        });
    }
    
    function create_spare_parts_by_sf_table(response){
        obj = JSON.parse(response);
        $('#loader_gif2').hide();
        $('#spare_details_by_sf').fadeIn();
        var table_body_html = '';
        $.each(obj, function (index,val) {
            table_body_html += '<tr>';
            table_body_html += '<td>' + (Number(index)+1) +'</td>';
            table_body_html += '<td>' +val['name'] +'</td>';
            table_body_html += "<td><a href='#' onclick = show_dashboard_modal('"+val['booking_id']+"') >" +val['oot_defective_parts_count'] +"<a/></td>";
            table_body_html += '</tr>';
        });
        $('#spare_details_by_sf_table_data').html(table_body_html);
        $('#spare_details_by_sf_table').DataTable({
            dom: 'Bfrtip',
            pageLength: 50,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    title: 'sf_out_of_tat_spare_details' + time
                }
            ]
        });
    }
    
    function show_dashboard_modal(modal_data){
        var modal_body = modal_data.split(',');
        var html = "<table class='table table-bordered table-hover table-responsive'><thead><th>Booking Id</th></thead><tbody>";
        $(modal_body).each(function(index,value){
            html += "<tr><td>";
            html += "<a href='/employee/user/finduser?search_value="+value+"' target='_blank'>"+value+"</a>";
            html += "</td></tr>";
        });
        html += "</tbody></table>";
        $('#open_model').html(html);
        $('#modalDiv').modal('show'); 
    }
</script>