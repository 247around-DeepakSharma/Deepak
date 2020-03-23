<style>
    #datatable2_info{
    display: none;
    }
    
    #datatable2_filter{
        text-align: right;
    }
    .select2-container--default .select2-selection--single{
        height: 32px;
    }
</style>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
      
                             <a href="#" class="btn btn-sm btn-warning hide"  data-toggle="modal" id="model_error" data-target="#myModal">See details</a>

 
            <h1 class="page-header">
                <center><a href="<?php echo base_url(); ?>inventory/msl_excel_upload">Go Back</a></center>
            </h1>
          <div id="show_both">

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
                echo $message;
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
<script>
  $("#model_error").click();
</script>
