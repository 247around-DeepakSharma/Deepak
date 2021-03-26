<?php
	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");

?>
<style>
    .count{
        font-size: 16px;
        font-weight: bold;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title" style="background: #2a3f54;">
                    <h2 style="color:#fff;padding-left:10px;padding-top:7px;">Payment</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="payment_details_div">
                        
                        <div class="current_details">
                            <div class="row tile_count">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <span class="count_top" style="font-size:17px;"><b>Account Balance :</b>
                                    <span class="count" id="acnt_bal"><i class="fa fa-spinner fa-spin"></i></span></span>
                                </div>
<!--                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <span class="count_top">Valid till</span>
                                    <div class="count" id="aggrement_end_date"><i class="fa fa-spinner fa-spin"></i></div>
                                </div>-->
                            </div>
                        </div>
                        <hr>
                        <form method="post" action="<?php echo base_url();?>payment/checkout" id="payment_form">
                            
                            <h2 style="font-size:17px;">Select Amount</h2>
                            
                            <div class="amount_details">
                                <div class ="col-md-4 col-sm-6 col-xs-12">
                                    <div class="radio">
                                        <label><input type="radio" class="initial_amount" name="amount" value="2500" required="" checked=""><i class="fa fa-inr"></i> 2500</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" class="initial_amount" name="amount" value="5000" required=""><i class="fa fa-inr"></i> 5000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" class="initial_amount" name="amount" value="10000" required=""><i class="fa fa-inr"></i> 10000</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" class="initial_amount" name="amount" value="15000" required=""><i class="fa fa-inr"></i> 15000</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" id="other_amount" value="other" name="amount" required="">Other</label>
                                    </div>
                                    <div class="form-group" style="display:none;" id="other_amount_div">
                                        <input type="number" min="2500" id="other_amount_value">
                                    </div>
                                </div>
                                <div class ="col-md-4 col-sm-6 col-xs-12">
                                    <div class="radio">
                                        <label><input type="radio" class="initial_amount" name="amount" value="20000" required=""><i class="fa fa-inr"></i> 20000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" class="initial_amount" name="amount" value="25000" required=""><i class="fa fa-inr"></i> 25000</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" class="initial_amount" name="amount" value="30000" required=""><i class="fa fa-inr"></i> 30000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" class="initial_amount" name="amount" value="50000" required=""><i class="fa fa-inr"></i> 50000</label>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-md-6 col-sm-12 col-xs-12 payment_summary">
                                <h2 style="font-size:17px;">Payment Summary</h2>
                                <table class="table">
                                    <tr>
                                        <th>Central GST </th>
                                        <td><i class="fa fa-inr"></i> <span id="cgst"> 0</span></td>
                                    </tr>
                                    <tr>
                                        <th>State GST</th>
                                        <td><i class="fa fa-inr"></i> <span id="sgst"> 0</span></td>
                                    </tr>
                                    <tr>
                                        <th>Integrated GST</th>
                                        <td><i class="fa fa-inr"></i> <span id="igst"> 0</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-5 col-sm-12 col-xs-12">
                                    <input type="hidden" name="TDS_RATE" id="apply_tds_percent" value="">
                                    <input type="hidden" name="TDS_AMOUNT" id="apply_tds_amount" value="">
                                    <table class="table" width="50%">
                                        <tr>
                                            <td width="15%" style="font-size:17px;">Deduct TDS at?</td>
                                            <td width="8%">
                                                <input type="radio" name="tds" onClick="deduct_tds(0);">&nbsp;None
                                            </td>
                                            <td width="8%">
                                                <?php $tds_rate1 = 0.75; if(date('Y-m-d') < '2021-04-01'){ $tds_rate1 = 1;}?>
                                                <input class="tds" type="radio" name="tds" onClick="deduct_tds(<?php echo $tds_rate1;?>);">&nbsp;<?php echo $tds_rate1." %";?>
                                            </td>
                                            <td width="8%">
                                                <?php $tds_rate2 = 1.5; if(date('Y-m-d') < '2021-04-01'){ $tds_rate2 = 2;}?>
                                                <input class="tds" type="radio" name="tds" onClick="deduct_tds(<?php echo $tds_rate2;?>);">&nbsp;<?php echo $tds_rate2." %";?>
                                            </td>
<!--                                            <td width="8%">
                                                <input class="tds" type="radio" name="tds" onClick="deduct_tds(3.75);">&nbsp;3.75%
                                            </td>
                                            <td width="8%">
                                                <input class="tds" type="radio" name="tds" onClick="deduct_tds(7.5);">&nbsp;7.5%
                                            </td>-->
                                        </tr>
                                    </table>
                                 </div>
                            </div>
                            <div class="tds_deduction_amount">
                                <hr>
                                <h4 style="font-size:17px;"> TDS Deduction Amount : 
                                    <i class="fa fa-inr"></i>
                                    <span id="tds_deduction_amount"></span>
                                </h4>
                            </div>                            
                            <hr>
                            <div class="final_amount">
                                <h4 style="font-size:17px;"> Total Amount To be Paid : 
                                    <i class="fa fa-inr"></i>
                                    <span id="final_amount"></span>
                                </h4>
                            </div>
                            <hr>
                            <?php if($this->session->userdata('partner_id')) {
                                $order_id = $this->session->userdata('partner_id')."_".date('Ymdhis');
                                $cust_id = $this->session->userdata('partner_id');
                                $order_details = "";
                            }else{ 
                                $order_id = "BOOKING_"."booking_id".date('Ymdhis');
                                $cust_id = "booking_id";
                                $order_details = "";
                            } ?>
                            <input type="hidden" name="ORDER_ID" id="order_id" value="<?php echo $order_id;?>">
                            <input type="hidden" name="CUST_ID" id="cust_id" value="<?php echo $cust_id;?>">
                            <input type="hidden" name="INDUSTRY_TYPE_ID" id="industry_type_id" value="<?php echo PAYTM_GATEWAY_INDUSTRY_TYPE_ID ; ?>">
                            <input type="hidden" name="CHANNEL_ID" id="channel_id" value="<?php echo PAYTM_GATEWAY_CHANNEL_ID ; ?>">
                            <input type="hidden" name="ORDER_DETAILS" id="order_details" value="<?php echo $order_details;?>">
                            <input type="hidden" name="TXN_AMOUNT" id="txn_amount" value="">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success" style="background: #2a3f54;border-color:#2a3f54;">Continue</button>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    $(document).ready(function () {
        get_partner_amount_details();
        //Disable full page
//        $("body").on("contextmenu",function(e){
//            return false;
//        });

        
    });
    
    
    var is_c_s_gst;
    
    $('input[name=amount]').change(function () {  
        get_final_amount();
    });
    
    $('#other_amount').click(function () {
        // unselect tds radio buttons.
        $("input:radio[class^=tds]").each(function(i) {
            this.checked = false;
        });

        $('#other_amount_div').show();            
    });
    
    $('.initial_amount').click(function () {
        // unselect tds radio buttons.
        $("input:radio[class^=tds]").each(function(i) {
            this.checked = false;
        });        
        $('#other_amount_div').hide();
    });
    
//    $('input[name=tds_rate]').change(function(){
//        get_final_amount();
//    });
    
    function get_partner_amount_details(){
        $.ajax({
            method:"POST",
            url:'<?php echo base_url();?>paytm_gateway/get_partner_amount_details',
            data:{'partner_id': <?php echo $this->session->userdata('partner_id'); ?>},
            success:function(res){
                var obj = JSON.parse(res);   
                if(obj.status === 'success'){
                    is_c_s_gst = obj.data.is_c_s_gst;
                    var date = new Date(obj.data.agreement_end_date);
                    if(obj.data.amount_details.prepaid_amount > 0 ){
                        $('#acnt_bal').addClass('text-success');
                    }else{
                        $('#acnt_bal').addClass('text-danger');
                    }
                    $('#acnt_bal').html(obj.data.amount_details.prepaid_amount);
                    $('#aggrement_end_date').html(date.toString('dd MMM yyyy'));
                }else if(obj.status === 'error'){
                    alert('Some Error Occures!!! Please Contact backoffice Team');
                }
                
                get_final_amount();
            }
        });
    }
    
    function get_final_amount(is_tds_check = false){
        var final_amount;
        var amount = parseInt($('input[name=amount]').filter(':checked').val());
        
        if(is_tds_check){
            var tds_per = $('#apply_tds_percent').val();
            var tds_amount = ((amount/100) * tds_per).toFixed(2);
            $('#tds_deduction_amount').html(tds_amount);
            console.log(tds_per);
            $('#apply_tds_amount').val(tds_amount);
            final_amount = amount - tds_amount;            
        }else{
            $('#tds_deduction_amount').html(0.00);
            $('#apply_tds_amount').val(0);            
            final_amount = amount;
        }
        
        var gst_amount = amount * .18;
        if(is_c_s_gst){
            var sgst = gst_amount/2;
            var cgst = gst_amount/2;
            var gst = sgst+cgst;
            
            $('#cgst').html(cgst);
            $('#sgst').html(sgst);
        }else{
            var igst = gst_amount;
            var gst = igst;
            $('#igst').html(igst);
        }
        
        var final_amount = parseInt(final_amount)+parseInt(gst);

        $('#final_amount').html(final_amount);
        $('#txn_amount').val(final_amount);
    }
    function deduct_tds(tds_percent) {
        // hide TDS Amount section if none selected.
        if(tds_percent == 0) {
            $('.tds_deduction_amount').css('display', 'none');
        } else {
            $('.tds_deduction_amount').css('display', 'block');
        }
        $('#apply_tds_percent').val(tds_percent);
        get_final_amount(true);
    }

    $("#other_amount_value").blur(function(){
        var other_amount_value = $('#other_amount_value').val();
        parseInt($('input[name=amount]').filter(':checked').val(other_amount_value));
        get_final_amount(true);
    });
    
    $("#payment_form").submit(function(e){
        var amount = $('input[name=amount]').filter(':checked').val();
        
        if(amount >= 2500){
            if (confirm("Are you sure to continue?")) {
                return true;
            } else {
                return false;
            }
        }else{
            alert("Please Enter Amount To Continue...");
        }
        
        return false;
    });
    
</script>