<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main">
    <!-- buyback balance -->
    <div class="row bb_balance">
        <div class="container-fluid" style="background-color:#fff;">
            <div class="form-group col-md-3" style="float: right;"><label for=""> Date</label>
                  <input type="text" class="form-control" name="daterange" id="daterange_id" onchange="getBalance(this.value)">
              </div>
            <table class="table">
  <thead>
    <tr>
      <th scope="col">Date</th>
      <th scope="col">TV Balance</th>
      <th scope="col">LA Balance</th>
      <th scope="col">Mobile Balance</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody id="balance_holder_table">
  </tbody>
</table>
        </div>
    </div>
</div>
<script>
    $('input[id="daterange_id"]').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'YYYY-MM-DD'
            },
            startDate:  "<?php echo date("Y-m-d", strtotime("-15 days")); ?>"
        });
        function getBalance(dateRange){
            var post_request = 'POST';
            var data = {dateRange: dateRange};
            url =  '<?php echo base_url(); ?>buyback/buyback_process/get_bb_svc_balance';
            sendAjaxRequest(data,url,post_request).done(function(response){
                $("#balance_holder_table").html(response);
            });
        }
        function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    </script>
