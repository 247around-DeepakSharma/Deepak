<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    
    <div class="row"  >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            
            <div class="container"  style="border: 1px solid #e6e6e6; padding: 20px;" >
                <h2 style="padding-bottom: 32px;">Bulk Spare Transfer From  To Warehouse</h2><hr>
            <?php if(!empty($this->session->flashdata('success'))){ ?>
           <div class="alert alert-success">
            <strong>Success!</strong>  <?php echo $this->session->flashdata('success');  ?>
           </div> 
            <?php }  ?> 
                <form method="POST" id="idForm" action="<?php echo base_url();?>employee/spare_parts/bulkConversion_process">
                    <div class="form-group">
                        <textarea class="form-control"  required="" rows="5" id="bulk_input"  name="bulk_input" placeholder="Booking Ids"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-small btn-success" id="search">Transfer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
 
