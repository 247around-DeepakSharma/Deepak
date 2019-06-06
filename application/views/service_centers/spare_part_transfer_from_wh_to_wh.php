<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <h2 style="    padding-left: 194px;
    padding-bottom: 32px;">Bulk Spare Transfer from Warehouse to Warehouse</h2>

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
                            <label>Select Warehouse</label>
                            <select class="form-control select2" id="warehouse"  required name="service_center"></select>
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
<script>
    $(document).ready(function(){
       is_wh=1; 
       get_vendor(); 
    });
    
        function get_vendor(is_wh) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>'+'employee/vendor/get_all_service_center_with_micro_wh',
            data:{is_wh:is_wh},
            success: function (response) {
                $('#warehouse').html(response);      
                $('#warehouse').select2();  
            }
        });
    }
    
    
 </script>
