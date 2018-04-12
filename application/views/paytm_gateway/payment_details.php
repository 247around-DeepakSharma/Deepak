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
                <div class="x_title">
                    <h2>Make a payment</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="payment_details_div">
                        
                        <div class="current_details">
                            <div class="row tile_count">
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <span class="count_top">Account Balance</span>
                                    <div class="count" id="acnt_bal"></div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <span class="count_top">Valid till</span>
                                    <div class="count" id="aggrement_end_date"></div>
                                </div>
                            </div>
                            <p>Once your amount is below 0, you are not able to insert new booking</p>
                        </div>
                        <hr>
                        <form method="post" action="<?php echo base_url();?>payment/checkout_processing" onsubmit="return confirm('Do you really want to make a payment?');">
                            
                            <h2>Select Amount To make a payment</h2>
                            
                            <div class="amount_details">
                                <div class ="col-md-4 col-sm-6 col-xs-12">
                                    <div class="radio">
                                        <label><input type="radio" name="amount" value="2500" required="" checked=""><i class="fa fa-inr"></i> 2500</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="amount" value="5000" required=""><i class="fa fa-inr"></i> 5000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" name="amount" value="10000" required=""><i class="fa fa-inr"></i> 10000</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="amount" value="15000" required=""><i class="fa fa-inr"></i> 15000</label>
                                    </div>
                                </div>
                                <div class ="col-md-4 col-sm-6 col-xs-12">
                                    <div class="radio">
                                        <label><input type="radio" name="amount" value="20000" required=""><i class="fa fa-inr"></i> 20000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" name="amount" value="25000" required=""><i class="fa fa-inr"></i> 25000</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="amount" value="30000" required=""><i class="fa fa-inr"></i> 30000</label>
                                    </div>
                                    <div class="radio disabled">
                                        <label><input type="radio" name="amount" value="50000" required=""><i class="fa fa-inr"></i> 50000</label>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-md-6 col-sm-12 col-xs-12 payment_summary">
                                <h2>Payment Summary</h2>
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
                            
<!--                            <div class="tds_details">
                                <div class="radio">
                                    <label><input type="checkbox" name="tds_rate" value="1"> Deduct TDS at <span id="tds_per">2</span>%</label>
                                </div>
                            </div>-->
                            <hr>
                            <div class="final_amount">
                                <h4> Total Amount To be Paid  
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
                                <button type="submit" class="btn btn-success">Continue</button>
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
        //Disable full page
        $("body").on("contextmenu",function(e){
            return false;
        });
    });
    
    
    var is_c_s_gst;
    $(document).ready(function(){
        get_partner_amount_details();
    });
    $('input[name=amount]').change(function () {  
        get_final_amount();
    });
    
    $('input[name=tds_rate]').change(function(){
        get_final_amount();
    });
    
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
                    alert('Some Error Occures!!! Please Contact 247Around Team');
                }
                
                get_final_amount();
            }
        });
    }
    
    function get_final_amount(){
        var final_amount;
        var amount = parseInt($('input[name=amount]').filter(':checked').val());
        //var is_tds_check = parseInt($('input[name=tds_rate]').filter(':checked').val());
        var is_tds_check = false;
        if(is_tds_check){
            var tds_per = parseInt($('#tds_per').html());
            final_amount = amount - amount * (tds_per/100);
        }else{
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
</script>