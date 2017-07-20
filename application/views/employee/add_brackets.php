<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center>Add Brackets</center></div>
        <div class="panel-body">

            <table class="table">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Order Received From</th>
                        <th>Order Given To</th>
<!--                        <th>19 to 24 inch</th>-->
                        <th>Less Than 32"</th>
                        <th>32" & Above </th>
<!--                        <th>&gt;43 inch</th>-->
                        <th>Total</th>
                        <!--<th>Order Given To</th>-->
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (validation_errors()) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . validation_errors() . '</strong>
                    </div>';
                    }
                    ?>
                    <?php
                    if ($this->session->userdata('brackets_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_success').'</strong>
                    </div>';
                    }
                    ?>
                    <?php
                    if ($this->session->userdata('brackets_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_error').'</strong>
                    </div>';
                    }
                    ?>
                <form name="myForm" class="form-horizontal" id ="brackets"  action='<?php echo base_url() ?>employee/inventory/process_add_brackets_form' method="POST" enctype="form-data">
                    <?php for ($i = 0; $i < 10; $i++) { ?>		
                        <tr>
                            <td>
                                <input type="checkbox" name='choice[]' value='<?php echo ($i) ?>' id="check_<?php echo $i ?>" onchange="return validate(this.id)"/>
                            </td>
                            <td>
                                <select name="order_received_from[]" class="order_received_from" id="order_received_from_<?php echo $i ?>" class = "form-control" disabled="">
                                    <option selected disabled hidden>Select Vendor</option>
                                    <?php foreach ($vendor as $value) { ?>
                                        <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?> </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="order_given_to[]" class="order_given_to" id="order_given_to_<?php echo $i ?>" class = "form-control" disabled="">
                                    <option selected disabled hidden>Select Vendor</option>
                                    <?php foreach ($vendor as $value) { ?>
                                        <option value="<?php echo $value['id'] ?>" <?php if($value['id'] == '10'){ echo 'selected';}?> ><?php echo $value['name'] ?> </option>
                                    <?php } ?>
                                </select>
                            </td>
<!--                            <td>
                                <input typt='text' name='_19_24[]' id ="_19_24_<?php echo $i ?>"   disabled="" class = "form-control" onchange="return add_value(this.id)"/>
                            </td>-->
                            <td>
                                <input typt='text' name='_26_32[]' id = "_26_32_<?php echo $i ?>" disabled="" class = "form-control" onchange="return add_value(this.id)"/>
                            </td>
                            <td>
                                <input typt='text' name='_36_42[]' id = "_36_42_<?php echo $i ?>" disabled="" class = "form-control" onchange="return add_value(this.id)"/>
                            </td>
<!--                            <td>
                                <input typt='text' name='_43[]' id = "_43__<?php echo $i ?>" disabled="" class = "form-control" onchange="return add_value(this.id)"/>
                            </td>-->
                            <td>
                                <input type='text' id = 'total_<?php echo $i ?>' name='total' value='0' disabled="" class = "form-control"/>
                            </td>
<!--                            <td style="width:30%;">
                                <select style="width: 100%;" name="order_given_to[]" class="order_given_to"  id="order_given_to_<?php echo $i ?>" class = "form-control">
                                    <option  disabled hidden>Select Vendor</option>
                                    <option value="10" selected>Manish Kapoor</option>
                                </select>
                            </td>-->
<!--                        <input type="hidden" name = "order_given_to[]" value="10"/>-->


                        </tr>
                    <?php } ?>
                    </tbody>
            </table>

            <center>
                <input type="submit" id="submitform" class="btn btn-info " value="Save"/>
            </center>
            </form>   
        </div>
    </div>
</div>
<?php 
$this->session->unset_userdata('brackets_success');
$this->session->unset_userdata('brackets_error');
?>
<script type="text/javascript">
    
    $(document).ready(function(){
       $(".order_received_from").select2(); 
       $(".order_given_to").select2();
    });


    function add_value(id) {
        var id = id.split("_");
//        var _19_24 = 0;
        var _26_32 = 0;
        var _36_42 = 0;
//        var _43 = 0;
        
        var numbers = /^[0-9]+$/;
//        if ($('#_19_24_' + id[3]).val() == '') {
//            _19_24 = 0;
//        } else {
//            if ($('#_19_24_' + id[3]).val().match(numbers)) {
//                _19_24 = parseInt($('#_19_24_' + id[3]).val());
//            } else {
//                alert('Please add number in 19 to 24 inch');
//            }
//
//        }
        if ($('#_26_32_' + id[3]).val() == '') {
            _26_32 = 0;
        } else {
            if ($('#_26_32_' + id[3]).val().match(numbers)) {
                _26_32 = parseInt($('#_26_32_' + id[3]).val());
            } else {
                alert('Please add number in 26 to 32 inch');
            }

        }
        if ($('#_36_42_' + id[3]).val() == '') {
            _36_42 = 0;
        } else {
            if ($('#_36_42_' + id[3]).val().match(numbers)) {
                _36_42 = parseInt($('#_36_42_' + id[3]).val());
            } else {
                alert('Please add number in 36 to 42 inch');
            }

        }
//        if ($('#_43__' + id[3]).val() == '') {
//            _43 = 0;
//        } else {
//            if ($('#_43__' + id[3]).val().match(numbers)) {
//                _43 = parseInt($('#_43__' + id[3]).val());
//            } else {
//                alert('Please add number in 43 inch');
//            }
//
//        }

        $('#total_' + id[3]).val( _26_32 + _36_42);
    }

    function validate(id) {
        var id = id.split("_")[1];
        if ($('#check_' + id).is(':checked')) {
//            $("#_19_24_" + id).attr('required', true);
//            $("#_19_24_" + id).attr('disabled', false);
            $("#order_received_from_" + id).attr('disabled', false);
            $("#order_given_to_" + id).attr('disabled', false);
            $("#_26_32_" + id).attr('required', true);
            $("#_26_32_" + id).attr('disabled', false);
            $("#_36_42_" + id).attr('required', true);
            $("#_36_42_" + id).attr('disabled', false);
//            $("#_43__" + id).attr('required', true);
//            $("#_43__" + id).attr('disabled', false);
            $("#order_received_from_" + id).attr('required', true);
            $("#order_given_to_" + id).attr('required', true);
        } else {
            $("#order_received_from_" + id).attr('disabled', true);
            $("#order_received_from_" + id).removeAttr('required');
            $("#order_given_to_" + id).attr('disabled', true);
            $("#order_given_to_" + id).removeAttr('required');
//            $("#_19_24_" + id).removeAttr('required');
//            $("#_19_24_" + id).attr('disabled', true);
            $("#_26_32_" + id).removeAttr('required');
            $("#_26_32_" + id).attr('disabled', true);
            $("#_36_42_" + id).removeAttr('required');
            $("#_36_42_" + id).attr('disabled', true);
//            $("#_43__" + id).removeAttr('required');
//            $("#_43__" + id).attr('disabled', true);
        }
    }

</script>
