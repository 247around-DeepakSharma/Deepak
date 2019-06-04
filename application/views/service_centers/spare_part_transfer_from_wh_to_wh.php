<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>

    .dataTables_filter{display: none;}

 
    #inventory_master_list_filter{
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

    #inventory_master_list_processing{
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
<div class="row" style="border: 1px solid #e6e6e6; padding: 20px;">
<div class="right_col" role="main">
    <div class="clearfix"></div>


            <div class="title">
            <div class="row">
                <div class="col-md-12">
                    <h3 style="    padding-left: 150px;
    padding-bottom: 32px;" >Bulk Spare Transfer from Warehouse to Warehouse</h3><hr>
                </div>

            </div>
        </div>


    <h2 ></h2>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            <div class="container" >
            <?php if(!empty($this->session->flashdata('success'))){ ?>
           <div class="alert alert-success">
            <strong>Success!</strong>  <?php echo $this->session->flashdata('success');  ?>
           </div> 
            <?php }else if(!empty($this->session->flashdata('error'))){ ?>    
            <div class="alert alert-danger">
            <strong>Warning !</strong>  <?php echo $this->session->flashdata('error');  ?>
           </div> 
           <?php  }  ?> 
                <form method="POST" id="idForm" action="<?php echo base_url();?>employee/spare_parts/spare_transfer_from_wh_to_wh_process">
                   <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Warehouse From</label>
                            <select class="form-control select2" id="warehouse"  required name="service_center"></select>
                        </div>
                        </div>


                        <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Warehouse To</label>
                            <select class="form-control select2" id="warehouseto"  required name="service_center_to"></select>
                        </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                       <div class="form-group">
                        <label>Enter Booking Ids</label>
                        <textarea class="form-control" rows="5" required="" id="bulk_input"  name="bulk_input" placeholder="Booking Ids"></textarea>
                    </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-small btn-success" id="search">Transfer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
</div>
</div>


 

<script>
    $(document).ready(function(){
       is_wh=1; 
       get_vendor(is_wh); 

       $("#warehouseto").change(function(){
        var to = $(this).val();
        var from = $("#warehouse").val();
        if (to==from) {
            alert("Both warehouse Can not be same ");
            $(this).select2("val", "");
        }

       });

      $("#warehouse").change(function(){
        var to = $(this).val();
        var from = $("#warehouseto").val();
        if (to==from) {
            alert("Both warehouse Can not be same ");
            $(this).select2("val", "");
        }

       });

    });
    



        function get_vendor(is_wh) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>'+'employee/vendor/get_all_service_center_with_micro_wh',
            data:{is_wh:is_wh},
            success: function (response) {
                $('#warehouse').html(response);      
                $('#warehouse').select2();  
                $('#warehouseto').html(response);      
                $('#warehouseto').select2();
            }
        });
    }

 
    
 </script>
