<?php $offset = $this->uri->segment(3); ?>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.filtertable.min.js"></script>
<script type="text/javascript">
   $(document).ready(function(){ 
                              
       $(function() {
           $("#contentLeft").sortable({ opacity: 0.6, cursor: 'move', update: function() {
               var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
               $.post("<?php echo base_url()?>service/servicedrag", order, function(theResponse){
                 location.reload();
               });                                                              
           }                                 
           });
       });
   
   }); 
</script>
<script>
   $(function(){
   
     $('#dynamic_select').bind('change', function () {
         var url = $(this).val(); 
         if (url) {
             window.location = url; 
         }
         return false;
     });
   });
</script>
<div id="page-wrapper">
   <div class="container-fluid">
      <?php if($this->session->userdata('success')) {
         echo '<div class="alert alert-success alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('success') . '</strong>
         </div>';
         }
         ?>
      <?php if($this->session->userdata('error')) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('error') . '</strong>
         </div>';
         }
         ?>
      <div id="success"></div>
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               List of Services 
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Service
               </li>
            </ol>
         </div>
      </div>
      <div class="col-lg-12">
         <?php if(is_array($result) && sizeof($result)>0){ ?>
         <?php if(!empty($paginglinks)) {?>
         <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
         <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
         <?php } ?>
         <div class="pagination">
            <select id="dynamic_select">
               <option value="<?php echo base_url().'service/viewservices'?>" <?php if($this->uri->segment(4) == 10){ echo 'selected';}?>>10</option>
               <option value="<?php echo base_url().'service/viewservices/0/30'?>" <?php if($this->uri->segment(4) == 30){ echo 'selected';}?>>30</option>
               <option value="<?php echo base_url().'service/viewservices/0/50'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
               <option value="<?php echo base_url().'service/viewservices/0/100'?>" <?php if($this->uri->segment(4) == 100){ echo 'selected';}?>>100</option>
               <?php if ($this->uri->segment(4)){if($this->uri->segment(4) != 10 || $this->uri->segment(4) !==30 || $this->uri->segment(4) != 50 || $this->uri->segment(4) != 100  ){?>
               <option value="" <?php if($this->uri->segment(4) == count($result)){ echo 'selected';}?>><?php echo $this->uri->segment(4);?></option>
               <?php } }?>
            </select>
         </div>

<div class="input-filter-container"   ><label for="input-filter">Filter the table:</label> <input type="search" id="input-filter" size="15" placeholder="search"></div>
      </div>
      <table class="table table-bordered table-hover table-striped data" id="search_list" >
         <thead>
            <tr>
               <th>ID</th>
               <th>Icon</th>
               <th>Service</th>
               <th>Distance</th>
               <th>keywords</th>
               <th>Status</th>
               <th colspan="3" style="text-align:center">Action</th>
            </tr>
         </thead>
         <tbody id="contentLeft" >
            <?php foreach($result as $key =>$row) {?>
            <tr id="recordsArray_<?php echo $row['id']."|".$row['priority']; ?>">
               <td  ><?php echo $row['id'] ;?></td>
               <td ><img src="https://d28hgh2xpunff2.cloudfront.net/service-320x252/<?php echo $row['service_image'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
               <td><?php echo $row['services']?></td>
               <td><?php echo $row['distance']?></td>
               <td><?php echo $row['keywords']?></td>
               <td><?php if($row['action'] ==0){echo "Inactive";} else { echo "Active";}?></td>
               <td>
                  <a class="btn btn-small btn-success"  href="<?php echo base_url();?>service/updateService/<?php echo $row['id'] ?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?><?php if(!empty($this->uri->segment(4))){ echo "/".$this->uri->segment(4); } ?>">Edit</a>
               </td>
               <?php if($row['action']==1){?>
               <td>
                  <a class="btn btn-small btn-info"  href="<?php echo base_url();?>service/inactive/<?php echo $row['id'] ?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?><?php if(!empty($this->uri->segment(4))){ echo "/".$this->uri->segment(4); } ?>">Deactivate</a>
               </td>
               <?php } else if($row['action']==0){?>
               <td>
                  <a class="btn btn-small btn-primary"  href="<?php echo base_url();?>service/ActivateService/<?php echo $row['id'] ?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?><?php if(!empty($this->uri->segment(4))){ echo "/".$this->uri->segment(4); } ?>">Activate</a>
               </td>
               <?php } ?>
               <td>
                  <a class="btn btn-small btn-danger"  onclick="deleteservice(<?php echo $row['id']?>)"  href="#">Delete</a>
               </td>
            <tr>
               <?php }?>
         </tbody>
      </table>
      <div class="col-lg-12">
         <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
         <div class="pagination" style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
         <?php }else{?>
         <p align="center" style="padding-top:20px;">
            <?php if(!empty($offset)){ redirect(base_url()."service/viewservices");} else { echo "Record Not Found" ;}?>
         </p>
         <?php }?>
      </div>
   </div>
</div>
<script>
   $(document).ready(function() {
       $('table').filterTable({ // apply filterTable to all tables on this page
           inputSelector: '#input-filter' // use the existing input instead of creating a new one
       });
   });
</script>
<style>
   /* generic table styling */
   table { border-collapse: collapse; }
   th, td { padding: 5px; }
   th { border-bottom: 2px solid #999; background-color: #eee; vertical-align: bottom; }
   td { border-bottom: 1px solid #ccc; }
   td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
   /* special filter field styling for this example */
   .input-filter-container { position: absolute; top: -8em; right: 1em; border: 2px solid #66f; background-color: #eef; padding: 0.5em; }
</style>
<script>
   /*function deleteservice(id) {
       
       if (confirm("Are You Sure!") == true) {
           $.ajax({
                type: 'POST', 
                url: '<?php echo base_url();?>service/delete/'+id+'/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?><?php if(!empty($this->uri->segment(4))){ echo "/".$this->uri->segment(4); } ?>', 
                success :function(response){
                   location.reload(); 
                   //$( "#success" ).addClass( "alert alert-success alert-dismissible" );
                  // var x = "<strong>Delete Sucessfully</strong>";
                  // document.getElementById("success").innerHTML= x;
                 }
                // complete:function(response){
                    //setTimeout(function(){
                   // location.reload();  
                   //}, 1000);
                // }
               
               });
          
          }
      }*/
   
       function deleteservice(id){
      if (confirm("Are You Sure!") == true) {
           $.ajax({ 
             type: 'POST', 
              url: '<?php echo base_url();?>service/delete/'+id+'/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?><?php if(!empty($this->uri->segment(4))){ echo "/".$this->uri->segment(4); } ?>', 
             success: function(){
              location.reload();
               
             } 
           });
   
          }
           } 
              
         
</script>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
</body>
</html>
