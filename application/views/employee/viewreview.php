 <div id="page-wrapper">
   <div class="container-fluid">
        <div class="row">
 
        	<div class="col-lg-12">
        			 <?php if($this->session->userdata('inactive')) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('inactive') . '</strong>
         </div>';
         }
         ?>
          <?php if($this->session->userdata('success')) {
         echo '<div class="alert alert-success alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('success') . '</strong>
         </div>';
         }
         ?>
         <?php if(validation_errors()) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . validation_errors() . '</strong>
         </div>';
         }
         ?>
            <h1 class="page-header">
                Review
            </h1>
            </div>
        </div>


        <div class="row">
         <div class="col-lg-12">

         	  <table class="table table-bordered table-hover table-striped data " >
               <thead>
                  <tr>
                     <th>No.</th>
                     <th>Handyman Photo </th>
                     <th>Handyman </th>
                     <th>Handyman Services</th>
                     <th>User Email</th>
                     <th>Behaviour</th>
                     <th>Expertise</th>
                     <th>Review</th>
                     <th>Status</th>
                     <th colspan="3">Operation</th>
                  </tr>
               </thead>
               <tbody> 	

               	 <?php $i=1; foreach($review as $key =>$Getreview) {?>
               	<tr>
               		<td><?php echo $i; ?></td>
                  <td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $Getreview['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
               		<td><?php echo $Getreview['name']; ?></td>
                  <td><?php echo $Getreview['services']; ?></td>
               		<td><?php echo $Getreview['user_email']; ?></td>
               		<td><?php echo $Getreview['behaviour']; ?></td>
               		<td><?php echo $Getreview['expertise']; ?></td>
               		<td><?php echo $Getreview['review']; ?></td>
                  <td><?php if($Getreview['status'] == 0) { echo "Active";} else{ echo "Inactive";}?></td>
               	  <td>
                     <?php if($Getreview['status'] == 0) { ?>
                    
                    <a class="btn btn-small btn-success btn-sm"  href="<?php echo base_url();?>employee/review/toDoinactive/<?php echo $Getreview['id'];?>">Deactivate</a>
                  
               	<?php } else if($Getreview['status'] == 1){?>
                   <a class="btn btn-small btn-info btn-sm"  href="<?php echo base_url();?>employee/review/toDoactive/<?php echo $Getreview['id'];?>">Active</a>
                   <?php } ?>
                     </td>
                     <td>
                   <a class="btn btn-small btn-danger btn-sm"  href="<?php echo base_url();?>employee/review/delete/<?php echo $Getreview['id'];?>">Delete</a>
                 </td>
                <td>
                   <a data-id="<?php echo $Getreview['user_email']?>" title="Add this item" class="open-models btn btn-primary btn-sm" href="#models">Send Mail</a>
                </td>
                </tr>
               	<?php 	$i =$i+1;}?> 
               </tbody>
           </table>
             
         </div>
     </div>

<div class="modal fade" id="models" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" >
            <div class="modal-content" >
               <div class="modal-header"  >
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Send  Mail</h4>
               </div>
               <div class="modal-body">
                  <div class="col-md-12">
                     <form class="form-horizontal" action="<?php echo base_url()?>employee/review/sending_mail" method="POST" enctype="multipart/form-data">
                      
                        <div class="form-group ">
                           <label for="Name" >To</label>
                           <input type="text" name="user_email" class="form-control"  id="user_email" value="" />
                        </div>
                        <div class="form-group ">
                           <label for="Name" >Image Attach</label>
                           <input  class="form-control"  type="file" name="file" >
                        </div>
                        <div class="form-group ">
                           <label for="Name" >Comment</label>
                           <textarea type="text" class="form-control" rows="4" cols="50" name="comment"  value = "" placeholder = "Comment"> </textarea  >
                        </div>
                        <div class="modal-footer">
                           <input type="submit" class="btn btn-primary" value ="Send Mail">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                       
                     </form>
                  </div>
               
                  <div class="modal-footer">
                 
                  </div>
               </div>
            </div>
         </div>
     </div><!-- /.modal -->
<script>
$(document).on("click", ".open-models", function (e) {

  e.preventDefault();

  var _self = $(this);

  var myuser_email = _self.data('id');
  $("#user_email").val(myuser_email);

  $(_self.attr('href')).modal('show');
});
</script>
<!--   container -fluid  -->
    </div>
</div>


<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('inactive'); ?>