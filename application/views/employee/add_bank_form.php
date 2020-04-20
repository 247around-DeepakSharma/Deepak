<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style>
    #bank_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    #bank_table_filter {
            text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Add Bank</div>
            <div class="panel-body">
            <div class="row">
                 <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
                <form name="myForm" class="form-horizontal" id ="bank_form" novalidate="novalidate" action="<?php echo base_url()?>employee/service_centre_charges/process_add_bank"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('bank_name')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="name" class="col-md-4">Bank Name* </label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="bank_name" name="bank_name" value = "" placeholder="Enter bank name">
                                        <?php echo form_error('charges_type'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group">
                                    <input type="submit" id="submit_btn" name="submit_btn" class="btn btn-info" value="Submit"/>
                                </div>
                            </div>
                        </div>
                    </div>   
                </form>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" id="bank_table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Bank Name</th>
                                <th>Create Date</th>
                                <th>Status</th>
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
</div>
<?php if($this->session->userdata('error')){ $this->session->unset_userdata('error'); } ?>
<?php if($this->session->userdata('success')){ $this->session->unset_userdata('success');  } ?>
<script type="text/javascript">
    var bank_datatable = "";
    //form validation
    (function ($, W, D){
        var JQUERY4U = {};
        JQUERY4U.UTIL = { setupFormValidation: function (){
                $("#bank_form").validate({
                rules: {
                    bank_name: "required",
                },
                messages: {
                    bank_name: "Please enter bank name",
                },
                submitHandler: function (form) {
                    form.submit();
                }
                });
            }
        };
        $(D).ready(function ($) {
            JQUERY4U.UTIL.setupFormValidation();
        });
    })(jQuery, window, document);
    
    bank_datatable = $('#bank_table').DataTable({
        processing: true, //Feature control the processing indicator.
        serverSide: true, //Feature control DataTables' server-side processing mode.
        order: [], //Initial no order.
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        // Load data for the table's content from an Ajax source
        dom: 'lBfrtip',
        buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'banks',
                    exportOptions: {
                       columns: [1,2],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
        ajax: {
            url: "<?php echo base_url(); ?>employee/service_centre_charges/get_bank_details",
            type: "POST",
            data: {}
        },
        //Set column definition initialisation properties.
        columnDefs: [
            {
                "targets": [0,1,2,3], //first column / numbering column
                "orderable": false //set not orderable
            }
        ]
    });
    
    // Activate or Deactivate bank detail
    function update_bank_detail(id, status){
       $.ajax({
           url:'<?php echo base_url(); ?>employee/service_centre_charges/update_bank_details',
           type:'POST',
           data:{id:id, status:status},
       }).done(function(response){
            if(response){
                if(status == '1'){
                    alert("Bank Activate Successfully");
                }
                else{
                    alert("Bank Deactivate Successfully");
                }
                bank_datatable.ajax.reload(null, false);
            }
            else{
                alert("Bank detail not updated. Try Again");
            }
       });
    }
</script> 

    