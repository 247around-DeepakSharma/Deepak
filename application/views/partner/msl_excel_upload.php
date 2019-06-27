<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="tab-content" id="tab-content">
    <div role="tabpanel" class="tab-pane active" id="onMsl">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
<div class="right_col" role="main">
    <h1 class="col-md-6 col-sm-12 col-xs-12"><b>MSL Excel Upload </b></h1>
    <div class="clearfix"></div>
    <div style='margin:3%;'>
        
            <?php 
            if(!empty($this->session->flashdata('fail'))){ ?>
               
                 <div class="alert alert-danger">
                 <strong>Failed!</strong>  <?php echo $this->session->flashdata('fail'); ?>
                </div>
            <?php }else if(!empty($this->session->flashdata('details'))){ ?>
                   <div class="alert alert-success">
                   <strong>Success!</strong> Click See details button to see the details because some data may not be updated
                   <?php if(!empty($this->session->flashdata('fail')) || !empty($this->session->flashdata('details'))){ ?>
                             <a href="#" class="btn btn-sm btn-warning"  data-toggle="modal" data-target="#myModal">See details</a>
                       <?php  } ?>
                  </div>
           <?php  }
            ?>

        <form method="POST"   enctype="multipart/form-data"  action="<?php echo base_url();  ?>file_upload/process_upload_file"  >
            <div class="row">
                
               <div class="col-md-2">
                    <div class="form-group">
                        <input type="file" required id="msl_excel" name="file"  accept=".xls,.xlsx" />
                    </div>
                </div>
                
                <input type="hidden" name="file_type" value="<?php echo MSL_TRANSFERED_BY_PARTNER_BY_EXCEL; ?>" />
                
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-small btn-primary" id="search"  >Upload</button>

                       
                    </div>
                </div>
            </div>
        </form>
    </div>
 
    
    
    
    <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Excel Entries Not Updated</h4>
      </div>
      <div class="modal-body">
            <?php 
            if(!empty($this->session->flashdata('fail'))){
                echo $this->session->flashdata('fail');
            }else{
                echo $this->session->flashdata('details'); 
            }
            ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    
    
    
        
</div>
</div>
</div>
</div>
</div>
</div>
<style>
    #brand_collateral_partner_filter{
        float: right;
    }
</style>
<script>
   
   
      $("#msl_excel").change(function () {
        var fileExtension = ['xls', 'xlsx', 'csv'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
            $("#search").attr("disabled","disabled");
        }else{
            
            $("#search").removeAttr("disabled");
        }
    });
    
</script>