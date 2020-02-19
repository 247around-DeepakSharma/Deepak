<style>
    #spare_sale_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #spare_sale_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Spare Sale List</h3>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
        <?php if($this->session->flashdata('error')){ ?>
        <div class="error_msg_div">
            <div class="alert alert-warning alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"><?php echo $this->session->flashdata('error'); ?></span></strong>
            </div>
        </div>
        <?php } ?>
        
        <?php if($this->session->flashdata('success')){ ?>
        <div class="success_msg_div">
            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"><?php echo $this->session->flashdata('success'); ?></span></strong>
            </div>
        </div>
        <?php } ?>
        
        <div class="model-table">
            <table class="table table-bordered table-hover table-striped" id="spare_sale">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Booking Id</th>
                        <th>Part Requested</th>
                        <th>Part Type</th>
                        <th>Model Number</th>
                        <th>Sale Invoice Id</th>
                        <th>Price</th>
                        <th>Service Centre</th>
                        <th>District</th>
                        <th>Reverse Sale Invoice Id</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
</div>
<script>
    var sold_spare_parts_table;
    var time = moment().format('D-MMM-YYYY');
    
    $(document).ready(function(){
        //load all spare sale list at starting
        get_spare_sale_list();
    });
    
    function get_spare_sale_list(){
        sold_spare_parts_table = $('#spare_sale').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "search": "Filter By : Booking ID, Sale Invoice ID"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Spare_Sale_List',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/invoice/get_spare_sale_list",
                "type": "POST",
                data: function(d){
                }
            },
            "deferRender": true       
        });
    }
    
    //function to reverse spare sale invoice
    function reverse_spare_sale(id, row_index)
    {
        //popup for confirmation
        var status = confirm("Are you sure?");
        if (status) {
            //user confirmed to continue
            document.getElementById("btn"+row_index).disabled = true;
            document.getElementById("btn"+row_index).innerHTML = "Generating...";
            var temp = sold_spare_parts_table.row(row_index).data();
            var url = '<?php echo base_url(); ?>employee/invoice/reverse_spare_sale';
            var success_msg = "Reverse spare sale invoice created successfully!";
            var error_msg = "Something went wrong while creating reverse spare sale invoice!";

            $.ajax({
            type: 'POST',
            url: url,
            data: {sale_invoice_id : id}
          })
          .done (function(data) {
             // success case
              if(data){
                  // success response
                  //change datatable - replace 'Reverse Sale Invoice' button with created reverse invoice id
                  temp[9] = data;
                  sold_spare_parts_table.row(row_index).data(temp).invalidate();
                  alert(success_msg);
              }
              else{
                  //fail response
                   alert(error_msg);
                   document.getElementById("btn"+row_index).disabled = false;
                   document.getElementById("btn"+row_index).innerHTML = "Reverse Sale Invoice";
              }
            //  location.reload();
          })
          .fail(function(jqXHR, textStatus, errorThrown){
              //fail case
              alert(error_msg);
              document.getElementById("btn"+row_index).disabled = false;
              document.getElementById("btn"+row_index).innerHTML = "Reverse Sale Invoice";
           //   location.reload();
           })
        }

        return false;
        
    }

</script>