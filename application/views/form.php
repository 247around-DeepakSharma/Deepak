<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <?php if(isset($error) && $error !==0) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $error . '</strong>
            </div>';
            }
            ?>
            <?php echo validation_errors(); ?>

         <!--  api  -->
         <div class="col-lg-12">
            <form action="<?php echo base_url()?>api" method="POST">
               <textarea rows="20" cols="100" name="token" >
               BODY ....</textarea><br/>
               <input type ="submit" value="Submit Query">
               <br/><br/><br/><br/><br/><br/>
            </form>
         </div>
      </div>

      <!-- End---->
      <div class="row">
         <div class="col-lg-4">
            <form action="<?php base_url()?>form/getshandyman"  >
               <input type="text" name="id" placeholder="ID"><br/>
               <input type="submit"  value="Enter Id">
            </form>
         </div>
         <div class="col-lg-4">
            <form action="<?php base_url()?>form/searchAddress" method="POST">
               <input type="text" name="search" placeholder="Search address"><br/>
               <input type="submit"  value="Search Address">
            </form>
         </div>
         <div class="col-lg-4">
            <form action="<?php base_url()?>form/getallhandyman" method="POST">
               <input type="text" name="firstid" placeholder="Start Id"><br/>
               <input type="text" name="lastid" placeholder="End Id"><br/>
               <input type="submit"  value="Enter Id">
            </form>
         </div>
      
          <div class="col-lg-4">
            <form action="<?php base_url()?>form/searchservice" method="POST">
               <input type="text" name="service" placeholder="Search services from elasticsearch" style="width:62%"><br/>
               <input type="submit" value="Search services from elasticsearch">
            </form>
         </div>
      </div>
   </div>
</div>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Add Handyman
            </h1>
            <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-edit"></i> handyman
               </li>
            </ol>
            <form class="form-horizontal" action="<?php echo base_url()?>form/handyman" method="POST"  enctype="multipart/form-data">
               <div class="form-group">
                  <label for="institute" class="col-md-2">Name</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="name"  required>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                  <label for="institute" class="col-md-2">phone</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="phone"  required>
                     <?php echo form_error('phone'); ?>
                  </div>
               </div>
               <div class="form-group">
                  <label for="institute" class="col-md-2">Service</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="service"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="institute" class="col-md-2">Profile Photo</label>
                  <div class="col-md-6">
                     <input type="file" class="form-control"  name="file"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="institute" class="col-md-2">Address</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="address"   required>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('experience') ) { echo 'has-error';} ?>">
                  <label for="institute" class="col-md-2">Experience</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="experience"   required>
                     <?php echo form_error('experience'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('age') ) { echo 'has-error';} ?>">
                  <label for="institute" class="col-md-2">Age</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="age"   required>
                     <?php echo form_error('age'); ?>
                  </div>
               </div>
               <div class="form-group">
                  <label for="is_paid" >
                  <input type="checkbox" name="paid" value ="1"> Is Paid
                  </label>
                  <label for="Passport" >
                  <input type="checkbox" name="passport" value ="Yes"> Passport
                  <label for="id" >
                     <input type="checkbox" name="identity" value ="Yes"> Identity
               </div>
               <div class="form-group">
               <label for="when you work prefer" >
               when you Prefer work 
               <input type="checkbox" name="work_on_weekdays" value ="weekdays">  weekdays
               </label>
               <input type="checkbox" name="weekends" value ="weekends">  weekends
               </div>
               <div class="form-group">
                  <label for="Service On call" >
                  Service On Call
                  <input type="checkbox" name="service_on_call" value ="Yes">  Yes
                  </label>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Add User Profile
            </h1>
            <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-edit"></i> <a href="<?php echo base_url()?>form/user">All User</a>
               </li>
            </ol>
            <form class="form-horizontal" action="<?php base_url()?>form/user_profile" method="POST"  >
               <div class="form-group">
                  <label for="Name<" class="col-md-2">Name</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="name" required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="Name<" class="col-md-2">User ID</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="userid" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
               </form>
                  <form class="form-horizontal" action="<?php base_url()?>form/userprofilePhone" method="POST"  >
               <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                  <label for="phone" class="col-md-2">phone</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="phone" >
                  </div>
                  <?php echo form_error('phone'); ?>
               </div>
                <div class="form-group">
                  <label for="Name<" class="col-md-2">User ID</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="userid" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
            <form class="form-horizontal" action="<?php base_url()?>form/userprofileAddress" method="POST"  >
               <div class="form-group">
                  <label for="home Address" class="col-md-2">Home Address</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="home_address"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="Name<" class="col-md-2">User ID</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="userid" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
            <form class="form-horizontal" action="<?php base_url()?>form/userprofileOfficeAddress" method="POST"  >
               <div class="form-group">
                  <label for="office address" class="col-md-2">Office Address</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="office_address"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="Name<" class="col-md-2">User ID</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="userid" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Add Handyman Review
            </h1>
            <form class="form-horizontal" action="<?php base_url()?>form/handymanReview" method="POST"  >
               <div class="form-group <?php if( form_error('behaviour') ) { echo 'has-error';} ?>">
                  <label for="Behavior" class="col-md-2">Behaviour</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="behaviour" required>
                     <?php echo form_error('behaviour'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('expertise') ) { echo 'has-error';} ?>">
                  <label for="Expertise" class="col-md-2">Expertise</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="expertise"  required>
                     <?php echo form_error('expertise'); ?>
                  </div>
               </div>
               <div class="form-group">
                  <label for="Review" class="col-md-2">Review</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="review"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="handyman_id" class="col-md-2">Handyman id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="handyman_id"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="user_id" class="col-md-2">User id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="user_id"   required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <div class="row">
         <h1 class="page-header">
            Caluculate differnce between Latitude and Longitude (in Kilometer):
         </h1>
         <div class="col-lg-6">
            <form class="form-horizontal" action="<?php base_url()?>form/SearchIdCalculate" method="POST"  >
               <div class="form-group">
                  <label for="handyman" class="col-md-2">handyman Id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="handymanid"   required>
                  </div>
               </div>
               <h3>First location (in decimal)</h3>
               <div class="form-group">
                  <label for="Latitude:" class="col-md-2">Latitude:</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="lat1"   required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="Latitude:" class="col-md-2">Longitude</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="lon1"   required>
                  </div>
               </div>
              <!-- <div class="form-group">
                  <label>UNIT</label>
                  <p><strong>For Kilometers ="k" & For Miles = "m" $ for Nautical Miles ="n"</strong></p>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="unit"   required>
                  </div>
               </div>-->
         </div>
         <div class="col-lg-6">
            <h3>Second location (in decimal)</h3>
            <div class="form-group">
              <label for="Latitude:" >Latitude</label>
               <div class="col-md-6">
                  <input type="text" class="form-control"  name="lat2"   required>
               </div>
            </div>
            <div class="form-group">
                <label for="Latitude:" >Longitude</label>
               <div class="col-md-6">
                 <input type="text" class="form-control"  name="lon2"   required>
               </div>
            </div>
         </div>
        <!-- <div class="form-group">
         <label>Result :---</label>
         <div class="col-md-6">
         <input type="text" class="form-control"  value = " <?php if(isset($distance)){
            if($unit == 'k'){
            echo $distance." "."Kilometers";
            }else if ($unit == 'm') {
             echo $distance." "."Miles";
            }else if($unit == 'n') {
            echo $distance." "." Nautical Miles";
            }
            
            } ?>" >
         </div>
         </div>-->
         <div class="form-group">
             <div class="col-md-10">
                <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Calculate" style="width:33%"></center>
             </div>
         </div>
         </form>
      </div>

      <div class="row">
        <h1 class="page-header">
           handyman search Api
         </h1>
          <div class="col-lg-12">
            <form class="form-horizontal" action="<?php echo base_url() ?>form/processGetUsedSavedHandymans" method="POST"  >
               <div class="form-group">
                  <label for="Area" class="col-md-2">deviceid</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="deviceId"   >
                  </div>
               </div>
               <div class="form-group">
                  <label for="sercice" class="col-md-2">saved_type</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="saved_type"   >
                  </div>
               </div>
                   <div class="form-group ">
                  <label for="Area" class="col-md-2">sortBy</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="sortby"   >
                  </div>
                
               </div>
                <!--   <div class="form-group <?php if( form_error('lastid') ) { echo 'has-error';} ?>">
                  <label for="Area" class="col-md-2">last id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="lastid"   >
                  </div>
                   <?php echo form_error('lastid'); ?>
               </div>-->

               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Search" style="width:33%"></center>
                  </div>
               </div>
            </form>
          </div>
      </div>
      <div class="row">
         <h1 class="page-header">
            See Review
         </h1>
         <div class="col-lg-12">
            <form class="form-horizontal" action="<?php echo base_url()?>form/checkReview" method="POST">
               <div class="form-group">
                  <label for="handyman" class="col-md-2">handyman</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control" name="handyman_id" required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="user" class="col-md-2">user</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control" name="user_id" required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="check" style="width:33%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>

       <div class="row">
        <h1 class="page-header">
           handyman search Apis
         </h1>
          <div class="col-lg-12">
            <form class="form-horizontal" action="<?php echo base_url()?>form/searchHandymans" method="POST"  >
               <div class="form-group">
                  <label for="Area" class="col-md-2">area</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="area"   >
                  </div>
               </div>
               <div class="form-group">
                  <label for="sercice" class="col-md-2">service</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="service"   >
                  </div>
               </div>
               <div class="form-group">
                  <label for="sercice" class="col-md-2">searchkeyword</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="searchkeyword"   >
                  </div>
               </div>
                   <div class="form-group ">
                  <label for="Area" class="col-md-2">start</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="start"   >
                  </div>
        

               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Search" style="width:33%"></center>
                  </div>
               </div>
            </form>
          </div>
      </div>
   </div>

</div>


</div>

<script src="<?php echo base_url()?>js/jquery.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
</body>
</html>