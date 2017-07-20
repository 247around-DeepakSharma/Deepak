<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center><?php if(isset($shipped_flag)){echo '<b>Update Shipment</b>';}elseif(isset($receiving_flag)){echo "<b>Update Delivery</b>";}elseif(isset($requested_flag)){echo '<b>Update/Cancel Order</b>';}?> </center></div>
        <div class="panel-body">
            <?php if(isset($shipped_flag)){
                $form_submit_form = 'process_update_shipment_form';
            }
            if(isset($receiving_flag)){
                $form_submit_form = 'process_update_receiving_form';
            }
            if(isset($requested_flag)){
                $form_submit_form = 'process_update_requested_form';
            }
            ?>
        
                    <?php
                    if ($this->session->userdata('brackets_update_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_update_error').'</strong>
                    </div>';
                    }
                    ?>
            
            <div class="clear"></div>
            <div class="col-md-4 form-group">
                <label class="label label-default" style="font-size:100%;">Order ID</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo $order_id ?>"/>
            </div>
            <div class="col-md-4 form-group" >
                <label class="label label-default" style="font-size:100%;">Received From</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo isset($order_received_from)?$order_received_from:'' ?>"/>
            </div>
            <div class="col-md-4 form-group">
                <label class="label label-default" style="font-size:100%;">Given To</label>
                <div class="clear"></div>
                <input type="text" disabled="" class="form-control" value="<?php echo isset($order_given_to)?$order_given_to:'' ?>"/>
            </div>
            <div class="clear"></div>
            
            <form name="myForm" class="form-horizontal" action='<?php echo base_url() ?>employee/inventory/<?php echo $form_submit_form?>' method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_id" value="<?php echo $order_id?>" />
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th></th>
<!--                            <th>19 to 24 inch</th>-->
                            <th>Less Than 32 Inch</th>
                            <th>32 Inch & Above</th>
<!--                            <th>&gt; 43 inch</th>-->
                            <th>Total</th>
                            <th>Shipment Date</th>
                            <th>File Uploads*</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($brackets as $key => $value) { ?>		
                                <input type="hidden" name="order_received_from" value="<?php echo $value['order_received_from']?>" />
                                <input type="hidden" name="order_given_to" value="<?php echo $value['order_given_to']?>" />
                            <tr>
                                <td><b>Requested Brackets Details</b></td>
<!--                                <td>
                                    <input type='text' name='19_24_requested' id ="19_24_requested" value="<?php //echo $value['19_24_requested'] ?>" class = "form-control" onchange="return add_requested_value()" <?php echo isset($requested_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='26_32_requested' id ="26_32_requested" value="<?php echo ($value['19_24_requested']+$value['26_32_requested']); ?>" class = "form-control" onchange="return add_requested_value()" <?php echo isset($requested_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                                <td>
                                    <input type='text' name='36_42_requested' id ="36_42_requested" value="<?php echo ($value['36_42_requested']+$value['43_requested']); ?>" class = "form-control" onchange="return add_requested_value()" <?php echo isset($requested_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
<!--                                <td>
                                    <input type='text' name='43_requested' id ="43_requested" value="<?php //echo $value['43_requested'] ?>" class = "form-control" onchange="return add_requested_value()" <?php echo isset($requested_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='total_requested' id ="total_requested" value="<?php echo $value['total_requested'] ?>" class = "form-control" onchange="return add_requested_value()" <?php echo isset($requested_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Shipped Brackets Details</b></td>
<!--                                <td>
                                    <input type='text' name='19_24_shipped' id ="19_24_shipped" value="<?php //echo $value['19_24_shipped'] ?>" class = "form-control" onchange="return add_value()" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='26_32_shipped' id ="26_32_shipped" value="<?php echo ($value['19_24_shipped']+$value['26_32_shipped']); ?>" class = "form-control" onchange="return add_value()" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                                <td>
                                    <input type='text' name='36_42_shipped' id ="36_42_shipped" value="<?php echo ($value['36_42_shipped']+$value['43_shipped']); ?>" class = "form-control" onchange="return add_value()" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
<!--                                <td>
                                    <input type='text' name='43_shipped' id ="43_shipped" value="<?php //echo $value['43_shipped'] ?>" class = "form-control" onchange="return add_value()" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='total_shipped' id ="total_shipped" value="<?php echo $value['total_shipped'] ?>" class = "form-control" onchange="return add_value()" readonly="" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                                <td>
                                    <div class="input-group input-append date">
                                        <input id="shipment_date" class="form-control"  name="shipment_date"  value = "<?php if($value['shipment_date'] > 0){ echo  date("Y-m-d", strtotime($value['shipment_date'])); } ?>"   <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>>
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </td>
                                <td>
                                    <input type='file' name='shipment_receipt' id="shipment_receipt" class = "form-control" <?php echo isset($shipped_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Received Brackets Details</b></td>
<!--                                <td>
                                    <input type='text' name='19_24_received' id ="19_24_received" value="<?php //echo $value['19_24_received'] ?>" class = "form-control" onchange="return add_received_value()" <?php echo isset($receiving_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='26_32_received' id ="26_32_received" value="<?php echo ($value['19_24_received']+$value['26_32_received']); ?>" class = "form-control" onchange="return add_received_value()" <?php echo isset($receiving_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
                                <td>
                                    <input type='text' name='36_42_received' id ="36_42_received" value="<?php echo ($value['36_42_received']+$value['43_received']); ?>" class = "form-control" onchange="return add_received_value()" <?php echo isset($receiving_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>
<!--                                <td>
                                    <input type='text' name='43_received' id ="43_received" value="<?php //echo $value['43_received'] ?>" class = "form-control" onchange="return add_received_value()" <?php echo isset($receiving_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>-->
                                <td>
                                    <input type='text' name='total_received' id ="total_received" value="<?php echo $value['total_received'] ?>" class = "form-control" readonly="" <?php echo isset($receiving_flag) ? '' : 'disabled="true"'; ?>/>
                                </td>


                            </tr>
                        <?php } ?>


                    </tbody>
                </table>
                <center>
                    <input type="submit" id="submitform" class="btn btn-info " onclick="return confirm_submit('Confirm?')" value="Update"/>
                    <span class="btn btn-danger " <?php echo isset($requested_flag)?'':'style="display:none"'?>  data-toggle="modal" data-target="#cancelmodal">Cancel Order</span>
                </center>
            </form>
        </div>
    </div>
</div>

<div id="cancelmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <form name="cancellation_form" id="cancellation_form" class="form-horizontal" action="<?php echo base_url() ?>employee/inventory/cancel_brackets_requested" method="POST">
          
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-align: center"><b>Cancel Reason</b></h4>
          </div>
          <div class="modal-body">
              <span id="error_message" style="display:none;color: red"><b>Please enter reason</b></span>
              <textarea name="cancellation_reason" rows="4" cols="40" id="cancellation_reason" placeholder="Enter reason for Cancel"></textarea>
              <input type="hidden" name="order_id" value="<?php echo $order_id?>" >
          </div>
          <div class="modal-footer">
             <input type="button" onclick="form_submit()" value="Submit" class="btn btn-info " form="modal-form">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </div>
          
      </form>
  </div>
</div>



<?php 
$this->session->unset_userdata('brackets_update_error');
?>
<script type="text/javascript">
    
    function form_submit() {
        if($('#cancellation_reason').val() == ""){
            $('#error_message').css('display','block');
            return false;
        }else{
            $("#cancellation_form").submit();
        }
    }    
    
    $("#shipment_date").datepicker({dateFormat: 'yy-mm-dd'});
    
    function add_value() {
        //For validating entered values
        validate_shipped();
//        var _19_24 = 0;
        var _26_32 = 0;
        var _36_42 = 0;
//        var _43 = 0;
        var numbers = /^[0-9]+$/;
        
        $('#total_shipped').val('0');
//        if ($('#19_24_shipped').val() == '') {
//            _19_24 = 0;
//        } else {
//            if ($('#19_24_shipped').val().match(numbers)) {
//                _19_24 = parseInt($('#19_24_shipped').val());
//            } else {
//                alert('Please add number in 19 to 24 inch');
//            }
//
//        }
        if ($('#26_32_shipped').val() == '') {
            _26_32 = 0;
        } else {
            if ($('#26_32_shipped').val().match(numbers)) {
                _26_32 = parseInt($('#26_32_shipped').val());
            } else {
                alert('Please add numbers Only');
            }

        }
        if ($('#36_42_shipped').val() == '') {
            _36_42 = 0;
        } else {
            if ($('#36_42_shipped').val().match(numbers)) {
                _36_42 = parseInt($('#36_42_shipped').val());
            } else {
                alert('Please add numbers Only');
            }

        }
//        if ($('#43_shipped').val() == '') {
//            _43 = 0;
//        } else {
//            if ($('#43_shipped').val().match(numbers)) {
//                _43 = parseInt($('#43_shipped').val());
//            } else {
//                alert('Please add number in Greater than 43 inch');
//            }
//
//        }

        $('#total_shipped').val(_26_32 + _36_42);
    }
    
    function add_received_value(){
        //For validating entered values
        validate_received();
        
//        var _19_24_received = 0;
        var _26_32_received = 0;
        var _36_42_received = 0;
//        var _43_received = 0;
        var numbers = /^[0-9]+$/;
        $('#total_received').val('0');
//        if ($('#19_24_received').val() == '') {
//            _19_24_received = 0;
//        } else {
//            if ($('#19_24_received').val().match(numbers)) {
//                _19_24_received = parseInt($('#19_24_received').val());
//            } else {
//                alert('Please add number in 19 to 24 inch');
//            }
//
//        }
        if ($('#26_32_received').val() == '') {
            _26_32_received = 0;
        } else {
            if ($('#26_32_received').val().match(numbers)) {
                _26_32_received = parseInt($('#26_32_received').val());
            } else {
                alert('Please add numbers Only');
            }

        }
        if ($('#36_42_received').val() == '') {
            _36_42_received = 0;
        } else {
            if ($('#36_42_received').val().match(numbers)) {
                _36_42_received = parseInt($('#36_42_received').val());
            } else {
                alert('Please add numbers Only');
            }

        }
        
//        if ($('#43_received').val() == '') {
//            _43_received = 0;
//        } else {
//            if ($('#43_received').val().match(numbers)) {
//                _43_received = parseInt($('#43_received').val());
//            } else {
//                alert('Please add number in Greater than 43 inch');
//            }
//
//        }

        $('#total_received').val(_26_32_received + _36_42_received);
    }
    
    function validate_shipped(){
//        if(parseInt($('#19_24_shipped').val()) > parseInt($('#19_24_requested').val())){
//            alert('19 to 24 inch Shipped quantity must not be greater than Requested quantity');
//            return false;
//        }
        if(parseInt($('#36_42_shipped').val()) > parseInt($('#36_42_requested').val())){
            alert('Shipped quantity must not be greater than Requested quantity');
            return false;
        }
        if(parseInt($('#26_32_shipped').val()) > parseInt($('#26_32_requested').val())){
            alert('Shipped quantity must not be greater than Requested quantity');
            return false;
        }
//        if(parseInt($('#43_shipped').val()) > parseInt($('#43_requested').val())){
//            alert('Greater than 43 inch Shipped quantity must not be greater than Requested quantity');
//            return false;
//        }
        
    }
    
    function validate_received(){
//        if(parseInt($('#19_24_received').val()) > parseInt($('#19_24_shipped').val())){
//            alert('19 to 24 inch Received quantity must not be greater than Shipped quantity');
//            return false;
//        }
        if(parseInt($('#36_42_received').val()) > parseInt($('#36_42_shipped').val())){
            alert('Received quantity must not be greater than Shipped quantity');
            return false;
        }
        if(parseInt($('#26_32_received').val()) > parseInt($('#26_32_shipped').val())){
            alert('Received quantity must not be greater than Shipped quantity');
            return false;
        }
//        if(parseInt($('#43_received').val()) > parseInt($('#43_shipped').val())){
//            alert('Greater than 43 inch Shipped quantity must not be greater than Requested quantity');
//            return false;
//        }
    }
    
    //This function is used to confirm before from submission
    function confirm_submit(reason){
        var shipped = '<?php echo isset($shipped_flag)?$shipped_flag:false;?>';
        var received = '<?php echo isset($receiving_flag)?$receiving_flag:false;?>';
        //validation for shipped before form submit
        if(shipped){
            if(typeof(validate_shipped()) == "undefined"){
                var c=confirm(reason);
                    if(!c){
                        return false;
                    }
            }else{
                return false;
            }
            
        }
        //validation for received before form submit
        if(received){
            if(typeof(validate_received()) == "undefined"){
                var c=confirm(reason);
                    if(!c){
                        return false;
                    }
            }else{
                return false;
            }
            
        }
        
    }
    
    //This function is used to check for inputs as number and add value in total
    function add_requested_value(){
//        var _19_24 = 0;
        var _26_32 = 0;
        var _36_42 = 0;
//        var _43 =0;
        var numbers = /^[0-9]+$/;
        
        $('#total_requested').val('0');
//        if ($('#19_24_requested').val() == '') {
//            _19_24 = 0;
//        } else {
//            if ($('#19_24_requested').val().match(numbers)) {
//                _19_24 = parseInt($('#19_24_requested').val());
//            } else {
//                alert('Please add number in 19 to 24 inch');
//            }
//
//        }
        if ($('#26_32_requested').val() == '') {
            _26_32 = 0;
        } else {
            if ($('#26_32_requested').val().match(numbers)) {
                _26_32 = parseInt($('#26_32_requested').val());
            } else {
                alert('Please add numbers Only');
            }

        }
        if ($('#36_42_requested').val() == '') {
            _36_42 = 0;
        } else {
            if ($('#36_42_requested').val().match(numbers)) {
                _36_42 = parseInt($('#36_42_requested').val());
            } else {
                alert('Please add numbers Only');
            }

        }
        
//        if ($('#43_requested').val() == '') {
//            _43 = 0;
//        } else {
//            if ($('#43_requested').val().match(numbers)) {
//                _43 = parseInt($('#43_requested').val());
//            } else {
//                alert('Please add number in Greater than 43 inch');
//            }
//
//        }

        $('#total_requested').val(_26_32 + _36_42);
    }
    


</script>