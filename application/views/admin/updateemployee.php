<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <?php if(isset($sucess) && $sucess !==0) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $sucess . '</strong>
            </div>';
            }
            ?>
            <?php if(isset($error) && $error !==0) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $error . '</strong>
            </div>';
            }
            ?>

         <?php foreach ($employee as $key => $value) { ?>
         <div class="col-lg-12">
            <h1 class="page-header">
               <?php echo $value['employee_id']?>
            </h1>
            <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i> Employee
               </li>
            </ol>
         </div>
      </div>
      <form class="form-horizontal" action="<?php echo base_url()?>employees/update/<?php echo $value['id'] ?>" method="POST" >
         <div class="form-group <?php if( form_error('employee_id') ) { echo 'has-error';} ?>" >
            <label for="Emplyee" class="col-md-2">Employee Name/Id</label>
            <div class="col-md-6">
               <input type="text" class="form-control"  name="employee_id" value = "<?php  echo $value['employee_id']; ?>" placeholder = "Employee Name" >
               <?php echo form_error('employee_id'); ?>
            </div>
         </div>
         <div class="form-group <?php if( form_error('employee_password') ) { echo 'has-error';} ?>" >
            <label for="Emplyee" class="col-md-2">Change Employee Password</label>
            <div class="col-md-6">
               <input type="password" class="form-control"  name="employee_password"  placeholder = "Employee Password" >
               <?php echo form_error('employee_password'); ?>
            </div>
         </div>
 
         <div class="form-group">
            <div class="col-md-8">
               <label for="right"> Employee Authority Here : </label> 
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-2">
               <label for="right"> Process Screens : </label> 
            </div>
            <div class="col-md-8">
                <label for="Addhandyman" >
            <input type="checkbox" name="right_for_add_handyman" <?php if(isset($value['right_for_add_handyman'])) { if($value['right_for_add_handyman'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Add handyman &nbsp;&nbsp;
            </label>
            <label for="activate/deactive" >
            <input type="checkbox" name="right_for_activate_deactivate_handyman"<?php if(isset($value['right_for_activate_deactivate_handyman'])) { if($value['right_for_activate_deactivate_handyman'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Activate/Deactivate handyman &nbsp;&nbsp;
            </label>
             <label for="delete" >
            <input type="checkbox" name="right_for_delete" <?php if(isset($value['right_for_delete'])) { if($value['right_for_delete'] == "1" ){ echo "checked" ; } } ?>   value ="1">    Delete Handyman &nbsp;&nbsp;
             </label>
            <label for="import Xls handyman" >
            <input type="checkbox" name="right_for_xls_for_handyman" <?php if(isset($value['right_for_xls_for_handyman'])) { if($value['right_for_xls_for_handyman'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Import Xls file for handyman &nbsp;&nbsp;
            </label>
            <label for="import Xls handyman" >
            <input type="checkbox" name="right_for_approve_handyman" <?php if(isset($value['right_for_approve_handyman'])) { if($value['right_for_approve_handyman'] == "1" ){ echo "checked" ; } } ?>   value ="1">   Approve New Handyman &nbsp;&nbsp;
            </label>
             <label for="verify" >
            <input type="checkbox" name="right_for_verifyhandyman" <?php if(isset($value['right_for_verifyhandyman'])) { if($value['right_for_verifyhandyman'] == "1" ){ echo "checked" ; } } ?>   value ="1">    Verify Handyman &nbsp;&nbsp;
            </label>
               </div>
         </div>
         <div class="form-group">
            <div class="col-md-2">
               <label for="right"></label> 
            </div>
            <div class="col-md-8">
               <label for="Add service" >
               <input type="checkbox" name="right_for_add_service" <?php if(isset($value['right_for_add_service'])) { if($value['right_for_add_service'] == "1" ){ echo "checked" ; } } ?> value ="1"> Add service &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               </label>
               <label for="activate/deactive" >
               <input type="checkbox" name="right_for_activate_deactivate_service" <?php if(isset($value['right_for_activate_deactivate_service'])) { if($value['right_for_activate_deactivate_service'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Activate/Deactivate Service 
               </label>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-2">
               <label for="right"> Employee  : </label> 
            </div>
            <div class="col-md-8">
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_add_employee" <?php if(isset($value['right_for_add_employee'])) { if($value['right_for_add_employee'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Create Employee &nbsp;&nbsp;
               </label>
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_update_employee" <?php if(isset($value['right_for_update_employee'])) { if($value['right_for_update_employee'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Update Employee &nbsp;&nbsp;
               </label>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-2">
               <label for="right"> Message  : </label> 
            </div>
            <div class="col-md-8">
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_report_messgae" <?php if(isset($value['right_for_report_messgae'])) { if($value['right_for_report_messgae'] == "1" ){ echo "checked" ; } } ?>   value ="1">  Edit Reoprt Message &nbsp;&nbsp;
               </label>
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_signup_message" <?php if(isset($value['right_for_signup_message'])) { if($value['right_for_signup_message'] == "1" ){ echo "checked" ; } } ?>   value ="1">   Edit Signup Message &nbsp;&nbsp;
               </label>
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_review_message" <?php if(isset($value['right_for_review_message'])) { if($value['right_for_review_message'] == "1" ){ echo "checked" ; } } ?>   value ="1">   Edit Review Message &nbsp;&nbsp;
               </label>
               <label for="Addhandyman" >
               <input type="checkbox" name="right_for_popularsearch" <?php if(isset($value['right_for_popularsearch'])) { if($value['right_for_popularsearch'] == "1" ){ echo "checked" ; } } ?>   value ="1">    Popular Search Keyword &nbsp;&nbsp;
               </label>
            </div>
         </div>

          <div class="form-group">
            <div class="col-md-2">
               <label for="right">Operation Review  : </label> 
            </div>
            <div class="col-md-8">
             <label for="Addhandyman" >
               <input type="checkbox" name="right_for_review" <?php if(isset($value['right_for_review'])) { if($value['right_for_review'] == "1" ){ echo "checked" ; } } ?>   value ="1">   Review &nbsp;&nbsp;
               </label>
            </div>
         </div>
         <div class="form-group">
            <div class="col-md-10">
               <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
            </div>
         </div>
      </form>
      <?php } ?>
      <!--  end of conatiner-->
   </div>
</div>
