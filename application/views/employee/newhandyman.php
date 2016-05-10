<?php $offset = $this->uri->segment(4);$page= $this->uri->segment(5);?>
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
      <div id="msgdisplay"></div>
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
      <?php if(is_array($result) && sizeof($result)>0){ ?>
            <?php if(!empty($paginglinks)) {?>
            <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
            <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
            <?php } ?>
      <div class="pagination">
                <select id="dynamic_select">
                    <option value="<?php echo base_url().'employee/handyman/verifiedhandyman'?>" <?php if($this->uri->segment(3) == 10){ echo 'selected';}?>>10</option>
                    <option value="<?php echo base_url().'employee/handyman/verifiedhandyman/0/30'?>" <?php if($this->uri->segment(5) == 30){ echo 'selected';}?>>30</option>
                    <option value="<?php echo base_url().'employee/handyman/verifiedhandyman/0/50'?>" <?php if($this->uri->segment(5) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/handyman/verifiedhandyman/0/100'?>" <?php if($this->uri->segment(5) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/handyman/Allverified'?>" <?php if($this->uri->segment(3) == 'Allverified'){ echo 'selected';}?>>All</option>
                    <?php if ($this->uri->segment(5)){if($this->uri->segment(5) != 10 || $this->uri->segment(5) !==30 || $this->uri->segment(5) != 50 || $this->uri->segment(5) != 100  ){?>
                    <option value="" <?php if($this->uri->segment(5) == count($result)){ echo 'selected';}?>><?php echo $this->uri->segment(5);?></option>
                    <?php } }?>
                </select>
            </div>
      <div class="input-filter-container"   ><label for="input-filter">Filter the table:</label> <input type="search" id="input-filter" size="15" placeholder="search"></div>
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
               <th>Image Process</th>
               <th>Status</th>
               <th style="text-align: center;" colspan="3">Action</th>
            </tr>
         </thead>
         <?php foreach($result as $key =>$gethandyman) {?>
         <tbody>
            <tr id="table_<?php echo $gethandyman['id']; ?>" class=" ">
               <td><?php echo $gethandyman['id']?></td>
               <td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $gethandyman['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
               <td id="name_<?php echo $gethandyman['id'];?>" ><?php echo $gethandyman['name']?></td>
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
               <td ><?php  if($gethandyman['image_processing'] == "1") {
                  echo "Yes";
                  } else {
                  echo "NO";
                  }?></td>
               <td id="status_<?php echo $gethandyman['id']; ?>"><?php echo "unverified"; ?></td>
               <td >
                  <a class="btn btn-small btn-success btn-sm"  href="<?php echo base_url();?>employee/handyman/update/<?php echo $gethandyman['id'];?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?>?tab=home">Edit</a>
               </td>
               <?php  if($gethandyman['approved'] ==0) {?>
               <td id="statusbutton_<?php echo $gethandyman['id']; ?>"> <a class="btn btn-small btn-danger btn-sm" href="<?php echo base_url();?>employee/handyman/approve/<?php echo $gethandyman['id'];?><?php echo "/".$offset?><?php echo "/".$page?>">Approve</a></td>
                <?php } if($this->session->userdata('deletehandyman')){ ?>
               <td> <button class="btn btn-small btn-danger btn-sm" onclick="deletehandyman(<?php echo $gethandyman['id'];?>)">Delete</button></td>
               <?php } ?>
            <tr>
               <?php }?>
         </tbody>
      </table>
      <div class="pagination" style="float:right;"> <?php if(isset($paginglinks)) echo $paginglinks; ?></div>
      <div class="pagination" style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
      <?php }else{?>
      <p align="center" style="padding-top:20px;">
         <?php if(!empty($offset)){ redirect(base_url()."employee/handyman/viewhandyman");} else { echo "Record Not Found";}?>
      </p>
      <?php }?>
   </div>
</div>
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
   .input-filter-container {
   position: absolute;
   top: 7em;
   right: 1em;
   border: 2px solid #66f;
   background-color: #eef;
   padding: 0.5em;
   }
</style>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
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

