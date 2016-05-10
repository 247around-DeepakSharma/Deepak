
<?php $offset = $this->uri->segment(4); ?>
<div id="page-wrapper">
  <div class="se-pre-con"></div>
    <div class="container-fluid">

        <!--<?php if($this->session->userdata('success')) {
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
            ?>-->
            
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
       <?php if(!empty($paginglinks)){?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div><?php }?>
      <center><div id="loading" >
         <?php if(is_array($handyman) && sizeof($handyman)>0){ ?>
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
                    <th>Service on Call</th>
                    <th>Image Process</th>
                    <th>Verified By</th>
                    <th>Status</th>
                    
                    
                    <th style="text-align: center;"colspan="3" >Action</th>
                </tr>
            </thead>
            <?php  foreach($handyman as $gethandyman ) { ?>
            <tbody>
                <tr id="table_<?php echo $gethandyman['id']; ?>" class=" ">
                    <td><?php echo $gethandyman['id']?></td>
                    <td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $gethandyman['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
                    <td id="name_<?php echo $gethandyman['id'];?>"><?php echo $gethandyman['name']?></td>
                    <td><?php echo $gethandyman['phone']?></td>
                    <td><?php if(isset($gethandyman['services'])) {echo $gethandyman['services'];}?></td>
                    <td><?php echo $gethandyman['address']?></td>
                    <td><?php echo $gethandyman['experience']; ?></td>
                    <td><?php echo $gethandyman['Rating_by_Agent']; ?></td>
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
                     <td  id="verified_by"><?php echo $gethandyman['verify_by'];?></td>
                    <td id="status_<?php echo $gethandyman['id']; ?>"><?php if($gethandyman['verified']==1){ if($gethandyman['approved'] ==1) {if($gethandyman['action']==1) { echo "active";}else { echo "Inactive";}} else{ echo "verified";}} else {echo "unverified";} ?></td>
                    
                     <?php  if($this->session->userdata('add handyman')== 1 || $this->session->userdata('userType')=='admin' ){ ?>
                    <td>
                       <a class="btn btn-small btn-success btn-sm"  href="<?php echo base_url();?>handyman/update/<?php echo $gethandyman['id'];?>/<?php if(!empty($offset)){ echo $offset; } else { echo "0"; }?>?tab=home">Edit</a>
                    </td>
                    <?php } if($gethandyman['verified'] ==1) {?>

                            <?php  if($gethandyman['approved'] ==1) {?>

                            <?php if($gethandyman['action']==1){?>
                             <?php  if($this->session->userdata('activate/deactivate')== 1 || $this->session->userdata('userType')=='admin' ){ ?>
                             <td id="statusbutton_<?php echo $gethandyman['id']; ?>">
                                 <button class="btn btn-small btn-info btn-sm" onclick="activate(<?php echo $gethandyman['id'];?>)">Deactivate</button>
                             </td>
                              <?php  } }else if($gethandyman['action']==0){?>

                              <?php  if($this->session->userdata('activate/deactivate')== 1  || $this->session->userdata('userType')=='admin'){ ?>
                              <td id="statusbutton_<?php echo $gethandyman['id']; ?>">
                                <button class="btn btn-small btn-primary btn-sm" onclick="deactivate(<?php echo $gethandyman['id'];?>)">Activate</button>
                              </td>
                               <?php } } }else  {?>

                              <?php  if($this->session->userdata('approvehandyman')== 1 || $this->session->userdata('userType')=='admin'){ ?>
                             <td id="statusbutton_<?php echo $gethandyman['id']; ?>">
                                <button class="btn btn-small btn-danger btn-sm" onclick="approvefilter(<?php echo $gethandyman['id'];?>)">Approve</button>
                             </td>
                             <?php } } }else  {?>
                          <?php  if($this->session->userdata('verify')== 1 || $this->session->userdata('userType')=='admin' ){ ?>

                           <td id="statusbutton_<?php echo $gethandyman['id']; ?>">
                            <button class="btn btn-small btn-danger btn-sm" onclick="verify(<?php echo $gethandyman['id'];?>)">Verify</button>
                           </td>
                           <?php } }?>
                          <?php  if($this->session->userdata('deletehandyman')== 1 || $this->session->userdata('userType')=='admin'){ ?>
                          <td>
                             <button class="btn btn-small btn-danger btn-sm" onclick="deletehandyman(<?php echo $gethandyman['id'];?>)">Delete</button>
                          </td>
                          <?php  }?>
                <tr> 
                    <?php  }?>
            </tbody>
        </table>
        <?php if(!empty($paginglinks)){?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div><?php }?>
        <?php }else{echo "Record Not Found";}?>
    </div></center>
       
  
    </div>
</div>


<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
