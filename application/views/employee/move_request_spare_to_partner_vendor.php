
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
               <div class="panel-heading">
                   <div class="row">
                       <div class="col-md-6">
                           <h2 class="panel-title"><i class="fa fa-search" aria-hidden="true"></i> Search Details By Booking Id </h2>
                       </div>                     
                   </div>
               </div>
                              
               <div class="panel-body">
                   <div role="tabpanel"> 
                       <div class="row">
                           <div class="container col-md-4">
                               <div class="panel panel-info" >
                                   <div class="panel-heading" >
                                       <div style="display: inline-block;">
                                           <p id="search-text-err">Search Booking: </p>                                    
                                           <input type="text" class="form-control" name="booking_id" value="" id="booking_id" />                           
                                       </div>                                  

                                       <div style="display: inline;">
                                           <button class="button-search" id="search_lists">Search</button>  
                                       </div>

                                   </div> 
                               </div>
                           </div>

                       </div>   
                       <div id="parts_details_data"></div>
                   </div>    
               </div>
           </div>
           
       </div>
   </div>
  
</div>
 
<script type="text/javascript">
    $(document).ready(function(){
       $("#search_lists").on('click',function(){           
          var booking_id =$("#booking_id").val(); 
          if(booking_id===''){
             $("#search-text-err").html("<span style='color:red;'>Please Enter Booking Id</span>");             
          }else{
              $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_details_search_by_booking_id',
                data: {booking_id: booking_id},
                success: function (response) { 
                    if(response === "error"){
                        alert('There is some issue. Please refresh and try again');
                    } else {
                        $("#parts_details_data").html(response);
                    }   
                }

        });
          }
           
       });
    })
</script>