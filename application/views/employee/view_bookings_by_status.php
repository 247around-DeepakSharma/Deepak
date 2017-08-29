<style>
    #datatable1_filter{
        display: none;
    }
    .col-md-3 {
        width: 22%;
    }
</style>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<div id="page-wrapper" >
    <div class="row">
        <h1> <?php echo ucfirst($booking_status);?> Bookings</h1>
        <hr>
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
        }
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error') . '</strong>
               </div>';
        }
        if ($this->session->userdata('failed')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('failed') . '</strong>
               </div>';
        }
        ?> 
        <div class="table_filter">
            <div class="row">
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="partner_id">
                                <option value="" selected="selected" disabled="">Select Partner</option>
                                <?php foreach($partners as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="sf_id">
                                <option value="" selected="selected" disabled="">Select Service Center</option>
                                <?php foreach($sf as $val){ ?>
                                <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="ratings">
                                <option value="" selected="selected" disabled="">Select Rating Status</option>
                                <option value="a">Booking With Ratings</option>
                                <option value="b">Booking Without Ratings</option>
                                <option value="c">All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" id="closed_date">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="bookings_table">
            <table id="datatable1" class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Booking Id</th>
                        <th>User Name / Phone Number</th>
                        <th>Service Name</th>
                        <th>Service Centre</th>
                        <th>Service Centre City</th>
                        <th>Completion Date</th>
                        <th>Call</th>
                        <?php if($booking_status === _247AROUND_COMPLETED) { ?> 
                        <th>Edit</th>
                        <th>Cancel</th>
                        <?php } else if ($booking_status === _247AROUND_CANCELLED) { ?> 
                        <th>Complete</th>
                        <?php } ?>
                        <th>Open</th>
                        <th>View</th>
                        <?php if($booking_status === _247AROUND_COMPLETED){ ?> 
                        <th>Rate</th>    
                        <?php } ?>
                        <th>Penalty</th>
                    </tr>
                    <tbody></tbody>
                </thead>
            </table>
        </div>
    </div>
    
    <!-- start upcountry model -->
    <div id="myModal3" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" id="open_model1">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upcountry Call</h4>
                </div>
                <div class="modal-body" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end upcountry model -->
    
    <!--Cancel Modal-->
    <div id="penaltycancelmodal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg" >
          <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/vendor/process_remove_penalty" method="POST">

            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" style="text-align: center"><b>Remove Penalty</b></h4>
              </div>
              <div class="modal-body">
                  <span id="error_message" style="display:none;color: red;margin-bottom:10px;"><b>Please Select At Least 1 Booking</b></span>
                  <div id="open_model"></div>
              </div>
              <div class="modal-footer">
                 <input type="button" onclick="form_submit()" value="Submit" class="btn btn-info " form="modal-form">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>

          </form>
      </div>
    </div>
    <!-- end cancel model -->
    
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    
    var booking_status = '<?php echo $booking_status;?>';
    var booking_id = '<?php echo $booking_id;?>';
    var datatable1;
    
    $('#partner_id').select2();
    $('#sf_id').select2();
    $(document).ready(function(){
        
        
        $('#closed_date').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
        $('#closed_date').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            datatable1.ajax.reload();
        });
        
        $('#closed_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            datatable1.ajax.reload();
        });
        
        
        datatable1 = $('#datatable1').DataTable({
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/booking/get_bookings_by_status/"+booking_status,
                "type": "POST",
                "data": function(d){
                    d.booking_status =  booking_status;
                    d.booking_id =  '<?php echo $booking_id;?>';
                    d.partner_id =  $('#partner_id').val();
                    d.sf_id =  $('#sf_id').val();
                    d.booking_date_range =  $('#closed_date').val();
                    d.ratings =  $('#ratings').val();
                 }
            },
            "fnInitComplete": function (oSettings, response) {
               $('input[type="search"]').attr("name", "search_value");           
            }        
        });
        
        $('.filter_table').on('change', function(){
            datatable1.ajax.reload();
        });
        
    });

</script>
<script>
    $(function(){

      $('#dynamic_select').bind('change', function () {
          var url = $(this).val();
          if (url) {
              window.location = url;
          }
          return false;
      });
    });

    function outbound_call(phone_number){
        var confirm_call = confirm("Call Customer ?");
       
        if (confirm_call == true) {
            
             $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/call_customer/' + phone_number,
                success: function(response) {
                    //console.log(response);
                }
            });
        } else {
            return false;
        }

    }
    
    function open_upcountry_model(sc_id, booking_id, amount_due, sf_rate){
     
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/booking/booking_upcountry_details/'+sc_id+"/" + booking_id+"/"+amount_due,
          success: function (data) {
              console.log(data);
           $("#open_model1").html(data);   
           $('#myModal3').modal('toggle');
          }
        });
    }
    
    function form_submit() {
        
        var checkbox_val = [];
        $(':checkbox:checked').each(function(i){
          checkbox_val[i] = $(this).val();
        });
        if(checkbox_val.length === 0){
            $('#error_message').css('display','block');
            return false;
        }else{
            $("#cancellation_form").submit();
        }
    }  
    
    function get_penalty_details(booking_id,status){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_penalty_details_data/' + booking_id+"/"+status,
            success: function (data) {
             $("#open_model").html(data);   
             $('#penaltycancelmodal').modal('toggle');

            }
          });
    }


</script>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
<?php $this->session->unset_userdata('failed'); ?>