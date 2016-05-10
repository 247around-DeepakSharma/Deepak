<?php $offset = $this->uri->segment(3); ?>
<div id="page-wrapper">
   <div class="container-fluid">
 
               <?php if($this->session->userdata('success')) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('success') . '</strong>
         </div>';
         }
         ?>
               <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Popular Search Keyword <small></small>
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Search
               </li>
            </ol>
         </div>
      </div>

        <?php if(is_array($result) && sizeof($result)>0){ ?>
      <?php if(!empty($paginglinks)){?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div><?php }?>
       <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr>
               <th>No #</th>
               <th>Search Keyword</th>
              
               <th>Action</th>

            </tr>
        </thead>
          <?php foreach($result as $key =>$search) {?>
          <tbody>
           <tr>
               <td><?php echo $search['id'];?></td>
               <td><?php echo $search['searchkeyword'];?></td>
               
               <td style="width:20%;padding:2px;vertical-align: middle;">
                  <p>
                     <a class="btn btn-small btn-success"  href="<?php echo base_url();?>employee/popularsearch/editserachkeyword/<?php echo $search['id']?>">Edit</a>
                     <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>employee/popularsearch/DeleteSearchkeyword/<?php echo $search['id']; ?><?php echo "/".$offset?>">Delete</a>
                  </p>
               </td>
           </tr>
          </tbody>
           <?php }?>
      </table>
      <?php if(!empty($paginglinks)){?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div><?php }?>
      <?php }else{?>
      <p align="center" style="padding-top:20px;">
        <?php if(!empty($offset)){ redirect(base_url()."employee/popularsearch/viewsearch");} else { echo "Record Not Found";}?>
      </p>
      <?php }?>

 <!-- container-->              
   </div>
</div>
<?php $this->session->unset_userdata('success'); ?>