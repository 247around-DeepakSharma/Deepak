<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Spare Parts Booking </h2>
            </div>
            <div class="panel-body">
                <div role="tabpanel"> 
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist" >
                            <li role="presentation" class="active"><a href="#spare_parts_requested" aria-controls="spare_parts_requested" role="tab" data-toggle="tab">Spare Parts Requested</a></li>
                            <li role="presentation"><a href="#shipped" aria-controls="shipped" role="tab" data-toggle="tab">Spare Parts Shipped By Partner</a></li>
                            <li role="presentation"><a href="#delivered" aria-controls="delivered" role="tab" data-toggle="tab">Spare Parts Received By SF</a></li>
                            <li role="presentation"><a href="#defective_part_pending" aria-controls="defective_part_pending" role="tab" data-toggle="tab">Defective Part Pending By SF</a></li>
                            <li role="presentation"><a href="#defective_part_shipped_by_SF" aria-controls="defective_part_shipped_by_SF" role="tab" data-toggle="tab">Defective Part Shipped By SF</a></li>
                            
                        </ul>
                    </div>
                    <div class="tab-content" id="tab-content">
                        <center style="margin-top:30px;"> <img style="width: 60px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                    </div>
                </div>    
            </div>
           </div>
           
       </div>
   </div>
<!--     <div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)){ echo $links;} ?></div>-->    
</div>
<script>
    $(document).ready(function() {
        spare_booking_on_tab();
    });
    
    function spare_booking_on_tab(){
        $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/inventory/spare_part_booking_on_tab',
         success: function (data) {
          $("#tab-content").html(data);   
         }
       });
    }
    
    $(document).on("click", ".open-adminremarks", function () {
   
        var booking_id = this.id;
        var url = $(this).data('id');
        $('#modal-title').text(booking_id);
        $('#textarea').val("");
        $("#url").val(url);

    });
    
    function reject_parts(){
      var remarks =  $('#textarea').val();
      var booking_id = $('#modal-title').text();
      if(remarks !== ""){
        var url =  $('#url').val();
        $.ajax({
            type:'POST',
            url:url,
            data:{remarks:remarks},
            success: function(data){
                console.log(data);
                if(data === "Success"){
                    $("#"+booking_id+"_1").hide();
                    $('#myModal2').modal('hide');
                } else {
                    alert("Spare Parts Cancellation Failed!");
                }
            }
        });
      } else {
          alert("Please Enter Remarks");
      }
    }
</script>
