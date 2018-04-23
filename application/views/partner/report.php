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
                    <h2>Download Reports</h2>
                    <div class="clearfix"></div>
                </div>
                    <h2 id="msg_holder" style="text-align:center;color: #108c30;font-weight: bold;"></h2>
                <div class="x_content">
                    <form>
                        <div class="form-group col-md-4"> 
                            <label class="control-label" for="daterange">Booking Create Date Range</label><br>
                            <?php
                            $endDate = date('Y-m-d');
                            $startDate = date('Y-m-d', strtotime("-".(date('d')-1)." days"));
                            $dateRange = $startDate." - ".$endDate;
                            ?>
                            <input style="border-radius: 5px;"  type="text" placeholder="Booking Create Date Range" class="form-control" id="create_date" value="<?php echo $dateRange ?>" name="create_date"/>
                        </div>
                        
                        <div class="form-group col-md-4">
                        <label for="Status">Status</label><br>
                                <select class="form-control" id="status" >
                               <option value="all">All</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                <div class="form-group col-md-4">
                        <label for="Status">States</label><br>
                        <select class="form-control" id="state" multiple="">
                                <?php
                                foreach($data as $state){
                                    ?>
                                <option value="<?php echo $state['state'] ?>"><?php echo $state['state'];  ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-success" onclick="get_report()" style="float:right; border: 1px solid #2a3f54;background: #2a3f54;">Get Report</button>
                                    </div>
                    </form>
               </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#status").select2();
    $("#state").select2();
    $('input[name="create_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear'
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
</script>