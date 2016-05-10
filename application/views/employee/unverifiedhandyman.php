<?php $offset = $this->uri->segment(4); ?>

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
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Vendors <small></small>
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Vendors
               </li>
            </ol>
         </div>
      </div>
        <div class="input-filter-container" style="  padding: 2px 10px;"><label for="input-filter">Filter the table:</label> <input type="search" id="input-filter" size="15" placeholder="search"></div>
 <?php if(is_array($result) && sizeof($result)>0){ ?>
      <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr>
               <th>No #</th>
               <th>Profle Photo</th>
               <th>Name</th>
               <th>Phone</th>
               <th>Service</th>
               <th>Address</th>
               <th>Experience</th>
               <th>Rate By Agent</th>
               <th>Paid</th>
               <th>Service on Call</th>
             
               <th style="text-align: center;" colspan="3">Action</th>
            
            </tr>
         </thead>
         <?php foreach($result as $gethandyman) {?>
         <tbody>
            <tr>
               <td><?php echo $gethandyman['id']?></td>
               <td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $gethandyman['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
               <td><?php echo $gethandyman['name']?></td>
               <td><?php echo $gethandyman['phone']?></td>
               <td><?php if(isset($gethandyman['services'])) {echo $gethandyman['services'];}?></td>
               <td><?php echo $gethandyman['address']?></td>
               <td><?php echo $gethandyman['experience']; ?></td>
               <td><?php echo $gethandyman['Rating_by_Agent']; ?></td>
               <td><?php echo $gethandyman['is_paid']; ?></td>
               <td ><?php  if($gethandyman['service_on_call'] == "Yes") {
                  echo $gethandyman['service_on_call'] ;
                   } else {
                  echo "NO";
                  }?></td>
                 
          
               <td >
              
                     <a class="btn btn-small btn-success"  href="<?php echo base_url();?>employee/handyman/update/<?php echo $gethandyman['id'];?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?>?tab=home">Edit</a>
                    </td>
                  
                     <?php if($gethandyman['verified']==0){?>
                       <td>
                     <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>employee/handyman/verify/<?php echo $gethandyman['id'];?>">Verify</a>
                    </td>
                     <?php } if($this->session->userdata('deletehandyman')){ ?>
                   <td>
                     <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>employee/handyman/unverifieddelete/<?php echo $gethandyman['id'];?>">Delete</a>
                    </td>
                     <?php } ?>
                
               
         
               
            <tr>
             <?php }?>
         </tbody>
      </table>
       <p align="center" style="padding-top:20px;">
      <?php }else {echo "Record Not Found" ;} ?>
    </p>
     
   </div>
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.filtertable.min.js"></script>
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
   /* filter-table specific styling */
   td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
   /* special filter field styling for this example */
   .input-filter-container { position: absolute; top: 7em; right: 1em; border: 2px solid #66f; background-color: #eef; padding: 0.5em; }
</style>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
