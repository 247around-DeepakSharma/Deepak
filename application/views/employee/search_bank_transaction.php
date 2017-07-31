<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<style>
    #invoiceDetailsModal .modal-lg {
        width: 100%!important;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_bank_transaction" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Search Bank Transaction</strong></h3>
            <hr>
            <section class="fetch_bank_transaction_details" style="padding-left:20px;">
                <div class="row">
                    <div class="form-inline">
                        <div class="form-group" style="margin-right: 20px;">
                            <label for="transaction_type">Transaction Type</label>
                            <select class="form-control" id="transaction_type">
                                <option value="Credit">Credit</option>
                                <option value="Debit">Debit</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right: 20px;">
                            <label for="transaction_date">Transaction Date</label>
                            <input type="text" class="form-control" id="transaction_date">
                        </div>
                        <div class="form-group" style="margin-right: 20px;">
                            <label for="transaction_amount">Transaction Amount</label>
                            <input type="text" class="form-control allowNumericWithDecimal" id="transaction_amount">
                        </div>
                        <button class="btn btn-success" id="get_bank_transaction_data">Search</button>
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section class="show_bank_transaction_details"></section>
        </div>
    </div>
    <!--Invoice Details Modal-->
    <div id="invoiceDetailsModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <div id="open_model"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<!-- end Invoice Details Modal -->
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script>
    
    $(function() {
        $('#transaction_date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                    format: 'YYYY/MM/DD'
            }
        });
    });
    
    $(".allowNumericWithDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) || e.ctrlKey) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(document).ready(function () {
        $('#get_bank_transaction_data').click(function () {
            var transaction_type = $("#transaction_type").val();
            var transaction_date = $("#transaction_date").val();
            var transaction_amount = $.trim($("#transaction_amount").val());
            
            if (transaction_type === null || transaction_type === undefined ||
                transaction_date === null || transaction_date === undefined ||
                transaction_amount === null || transaction_amount === undefined || transaction_amount === ''
                ){
            
                if(transaction_amount === ''){
                    alert("Please fill Amount");
                }else{
                    alert("Some Issue Occured !!! Please Refresh Page And Try Again");
                }
                
            }else{
                $('#loader').show();
                $.ajax({
                    method: 'POST',
                    data: {'transaction_type':transaction_type,'transaction_date':transaction_date,'transaction_amount':transaction_amount},
                    url: '<?php echo base_url(); ?>employee/accounting/process_search_bank_reansaction',
                    success: function (response) {
                        //console.log(response);
                        $('#loader').hide();
                        $('.show_bank_transaction_details').show();
                        $('.show_bank_transaction_details').html(response);

                    }
                });
                
            }
        });
    });
    
    
</script>