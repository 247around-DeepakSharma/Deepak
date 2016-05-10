<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <?php if($this->session->userdata('success')) {
         echo '<div class="alert alert-success alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('success') . '</strong>
         </div>';
         }
         ?>
         <div class="col-lg-12">
            <h2 class="page-header">
               User Report
            </h2>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   User Report
               </li>
            </ol>
         </div>
      </div>
      <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr bgcolor="#ccffff">
               <th>No #</th>
               <th>User Name</th>
               <th>Phone Number</th>
               <th>Handyman Name</th>
               <th>Service</th>
               <th>Handyman Phone</th>
               <!--<th>update Date</th> -->
               
            </tr>
         </thead>
         <tbody>
            <?php $i =1; foreach($report as $key =>$value) {?>
            <tr>
               <td><?php echo $i;?></td>
               <td><?php echo $value['name']?></td>
               <td><?php echo $value['phone_number']?></td>
               <td><?php echo $value['handyman_name']?></td>
               <td><?php echo $value['services']?></td>
               <td><?php echo $value['phone']?></td>
               
              <!-- <td>
                  <a data-id="<?php echo $value['id']?>"  title="Add this item" class="open-models btn btn-danger" href="#deactivate">Deactivate</a>
                  <a data-id="<?php echo $value['user_email']?>" title="Add this item" class="open-models btn btn-primary" href="#models">Send Mail</a>
                   <a data-id="<?php echo $value['phone']?>" title="Add this item" class="open-models btn btn-primary" href="#model">Send Message</a>
               <!--</td>-->
            </tr>
            <?php $i =$i+1;} ?>
         </tbody>
      </table>
      <!--<div class="modal fade" id="models" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" >
            <div class="modal-content" >
               <div class="modal-header"  >
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Send Mail</h4>
               </div>
               <div class="modal-body">
                  <div class="col-md-12">
                     <form class="form-horizontal" action="<?php echo base_url()?>user/sending_mail" method="POST" >
                        <div class="form-group ">
                           <label for="Name" >User Email</label>
                           <input type="text" name="user_email" class="form-control"  id="user_email" value="" />
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
      </div>
      <div class="modal fade" id="deactivate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" >
            <div class="modal-content" >
               <div class="modal-header"  >
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Add Verification Comment </h4>
               </div>
               <div class="modal-body">
                  <div class="col-md-12">
                     <form class="form-horizontal" action="<?php echo base_url()?>user/deactivate_report " method="POST" >
                        <div class="form-group ">
                           <label for="Name" >Comment</label>
                           <textarea type="text" class="form-control" rows="4" cols="50" name="comment"  value = "" placeholder = "Comment"></textarea  >
                           <input type="text" name="id"  id="id"  hidden>  

                        </div>
                        <div class="modal-footer">
                           <input type="submit" class="btn btn-primary" value ="Add">
                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- /.modal -->
      <!-- /.modal -->
      <!-- <div class="modal fade" id="model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" >
            <div class="modal-content" >
               <div class="modal-header"  >
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Send Sms</h4>
               </div>
               <div class="modal-body">
                  
                 <div class="col-md-12">
                     <form class="form-horizontal" action="<?php echo base_url()?>user/sending_sms" method="POST" >
                        <div class="form-group ">
                           <label for="Name">User Phone</label>
                          <input type="text" name="phone" class="form-control"  id="phone" value="" />
                        </div>
                        <div class="form-group ">
                           <label for="Name" >Comment</label>
                           <textarea type="text" class="form-control" rows="4" cols="50" name="comment"  value = "" placeholder = "Comment"> </textarea  >
                        </div>
                        <div class="modal-footer">
                           <input  type="submit" class="btn btn-primary" value ="Send Message">
                        </div>
                     </form>
                  </div> 
                  <div class="modal-footer">
                 
                  </div>
               </div>
            </div>
            </div>
            </div>-->
      <script>
         $(document).on("click", ".open-models", function (e) {
         
            e.preventDefault();
         
            var _self = $(this);
         
            var myuser_email = _self.data('id');
            $("#user_email").val(myuser_email);
         
            $(_self.attr('href')).modal('show');
         });
      </script>
      <script>
         $(document).on("click", ".open-models", function (e) {
         
            e.preventDefault();
         
            var _self = $(this);
         
            var myuser_email = _self.data('id');
            $("#id").val(myuser_email);
         
            $(_self.attr('href')).modal('show');
         });
      </script>
      <!--<script>
         $(document).on("click", ".open-models", function (e) {
         
            e.preventDefault();
         
            var _self = $(this);
         
            var myuser_email = _self.data('id');
            $("#phone").val(myuser_email);
         
            $(_self.attr('href')).modal('show');
         });
         </script>-->
      <!-- end of container -->
   </div>
</div>
<?php $this->session->unset_userdata('success'); ?>
