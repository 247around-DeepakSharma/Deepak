<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    
    <div class="row"  >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            
            <div class="container"  style="border: 1px solid #e6e6e6; padding: 20px;" >
                <h2 style="padding-bottom: 32px;">Bulk Spare Transfer From Partner To Warehouse</h2><hr>
            <?php if(!empty($this->session->flashdata('success'))){ ?>
           <div class="alert alert-success">
            <strong>Success!</strong>  <?php echo $this->session->flashdata('success');  ?>
           </div> 
            <?php }else if(!empty($this->session->flashdata('error'))){ ?>    
            <div class="alert alert-danger">
                <strong>Warning !</strong>  <?php echo $this->session->flashdata('error');  ?> <button id="modalauto" style="padding:0px !important;" class="btn btn-small btn-danger"  data-toggle="modal" data-target="#myModal" >Details</button>
           </div> 
            
           <?php  }  ?>   ?> 
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
 
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <center>
        <h4 class="modal-title">Spare not transferred details</h4>
        </center>
      </div>
      <div class="modal-body">
        <table class="table">
    <thead>
      <tr>
        <th>Booking Id</th>
        <th>Spare Id</th>
        <th>Part Number</th>
      </tr>
    </thead>
    <tbody>
        
      <?php  if(!empty($this->session->flashdata('error_spares'))){
          $error_spares =$this->session->flashdata('error_spares'); ?>
    <h5> Total Spare Not Transferred <span style="color: #f3ecec; background: #dd320b;" class="badge"><?php echo count($error_spares);  ?></span> </h5> 
       <?php   foreach ($error_spares as $data){ ?>
        <tr>
        <td><?php echo $data['booking']; ?> </td>
        <td><?php echo $data['spare_id'];   ?></td>
        <td><?php echo $data['part_number'];  ?></td>
        </tr>
       <?php  }
        }  ?>
    </tbody>
  </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#modalauto").click();
    });
</script>
