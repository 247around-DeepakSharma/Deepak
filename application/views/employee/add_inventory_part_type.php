<style>
    #inventory_master_list_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #inventory_master_list_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
    .dataTables_filter{float: right;}
</style>
<?php
//echo '<pre>';
//        print_r($parts_type);
?>
<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center>Add Inventory Part Type</center></div>
        <div class="panel-body">

            <table class="table">
                <thead>
                    <tr>                 
                        <th>Appliances *</th>                       
                        <th>Inventory Part Type *</th>   
                        <th>HSN Code *</th>
                    </tr>
                </thead>
                <tbody>                 
                    <?php
                    if ($this->session->userdata('part_type_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close close_message" data-dismiss="alert" sms-type="part_type_success" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('part_type_success').'</strong>
                    </div>';
                         $this->session->unset_userdata('part_type_success');
                    }
                    ?>
                   <?php
                    if ($this->session->userdata('part_type_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close close_message" data-dismiss="alert" sms-type="part_type_error" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('part_type_error').'</strong>
                    </div>';
                        $this->session->unset_userdata('part_type_error');
                    }
                    ?>
                <form name="myForm" class="form-horizontal" id ="brackets" method="POST" action='<?php echo base_url() ?>employee/inventory/process_add_inventory_part_type_form'>
                        <tr>                    
                            <td>
                                <select class="form-control" id="service_id" name="service_id" required="">
                                    <option selected="" disabled="">Select Appliance</option>  
                                </select>
                            </td>                            
                            <td>
                                <input typt='text' name="part_type" id = "part_type" class="form-control" placeholder="Please Enter Part Type" style="text-transform: capitalize;" required=""/>
                            </td>
                            <td>
                                <select  id="hsn_code" name="hsn_code" required="">                                   
                                    <option selected="" disabled="">Select HSN Code</option>  
                                </select>
                            </td>
                            <td>
                                <input type="submit" id="submitform" class="btn btn-info " value="Add">
                            </td>
                            
                        </tr>
                       
                    </tbody>
            </table>           
            </form> 
                <br>
                <h4><strong>Inventory Part Type  List</strong></h4>
        </div>
        
   <table class="table priceList table-striped table-bordered" id="inventory_part_type_table">
   <thead>
      <tr>
         <th class="text-center">Id</th>
         <th class="text-center">Appliance</th>
         <th class="text-center"> Part Type </th>
         <th class="text-center"> HSN Code </th>         
         <th class="text-center">Action</th>
      </tr>
   </thead>
   <tbody>
       <?php $i = 1; foreach ($parts_type as $key => $val){ ?>
      <tr>
         <td style="text-align: center;"><?php echo $i; ?></td>
         <td style="text-align: center;" id="td_service_id_<?php echo $val['id']; ?>">                                            
            <?php echo $val['service_name']; ?>
         </td>
         <td style="text-align: center;" id="td_part_type_id_<?php echo $val['id']; ?>">                                            
           <?php echo $val['part_type'];  ?>
         </td>
         <td style="text-align: center;" id="td_hsncode_id_<?php echo $val['id']; ?>">                                            
           <?php echo $val['hsn_code']; ?>
         </td>
         <td style="text-align: center;">
             <a href="javascript:void(0)" class="btn btn-primary part_type"  data-parts-type='<?php echo json_encode($val); ?>' title="Edit Details"><i class="fa fa-edit"></i></a>
         </td>
      </tr>  
       <?php $i++; } ?>
   </tbody>
</table>
    </div>
</div>

<!-- Modal start-->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4>Edit Inventory Part Details</h4>
          <span id="update_status" style="font-size: 16px; font-weight: bold;"></span>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
            <form role="form" method="POST" id="inventory_part_type_modal_form" action='<?php echo base_url() ?>employee/inventory/process_edit_inventory_part_type_form'>
            <div class="form-group">
              <label for="usrname"> Appliances</label>
              <select class="form-control" id="service_id_modal" name="service_id" required=""> 
                  <option selected="" disabled="">Select Appliance</option>  
              </select>
              <span id="service_modal_err"></span>
            </div>
            <div class="form-group">
              <label for="psw"></span> Inventory Part Type</label>
              <input typt='text' name="part_type" id = "part_type_modal" class = "form-control" placeholder="Please Enter Part Type" style="text-transform: capitalize;"  required=""/>
              <span id="part_type_modal_err"></span>
            </div>
              <div class="form-group">
              <label for="psw"></span> HSN Code</label>
              <select  class="form-control" id="hsn_code_modal" name="hsn_code" required="">                                   
                  <option selected="" disabled="">Select HSN Code</option>  
              </select>
              <span id="hsn_code_modal_err"></span>
            </div>    
              <input type="hidden" name="part_type_id" id="part_type_id" value="">
              <button type="submit" class="btn btn-info" id="update_modal_form" style="margin-left: 200px;">Update</button>
          </form>
        </div>
        <div class="modal-footer">          
          <p><button class="btn btn-danger btn-default pull-right" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button></p>
        </div>
      </div>
    </div>
  </div> 
<!-- Modal End-->
    
<script type="text/javascript">
   $(document).ready(function(){
       get_services('service_id','');
       get_hsn_code('hsn_code','');       
 
    function get_services(service_div_id,service_id){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/booking/get_service_id',
            data:{is_option_selected:true},
            success:function(response){
                $('#'+service_div_id).html(response);
                $('#'+service_div_id).select2();
                if(service_id != ''){
                   $('#service_id_modal').val(service_id).change();
                }
            }
        });
    }
          
    function get_hsn_code(hsn_code_div_id,hsn_code_details_id){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url();?>employee/inventory/get_hsn_code_gst_details',            
            data:{is_option_selected:true},
            success:function(response){              
                $('#'+hsn_code_div_id).html(response);
                $('#'+hsn_code_div_id).select2();
                if(hsn_code_details_id != ''){                  
                   $('#hsn_code_modal').val(hsn_code_details_id).change();
                }
            }
        });
    }
    
    $(".part_type").on('click',function(){
        var parts_type_details = $(this).attr("data-parts-type");
        var part_type_obj = JSON.parse(parts_type_details);
        var part_type_id = part_type_obj['id'];
        var service_id = part_type_obj['service_id'];
        var part_type = part_type_obj['part_type'];
        var hsn_code_details_id = part_type_obj['hsn_code_details_id']; 
        $("#part_type_modal").val(part_type);
        $("#part_type_id").val(part_type_id);
        
        get_hsn_code('hsn_code_modal',hsn_code_details_id);    
        get_services('service_id_modal',service_id);         
         $("#myModal").modal();        
    });
    
   }); 
   
   $("#update_modal_form").click(function(){
         var service_id_modal = $("#service_id_modal").val();
         var part_type_modal = $("#part_type_modal").val();
         var hsn_code_modal = $("#hsn_code_modal").val();
         if(service_id_modal == '' || service_id_modal==null){
             $("#service_modal_err").html("Select Appliance").css('color','red');
             return false;
         }else{
             $("#service_modal_err").html("");
         }
         
       if(part_type_modal == ''){
             $("#part_type_modal_err").html("Enter Part Type").css('color','red');
             return false;
         }else{
             $("#part_type_modal_err").html("");
         } 
         
         if(hsn_code_modal == '' || hsn_code_modal==null){
             $("#hsn_code_modal_err").html("Select HSN Code").css('color','red');
             return false;
         }else{
             $("#hsn_code_modal_err").html("");
         } 
         
   });
   
   $(function() {
    
    $("#inventory_part_type_modal_form").submit(function(e) {        
        e.preventDefault();        
        var actionurl = e.currentTarget.action;
        $.ajax({
                url: actionurl,
                type: 'post',
                dataType: 'json',
                data: $("#inventory_part_type_modal_form").serialize(),
                success: function(data) {
                    if(data){
                        $("#update_status").html("Sucess").css('color','green');
                        $("#td_service_id_"+data['id']).text(data['service_name']);
                        $("#td_part_type_id_"+data['id']).text(data['part_type']);
                        $("#td_hsncode_id_"+data['id']).text(data['hsn_code']);
                        $("#myModal").hide();    
                        location.reload();
                    }else{
                       $("#update_status").html("Failed").css('color','red'); 
                    }
                }
        });

    });

});

$(document).ready(function() {
    $('#inventory_part_type_table').DataTable( {
        orderCellsTop: true,
        fixedHeader: true,
       
        pageLength: 50,
        dom: 'lBfrtip',
        lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
        buttons: [{
            extend: 'excel',
            text: '<span class="fa fa-file-excel-o"></span> Excel Export',
            pageSize: 'LEGAL',
            title: 'inventory_part_type',
            exportOptions: {
               columns: [1,2,3],
                modifier : {
                     // DataTables core
                     order : 'index',  // 'current', 'applied', 'index',  'original'
                     page : 'All',      // 'all',     'current'
                     search : 'none'     // 'none',    'applied', 'removed'
                 }
            }
        }]
    });
    
    $(".close_message").on('click',function(){
        var sms_type = $(this).attr("sms-type");
        
        if(sms_type =='part_type_success'){
            <?php $this->session->unset_userdata('part_type_success'); ?>
        }
        if(sms_type =='part_type_error'){
            <?php $this->session->unset_userdata('part_type_error'); ?>
        }
    });
} );
</script>

