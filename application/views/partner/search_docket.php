<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Search Docket Number </h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <section class="search_docket_number_div">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <form method="POST" id='fileinfo'>
                                    <div class="form-group">
                                        <label for="model_number" >Docket Number *</label>
                                        <textarea class="form-control" rows="5" id="bulk_input" name="bulk_input" placeholder="Enter Docket Number"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-small btn-success" id="search" onclick="loadData()">Search</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="alert alert-danger alert-dismissible" id="not_found_data_div" role="alert" style="margin-top:15px; display: none">
                                    <button  type="button" class="close">
                                        <span onclick='close_alert_box()'>×</span>
                                    </button>
                                    <strong>Docket numbers not found - <span id="not_found_data"></span></strong>
                                </div>
                                <table id="searched_table" class="table table-bordered table-hover table-striped" style="display: none">
                                    <thead>
                                        <tr>
                                            <th>S No.</th>
                                            <th>AWB Number</th>
                                            <th>Company Name</th>
                                            <th>Part Name</th>
                                            <th>Part Number</th>
                                            <th>Courier Charge</th>
                                            <th>Invoice Id</th>
                                            <th>Large Box Count</th>
                                            <th>Small Box Count</th>
                                            <th>Billable weight</th>
                                            <th>Actual weight</th>
                                            <th>Courier Recipt</th>
                                            <th>Create Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function loadData() {
        bulk_input = document.getElementById("bulk_input").value;
        bulkInputArray = bulk_input.replace(/\n/g, " ").split(" ");
        if (bulkInputArray.length > 50) {
            alert("Search Input Should be less then 50");
        } else if (bulk_input) {
            var fd = new FormData(document.getElementById("fileinfo"));
            fd.append("label", "WEBUPLOAD");
            fd.append("docket_no", bulkInputArray);

            $.ajax({
                url: "<?php echo base_url() ?>employee/partner/process_search_docket",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $("#search").html('Searching..');
                    $("#not_found_data_div").hide();
                },
                success: function (response) {
                    //console.log(response);
                    $("#search").html('Search');
                    response = JSON.parse(response);
                    if (response.status === "success") {
                        $("#searched_table tbody").html(response.html);
                        $("#searched_table").show();
                        if (response.notFound) {
                            $("#not_found_data_div").css("display", "block");
                            $("#not_found_data").text(response.notFound);
                        }
                    } else {
                        alert('No data found');
                    }
                }
            });
        } else {
            alert("Please provide docket number");
            return false;
        }
    }
    function close_alert_box() {
        $("#not_found_data_div").css("display", "none");
    }


</script>