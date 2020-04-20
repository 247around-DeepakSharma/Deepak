  <?php if (isset($date_range)) { ?>
<div id="page-wrapper">
    <div style="margin-left:10px;">

      
            <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
            <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
            <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
            <script type="text/javascript">
                $(function () {
                    $('input[name="daterange"]').daterangepicker({
                        locale: {
                            format: 'YYYY/MM/DD'
                        },
                        startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
                        endDate: '<?php echo date("Y/m/t") ?>'
                    });

                });
            </script>
            <div class="row" style="margin-top:20px;">
                <form class="form-inline" action="#" method="POST">
                    <div class="form-group">
                        <label for="date ragne">Select Date Range:</label>
                        <input type="text" class="form-control" name="daterange"  />
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0)" type="submit" class="btn btn-default" onclick="load_data()">Submit</a>
                    </div>
                </form>
                <div id="load_data" style="margin-top:20px;"></div>
            </div>
        


    </div>
</div>

<script>
function load_data(){
    var date_range =  $('input[name="daterange"]').val();
    
    var postData = {};
    postData['date_range'] = date_range;
    $.ajax({
        type: 'POST',
        beforeSend: function(){
            $("#load_data").html('<div class="text-center"><img src= "<?php echo base_url(); ?>images/loadring.gif" /></div>');
        },
        url: '<?php echo base_url(); ?>employee/invoiceDashboard/process_invoice_summary_for_partner',
        data: postData,
        success: function (data) {
        console.log(data);
             $("#load_data").html("Invoice Summary is sent on mail..");
         }
    });
}
</script>
<?php } ?>