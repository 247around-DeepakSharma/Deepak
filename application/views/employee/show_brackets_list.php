<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 34px;
    }
   .custom-link-request {
        background-color: #FF8080;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 10px;
        height: 21px;
        width: 180px;
        cursor:pointer;
    }
    .custom-link-shipped{
        background-color: #FFEC8B;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 10px;
        height: 21px;
        width: 180px;
        cursor:pointer;
    }
    .custom-link-received{
        background-color: #4CBA90;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 10px;
        height: 21px;
        width: 180px;
        cursor:pointer;
    }
    
    
    
</style>
<?php $offset = $this->uri->segment(5); ?>
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
</script>
  
<div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Brackets List</center></div> 
</div>
<hr>
<div id="page-wrapper" >    
    <div class="panel-body">
                <div role="tabpanel"> 
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="tab_cat active" id="1-2-3-FF8080">
                                <a href="#requested_brackets_list" aria-controls="requested_brackets_list" id="current_1_2_3" data-bind="0-0" style="background-color: #ff8080;" role="tab" class="tab_link" data-toggle="tab">Requested Brackets List</a>                                
                            </li>
                            <li role="presentation" class="tab_cat" id="2-3-1-FFEC8B">
                                <a href="#shipped_brackets_list" aria-controls="shipped_brackets_list" id="current_2_3_1" data-bind="1-0" role="tab"class="tab_link" data-toggle="tab">Shipped Brackets List</a>
                            </li>
                            <li role="presentation" class="tab_cat" id="3-1-2-4CBA90">
                                <a href="#received_brackets_list" aria-controls="received_brackets_list" id="current_3_1_2" data-bind="1-1" role="tab" class="tab_link"  data-toggle="tab">Received Brackets List</a>
                            </li>
                        </ul>                        
                    </div>
                    
                </div>    
            </div>
       <hr>
    
    <div class="row">
        <div class="filter_brackets">
            <div class="filter_box">
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_role" name="sf_role">
                            <option selected disabled>Select Role</option>
                            <option value="order_received_from">Order Received From</option>
                            <option value="order_given_to">Order Given To</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_id" name="sf_id">
                            <option selected="" disabled="">Select Service Center</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                            <input type="text" class="form-control valid" id="daterange" name="daterange">
                    </div>
                    <div class="col-sm-3">
                        <div class="btn btn-success" id="filter" onclick="applyFilter()">Filter</div>
                    </div>
            </div>
        </div>
    </div>
    
       <div class="panel panel-info" style="margin-top:20px;">                    
        <div class="panel-body">
             <?php
                    if ($this->session->userdata('brackets_update_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:30px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_update_success').'</strong>
                    </div>';
                    }
                    ?>
             <?php
                    if ($this->session->userdata('brackets_cancelled_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:30px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_cancelled_error').'</strong>
                    </div>';
                    }
                    ?>
            <div id="loader"><img src="<?php echo base_url(); ?>images/loadring.gif" style="display:none;"></div>
            <div class="tab-content" id="tab-content">
                        <center style="margin-top:30px; display: none;"> <img style="width: 60px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
             </div>
        </div>
    </div>
  
</div>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
    $(function() {

      $('input[name="daterange"]').daterangepicker({
          autoUpdateInput: false,
          locale: {
              cancelLabel: 'Clear'
          }
      });

      $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      });

      $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });

    });

    $(document).ready(function(){
        $.ajax({
            method:'POST',
            url: "<?php echo base_url();?>employee/vendor/get_service_center_details",
            success:function(response){
                $('#sf_id').val('val', "");
                $('#sf_id').val('Select Service Center').change();
                $('#sf_id').select2().html(response);
            }
        });
    });
    
 
    $(".tab_cat").on('click',function(){
        var ids_strings = $(this).attr('id');
        ids_arr = ids_strings.split('-');          
        $("#current_"+ids_arr[0]+"_"+ids_arr[1]+"_"+ids_arr[2]).css('background','#'+ids_arr[3])
        $("#current_"+ids_arr[1]+"_"+ids_arr[2]+"_"+ids_arr[0]).css("background-color", ""); 
        $("#current_"+ids_arr[2]+"_"+ids_arr[0]+"_"+ids_arr[1]).css("background-color", ""); 
     });
       
result_list();

function result_list(){
    
    $.ajax({
            method:'POST',
            url: "<?php echo base_url();?>employee/inventory/show_brackets_list_on_tab",
            data: {},
            success:function(response){
                $("#tab-content").html(response);                
             }
       });

}

</script>

<?php if($this->session->userdata('brackets_update_success')){$this->session->unset_userdata('brackets_update_success');}?>
<?php if($this->session->userdata('brackets_cancelled_error')){$this->session->unset_userdata('brackets_cancelled_error');}?>
