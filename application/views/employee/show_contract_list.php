<style>
    #contract_list_filter{
        float: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
        <div class="row">
            <h1 class="col-md-6 col-sm-12 col-xs-12">Contract List</h1>
        </div>
        <hr>
        <div class="row">
            <div class="container-fluid">
                <table id="contract_list" class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Partner Name</th>
                            <th>Contract Type</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#contract_list').DataTable({
         "processing": true, //Feature control the processing indicator.
         "serverSide": true, //Feature control DataTables' server-side processing mode.
         "order": [[ 1, "asc" ]], //Initial no order.
         "pageLength": 10,
         "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
          ajax: {
                url: "<?php echo base_url();?>employee/partner/get_contract_list",
                type: "POST",
                data: function(d){
                }
            },
            columnDefs: [
                {
                    targets: [0,1,2,3,4,5], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
                $("#in_tranist_record").text(response.recordsTotal);
            }

        });
    });
    
    
</script>