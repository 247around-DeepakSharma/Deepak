<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_invoice_id" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Search Challan Id</strong></h3>
            <hr>
            <section class="fetch_invoice_id" style="padding-left:20px;">
                <div class="row">
                    <div class="form-inline" style="margin-left: 20px;">
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="cin_no">CIN Number:</label>
                            <input type="text" class="form-control" id="cin_no">
                        </div>
                        <button class="btn btn-success" id="get_challan_id_data">Search</button>
                    </div>
                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section class="show_challan_id_data"></section>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#get_challan_id_data').click(function () {
            var cin_no = $.trim($("#cin_no").val());
            if (cin_no){
                $('#loader').show();
                $.ajax({
                    method: 'POST',
                    data: {cin_no: cin_no},
                    url: '<?php echo base_url(); ?>employee/invoice/search_challan_id',
                    success: function (response) {
                        //console.log(response);
                        $('#loader').hide();
                        $('.show_challan_id_data').show();
                        $('.show_challan_id_data').html(response);

                    }
                });
            }else{
                alert("Please Insert CIN Number");
            }
        });
    });
</script>