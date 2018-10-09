<div class="container-fluid" >
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>Bulk Search Docket Number </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <form method="POST" action="<?php echo base_url();?>employee/inventory/search_courier_invoices">
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
                       <button  type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">×</span>
                       </button>
                        <strong>Docket numbers not found - <span id="not_found_data"></span></strong>
                    </div>
                    <table id="searched_table" class="table table-bordered table-hover table-striped" style="display: none">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>AWB Number</th>
                                <th>Company Name</th>
                                <th>Courier Charge</th>
                                <th>Invoice Id</th>
                                <th>Billable weight</th>
                                <th>Actual weight</th>
                                <th>Update Date</th>
                                <th>Create Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function loadData(){
        bulk_input = document.getElementById("bulk_input").value;
        bulkInputArray = bulk_input.replace( /\n/g, " " ).split( " " );
        if(bulkInputArray.length>50){
            alert("Search Input Should be less then 50");
        } else if(bulk_input){
            var fd = new FormData(document.getElementById("fileinfo"));
            fd.append("label", "WEBUPLOAD");
            fd.append("docket_no",bulkInputArray);

            $.ajax({
                url: "<?php echo base_url() ?>employee/inventory/process_search_courier_invoice",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function (response) { 
                    //console.log(response);
                    response = JSON.parse(response);
                    if(response.status === "success"){ 
                        $("#searched_table tbody").html(response.html);  
                        $("#searched_table").show();
                        console.log(response.notFound.length);
                        if(response.notFound.length > 0){ 
                           $("#not_found_data_div").css("display", "block");
                           $("#not_found_data").text(response.notFound);
                        }
                    } else {
                        alert('No data found');
                    }
                }
            });
        } else{
           alert("Please provide docket number");
           return false;
        }
    }
    
    
</script>