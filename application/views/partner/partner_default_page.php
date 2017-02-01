<script type="text/javascript">
    var spare_parts = '<?php echo $spare_parts?>';
    $(function () {
        $("#tabs").tabs();
        
        if(parseInt(spare_parts) > 0 ){
            $("#tabs").tabs("option", "active", 1);
            //Loading Pending Spare Parts if Spare Parts Present
            load_view('employee/partner/get_spare_parts_booking', 'tabs-2');
        }else{
            //Loading Pending Bookings in Else case
            load_view('employee/partner/pending_booking/0/1', 'tabs-1');
        }

    });
</script>
<style type="text/css">
    .ui-tabs .ui-tabs-nav {
        margin: -7px !important;
        padding: 0.2em 0.2em 0.2em !important;
    }
    .ui-widget-header{
        border:0px !important;
        background:none !important;
    }
    .ui-tabs-nav.ui-helper-reset.ui-helper-clearfix.ui-widget-header.ui-corner-all{
        margin-bottom:-36px !important;
    }
</style>

<div class="container-fluid">
    <div class="row" style="margin-top: 10px;">
        <div class="col-md-12">
            <?php if($this->session->flashdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>' . $this->session->flashdata('error') . '</strong>
                </div>';
                }
                ?>
            <div class="panel panel-default">
                <div id="tabs"> 
                    <ul>
                        <li><a href="#tabs-1" onclick="load_view('employee/partner/pending_booking/0/1', 'tabs-1')"><span class="panel-title">Pending Bookings</span></a></li>
                        <li><a href="#tabs-2" onclick="load_view('employee/partner/get_spare_parts_booking', 'tabs-2')"><span class="panel-title">Pending Spares</span></a></li>
                        <li><a href="#tabs-3" onclick="load_view('employee/partner/get_waiting_defective_parts', 'tabs-3')"><span class="panel-title">Shipped Spares by SF</span></a></li>
                    </ul>

                    <div class="panel-body">
                        <style type="text/css">

                            .ui-widget-content a{
                                color:#ffffff ;
                            }
                        </style>
                        <div id="tabs-1" style="margin-left:-2%;font-size:90%"><div id="#loading-image"><img src="<?php echo base_url() ?>images/loader.gif" style="margin-left:1%;height:150px;"></div></div>
                        <div id="tabs-2" style="margin-left:-2%"><div id="#loading-image"><img src="<?php echo base_url() ?>images/loader.gif" style="margin-left:1%;height:150px;"></div></div>
                        <div id="tabs-3" style="margin-left:-2%"><div id="#loading-image"><img src="<?php echo base_url() ?>images/loader.gif" style="margin-left:1%;height:150px;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Escalate Form</h4>
            </div>
            <div class="modal-body">
                 <center><h4 id ="failed_validation" style="color:red;margin-top: 0px;margin-bottom: 35px;"></h4></center>
                <div class="container">
                   
                    <!--<div class="row">-->
                        <div class="col-md-8">
                            <form class="form-horizontal" action="#" method="POST" target="_blank" >
                            <div class="form-group">
                                <label for="Booking Id" class="col-md-2">Booking Id</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control"  name="booking_id" id="ec_booking_id" placeholder = "Booking Id"  readonly>
                                </div>
                            </div>
                            <div class="form-group  <?php if( form_error('escalation_reason_id') ) { echo 'has-error';} ?>">
                                <label for="Service" class="col-md-2">Reason</label>
                                <div class="col-md-6">
                                    <select class=" form-control" name ="escalation_reason_id" id="escalation_reason_id">
                                        <option selected disabled>----------- Select Reason ------------</option>
                                        <?php 
                                            foreach ($escalation_reason as $reason) {     
                                            ?>
                                        <option value = "<?php echo $reason['id']?>">
                                            <?php echo $reason['escalation_reason'];?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <?php echo form_error('escalation_reason_id'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Escalation Remarks" class="col-md-2">Remarks</label>
                                <div class="col-md-6">
                                    <textarea  class="form-control" id="es_remarks" name="escalation_remarks" placeholder = "Remarks" ></textarea>
                                </div>
                            </div>
                            <div class="form-group ">
                                <input type= "submit"  onclick="return form_submit()" class=" col-md-3 col-md-offset-4 btn btn-primary btn-lg" value ="Save" style="background-color:#2C9D9C; border-color:#2C9D9C;">
                            </div>
                        </div>
                    </div>
                <!--</div>-->
            </div>
        </div>
    </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
<script>
    $(document).on("click", ".open-AddBookDialog", function () {
        var myBookId = $(this).data('id');
        $(".modal-body #ec_booking_id").val( myBookId );
        
    });
    
    window.onload = function(){
     //Adding Validation   
        $("#selectall_address").change(function(){
            var d_m = $('input[name="download_courier_manifest[]"]:checked');
            if(d_m.length > 0){
                $('.checkbox_manifest').prop('checked', false); 
                $('#selectall_manifest').prop('checked', false); 
            }
        $(".checkbox_address").prop('checked', $(this).prop("checked"));
        });
    
        $("#selectall_manifest").change(function(){
            var d_m = $('input[name="download_address[]"]:checked');
            if(d_m.length > 0){
                $('.checkbox_address').prop('checked', false); 
                $('#selectall_address').prop('checked', false); 
            }
        $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
        }); 
    }
    
    function load_view(url, tab) {
        //Enabling loader
        $('#loading-image').show();
        //Loading view with Ajax data
        $.ajax({
            type: "GET",
            url: "<?php echo base_url() ?>" + url,
            success: function (data) {
                data_to_append = $(data).find('div.row');
                $('div.row').css('margin-top','-40px !important');
                $('#' + tab).html(data_to_append);
                
                if(tab === 'tabs-2'){
                    //Adding Validation   
                    $("#selectall_address").change(function(){
                        var d_m = $('input[name="download_courier_manifest[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_manifest').prop('checked', false); 
                            $('#selectall_manifest').prop('checked', false); 
                        }
                    $(".checkbox_address").prop('checked', $(this).prop("checked"));
                    });
    
                    $("#selectall_manifest").change(function(){
                        var d_m = $('input[name="download_address[]"]:checked');
                        if(d_m.length > 0){
                            $('.checkbox_address').prop('checked', false); 
                            $('#selectall_address').prop('checked', false); 
                        }
                    $(".checkbox_manifest").prop('checked', $(this).prop("checked"));
                    }); 
                }
                console.log('Data appended to Tab - ' + tab);
            },
            complete: function () {
                $('#loading-image').hide();
            }
        });
    }
    
    function form_submit(){
        var escalation_id = $("#escalation_reason_id").val();
        var booking_id = $("#ec_booking_id").val();
        var remarks = $("#es_remarks").val();
        
        if(escalation_id ===  null){
            $("#failed_validation").text("Please Select Any Escalation Reason");
           
        }  else {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>partner/process_escalation/'+booking_id,
                data: {escalation_reason_id: escalation_id,escalation_remarks:remarks},
                success: function (data) {
                  console.log(data);

                }
              });
            
        }
        $('#myModal').modal('toggle');
        return false;
    }
    
  function check_checkbox(number){
      
      if(number === 1){
        var d_m = $('input[name="download_courier_manifest[]"]:checked');
        if(d_m.length > 0){
            $('.checkbox_manifest').prop('checked', false); 
            $('#selectall_manifest').prop('checked', false); 
        }
          
      } else if(number === 0){
         var d_m = $('input[name="download_address[]"]:checked');
        if(d_m.length > 0){
             $('.checkbox_address').prop('checked', false); 
             $('#selectall_address').prop('checked', false); 
         }
      }
      
  }
  
  function confirm_received(){
    var c = confirm("Continue?");
    if(!c){
        return false;
    }
}

</script>