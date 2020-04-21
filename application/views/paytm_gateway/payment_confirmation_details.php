<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top: 60px;">
            <div class="x_panel">
                <?php if(isset($invalid_data)) { ?>
                    <div class="invalid_payment_response_div">
                        <h2 class="text-warning">OOPS!!! Invalid Request</h2>
                        <?php if($this->session->userdata('partner_id')) { ?>
                        <p>It looks you are trying an invalid request. Please 
                            <a class="btn btn-info btn-sm" href="<?php echo base_url();?>payment/details">Click Here</a>
                            to go to payment page or <a class="btn btn-info btn-sm" href="<?php echo base_url();?>partner/home">Go to Home</a>
                        </p>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="valid_payment_response_div">
                    <div class="col-md-8 col-sm-12">
                    <?php if($is_txn_successfull) { 
                        switch ($final_txn_status){ 
                            case 'TXN_SUCCESS':
                                echo "<h1 class='text-success'>Payment Successful</h1>";
                                break;
                            case 'TXN_FAILURE':
                                echo "<h1 class='text-danger'>Payment Failed</h1>";
                                break;
                            case 'PENDING':
                            case 'OPEN':
                                echo "<h1 class='text-warning'>Payment Pending</h1>";
                                break;
                        }
                    ?>
                    <?php }else{ ?> 
                        <h1 class="text-danger">Payment Failed</h1>
                    <?php } ?>
                    
                    <h4>Transaction ID : <?php echo $gw_txn_id; ?></h4>
                    
                    <?php 
                        switch ($final_txn_status){ 
                            case 'TXN_SUCCESS':
                                echo "<p>Your payment completed successfully. You will receive payment confirmation email shortly.</p>";
                                break;
                            case 'TXN_FAILURE':
                            case 'PENDING':
                            case 'OPEN':
                                echo "<p>".$final_response_msg."</p>";
                                break;
                        }
                    ?>
                    <p></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <h2>Payment Details</h2>
                    <table class="table table-hover table stripped">
                        <tr>
                            <th>Amount</th>
                            <td><?php echo $txn_amount; ?></td>
                        </tr>
                        <?php if(isset($payment_mode) && !empty($payment_mode)) { ?> 
                         <tr>
                            <th>Payment Mode</th>
                            <td>
                                <?php switch ($payment_mode) { 
                                    case 'CC':
                                        echo 'Credit Card';
                                        break;
                                    case 'DC':
                                        echo 'Debit Card';
                                        break;
                                    case 'NB';
                                        echo 'Net Banking';
                                        break;
                                    case 'PPI':
                                        echo 'Paytm Cash';
                                        break;
                                    case 'Telco':
                                        echo 'Operator Billing';
                                        break;
                                }?>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(isset($bank_name) && !empty($bank_name)) { ?> 
                         <tr>
                            <th>Bank Name</th>
                            <td><?php echo $bank_name; ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    </div>
                </div>
                <?php } ?>
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
</script>

<?php if($this->session->userdata("query")){ $this->session->unset_userdata("query");} ?>
<?php if($this->session->userdata("payment_link_id")){ $this->session->unset_userdata("payment_link_id");} ?>
<?php if($this->session->userdata("user_email")){ $this->session->unset_userdata("user_email");} ?>
<?php if($this->session->userdata("user_contact_number")){ $this->session->unset_userdata("user_contact_number");} ?>