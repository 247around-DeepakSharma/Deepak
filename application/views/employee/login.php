<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>
Welcome 
</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">

      
    <style>
     body{
      text-align:center;
      background-color:#f2f2f2;
      float:none;
    }
    .pageWrap{
      width: 350px;
      text-align: center;
      height: auto;
      float: none;
      background-color: #E6E6E6;
      padding: 20px;
      margin-top:150px;
   }  
      
   </style>
</head>
<body>
<div class="container pageWrap">
<h2>Log In</h2>
   <div class="col-xs-4 " style="width:100%;text-align:left">
       <?php if(isset($error) && $error !==0) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>Error! </strong>' . $error .
         '</div>';
         }
         ?>
   
   <form action="<?php echo base_url()?>employee/login" method="POST" role="form">
      <div class="form-group">
             <label>Employee</label>
                 <input class="form-control" type="text" name="employee_id"  placeholder="Employee  ID">
        </div>
      <div class="form-group">
             <label>Password</label>
                 <input type="password" class="form-control" name="employee_password" placeholder="Password">
        </div>
      <div class="form-group" style="text-align:center">
            <input type="submit" class="btn btn-primary">
        </div>

   </form>
  <a href="<?php echo base_url()?>admin" style="padding-right:150px;text-decoration: none;font-size: 14px;">Login Admin</a>
   </div>
    
  </div>
 
</body>
</html>
