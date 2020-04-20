<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    
    <div class="loader hide"></div>
    
    <div class="row"  >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="container"  style="border: 1px solid #e6e6e6; padding: 20px;" >
            <h2 style="padding-bottom: 32px;">Bulk Spare Transfer To Partner</h2><hr>
           <button id="modalauto" style="padding:0px !important;" class="btn  btn-danger hide"  data-toggle="modal" data-target="#myModal" >Details</button>
                <form method="POST" id="idForm" action="<?php echo base_url();?>employee/spare_parts/bulkPartnerConversion_process">
                    <div class="form-group">
                        <textarea style="resize: none;" class="form-control"  required="" rows="5" id="bulk_input"  name="bulk_input" placeholder="Booking Ids"></textarea>
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
      <div id="errors" class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<style>
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>
<script type="text/javascript">

    $("#idForm").submit(function(e) {
    $(".loader").removeClass('hide');
    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');

    $.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(data)
           {
               console.log(data); // show response from the php script.
               if(data=='success'){
                   swal("Transferred!", "Your spares has been transferred !.", "success"); 
                   $(".loader").addClass('hide');
               }else{
                   swal("Warnings!", "Your all spares has not been transferred !.", "error");
                   $("#errors").html(data);
                   //$("#modalauto").click();
                   $(".loader").fadeOut("slow");
                   $(".loader").addClass('hide');
                    
               }
           }
         });
});
    
    
</script>
