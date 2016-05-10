<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Admin </title>
        <!-- Bootstrap Core CSS -->
        <link href="<?php echo base_url()?>css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="<?php echo base_url()?>css/sb-admin.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="<?php echo base_url()?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <script src="<?php echo base_url()?>js/jquery.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="<?php echo base_url()?>js/bootstrap.min.js"></script>
        <link href="<?php echo base_url()?>css/select2.css" rel="stylesheet" />
        <link href="<?php echo base_url()?>css/select2-bootstrap.css" rel="stylesheet" />
        <script src="<?php echo base_url();?>js/select2.js"></script>
        <script src="<?php echo base_url();?>js/myjs.js"></script>
        
       
        <!--<script src="<?php echo base_url();?>js/jquery-1.11.1.min.js"></script>
            < <script src="<?php echo base_url();?>js/jquery.rowsorter.js"></script>-->
    </head>
    <body>
        <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo base_url()?>admin/dashboard">Hello Admin</a>
            </div>
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user"></i> <?php echo $this->session->userdata('email'); ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo base_url()?>admin/reset_password"><i class="fa fa-key"></i> Change Password</a>
                         </li>
                         <li>
                     
                        </ul>
                </li>
                <li>
                    <a href="<?php echo base_url()?>admin/logout"><i class="fa fa-fw fa-power-off"></i></a>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                <li>
                    <a href="javascript:;" data-toggle="collapse" data-target="#filter"><i class="fa fa-fw fa-arrows-v"></i> Filter <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="filter" class="collapse">
                        <li id="sideview">
                            Services
                        </li>
                        <li>
                            <ul   class="facestyle" style=" height:257px;" >
                                <?php foreach ($service as $key => $value) {?>
                                <li class="face" >
                                    <input onclick="getValue()" type="checkbox" name="service[]" value="<?php echo $value['id']?>">  <?php echo $value['services'] ;?>
                                </li>
                                <?php  }?>
                            </ul>
                        </li>
                        <li id="sideview">
                            Experience
                        </li>
                        <li>
                            <ul   class="facestyle" style="height:80%">
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name="experience[]" value="0-5"> 0-5
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name = "experience[]" value="6-10"> 6-10
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name = "experience[]" value="11-15"> 11-15
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name = "experience[]" value="16-20">  16-20
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name = "experience[]" value="21-25"> 21-25
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name = "experience[]" value="25-50"> Greater than 25
                                </li>
                            </ul>
                        </li>
                        <li id="sideview">
                            Rating By Agent
                        </li>
                        <ul   class="facestyle" style="height:80%">
                            <li class="face">
                                <input onclick="getValue()" type="checkbox" name="Rating_by_Agent[]" value="Good"> Good
                            </li>
                            <li class="face" >
                                <input onclick="getValue()" type="checkbox" name="Rating_by_Agent[]"  value="Average"> Average
                            </li>
                            <li class="face" >
                                <input onclick="getValue()" type="checkbox" name="Rating_by_Agent[]"  value="Exceptional">  Exceptional
                            </li>
                            <li class="face" >
                                <input onclick="getValue()" type="checkbox" name="Rating_by_Agent[]"  value="Bad"> Bad
                            </li >
                            <li class="face" >
                                <input  onclick="getValue()" type="checkbox" name="Rating_by_Agent[]"  value="Very Bad">  Very Bad
                            </li>
                        </ul>
                        <li id="sideview">
                            Status
                        </li>
                        <li>
                            <ul   class="facestyle" style="height:80%">
                                <li class="face" >
                                    <input onclick="getValue()" type="checkbox" name="action[]" value="1">    Activated
                                </li>
                                <li class="face" >
                                    <input onclick="getValue()" type="checkbox" name="action[]" value="0">    DeActivated
                                </li>
                                <li class="face" >
                                    <input onclick="getValue()" type="checkbox" name="approved[]" value="1">    Verified
                                </li>
                                <li class="face" >
                                    <input onclick="getValue()" type="checkbox" name="verified[]" value="0">    Not verify
                                </li>
                            </ul>
                        </li>
                        <li id="sideview">
                            Serive On Call
                        </li>
                        <li>
                            <ul   class="facestyle" style="height:80%">
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name="service_on_call[]" value="Yes"> Yes
                                </li>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name="service_on_call[]"  value="No"> No
                                </li>
                            </ul>
                        </li>
                        </li>
                        <li id="sideview">
                            TeleCaller
                        </li>
                        <li>
                            <ul   class="facestyle" style="height:80%">
                                <?php foreach ($agent as $value) {?>
                                <li class="face">
                                    <input onclick="getValue()" type="checkbox" name="telecaller[]" value="<?php echo $value['employee_id']?>"> <?php echo $value['employee_id'];?>
                                </li>
                                <?php  }?>
                            </ul>
                        </li>
                        <!--    <li id="sideview">
                            Work On weedays
                            </li>
                            <li>
                            <ul class="facestyle" style="height: 94px;">
                               <li class="face" >
                                  <input onclick="getValue()" type="checkbox" name="work_on_weekdays[]" value="Yes"> Yes
                               </li>
                               <li class="face">
                                  <input onclick="getValue()" type="checkbox" name="work_on_weekdays[]"  value="No"> No
                               </li>
                            </ul>
                            </li>
                            <li id="sideview">
                            Work on Weekends
                            </li>
                            <ul class="facestyle"  style="height: 94px;" >
                            <li class="face" >
                               <input onclick="getValue()" type="checkbox" name="work_on_weekdays[]" value="Yes"> Yes
                            </li>
                            <li class="face" style="  padding-bottom: 12px;">
                               <input onclick="getValue()" type="checkbox" name="work_on_weekdays[]"  value="No"> No
                            </li>
                            </ul> -->
                    </ul>
                </li>
                <li <?php if($this->uri->uri_string()=='employees' || $this->uri->uri_string()=='employees/viewemployee' ){ echo 'class="active"';}?>>
                    <a href="javascript:;" data-toggle="collapse" data-target="#employee"><i class="fa fa-fw fa-arrows-v"></i> Employee <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="employee" class="collapse">
                        <li  <?php if($this->uri->uri_string()=='employees'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>employees"><i class="fa fa-fw fa-user"></i> Create Employee</a>
                        </li>
                        <li  <?php if($this->uri->uri_string()=='employees/viewemployee'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>employees/viewemployee"><i class="fa fa-fw fa-user"></i>  View Employee</a>
                        </li>
                        <li  <?php if($this->uri->uri_string()=='employees/approvebyemployeehandyman'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>employees/approvebyemployeehandyman"><i class="fa fa-fw fa-user"></i>   Approved List</a>
                        </li>
                        <li  <?php if($this->uri->uri_string()=='employees/verifylist'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>employees/verifylist"><i class="fa fa-fw fa-user"></i>       Verify List</a>
                        </li>
                    </ul>
                </li>
                <li <?php if($this->uri->uri_string()=='handyman' || $this->uri->uri_string()=='handyman/verifiedhandyman'||$this->uri->uri_string()=='excel/download_handyman' || $this->uri->uri_string()=='handyman/viewFromApps' || $this->uri->uri_string()=='handyman/viewhandyman' || $this->uri->uri_string()=='handyman/newhandyman' || $this->uri->uri_string()=='excel' || $this->uri->uri_string()=='admin/dashboard' || $this->uri->uri_string()=='admin'){ echo 'class="active"';}?>>
                    <a href="javascript:;" data-toggle="collapse" data-target="#demo1"><i class="fa fa-fw fa-arrows-v"></i> Handyman <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="demo1" class="collapse">
                        <li >
                            <a href="<?php echo base_url()?>handyman"><i class="fa fa-fw fa-edit"></i> Add Handyman</a>
                        </li>
                        <li> 
                            <a href="<?php echo base_url()?>handyman/viewhandyman"><i class="fa fa-fw fa-desktop"></i>   View Handyman</a>
                        </li>
                        <li> 
                            <a href="<?php echo base_url()?>handyman/verifiedhandyman"><i class="fa fa-fw fa-desktop"></i>  Verified Handyman</a>
                        </li>
                        <li >
                            <a href="<?php echo base_url()?>excel"><i class="fa fa-file-excel-o"></i>  &nbsp; Import Excel File</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url()?>excel/download_handyman"><i class="fa fa-file-excel-o"></i>  &nbsp; Export Excel File</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url()?>handyman/viewFromApps"><i class="fa fa-file-excel-o"></i>  &nbsp; Handyman From Apps</a>
                        </li>
                    </ul>
                </li>
                <li  <?php if($this->uri->uri_string()=='service'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>service"><i class="fa fa-fw fa-search"></i> Add Services</a>
                </li>
                <li <?php if($this->uri->uri_string()=='service/viewservices'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>service/viewservices"><i class="fa fa-shield"></i> View Services</a>
                </li>
                <li <?php if($this->uri->uri_string()=='user/viewuser'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>user/viewuser"><i class="fa fa-fw fa-user"></i>  All User</a>
                </li>
                <li <?php if($this->uri->uri_string()=='review/messgae' || $this->uri->uri_string()=='review/messgae' || $this->uri->uri_string()=='signup_message' || $this->uri->uri_string()=='report_message' ||$this->uri->uri_string()=='sharetext'){ echo 'class="active"';}?>>
                    <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Message <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="demo" class="collapse">
                        <li <?php if($this->uri->uri_string()=='review/messgae'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>review/messgae"><i class="fa fa-sign-in"> </i>     Review Message</a>
                        </li>
                        <li <?php if($this->uri->uri_string()=='signup_message'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>signup_message"><i class="fa fa-sign-in"> </i>     Signup Message</a>
                        </li>
                        <li <?php if($this->uri->uri_string()=='report_message'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>report_message"><i class="fa fa-sign-in"> </i>     Report Message</a>
                        </li>
                        <li <?php if($this->uri->uri_string()=='sharetext'){ echo 'class="active"';}?>>
                            <a href="<?php echo base_url()?>sharetext"><i class="fa fa-share"></i>  Share Button Text</a>
                        </li>
                    </ul>
                </li>
                <li <?php if($this->uri->uri_string()=='popularsearch'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>popularsearch"><i class="fa fa-fw fa-search"></i> Add Popular Search</a>
                </li>
                <li <?php if($this->uri->uri_string()=='popularsearch/viewsearch'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>popularsearch/viewsearch"><i class="fa fa-eye"></i> View Popular Search</a>
                </li>
                <li  <?php if($this->uri->uri_string()=='review/viewReview'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>review/viewReview"><i class="fa fa-eye"> </i>   Review</a>
                </li>
                <li <?php if($this->uri->uri_string()=='ads'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>ads"><i class="fa fa-sign-in"> </i>     Add Advertise</a>
                </li>
                <li <?php if($this->uri->uri_string()=='ads/view'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>ads/view"><i class="fa fa-sign-in"> </i>     View Advertise</a>
                </li>
                <li <?php if($this->uri->uri_string()=='user'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>user"><i class="fa fa-bug"> </i>      User Report</a>
                </li>
                <li <?php if($this->uri->uri_string()=='marketingmail/marketing_mail_message'){ echo 'class="active"';}?>>
                    <a href="<?php echo base_url()?>marketingmail/marketing_mail_message"><i class="fa fa-envelope-o"></i>    Marketing Mail Message</a>
                </li>
                
            </div>
        </nav>
        <script></script>
        <style>
            /* generic table styling */
            table { border-collapse: collapse; }
            th, td { padding: 5px; }
            th { border-bottom: 2px solid #999; background-color: #eee; vertical-align: bottom; }
            td { border-bottom: 1px solid #ccc; }
            /* filter-table specific styling */
            td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
            /* special filter field styling for this example */
        </style>
        <script>
            function getValue(){
               var checkboxes = document.getElementsByName('experience[]');
               var arrayOfData = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 arrayOfData.push(test);
                 }
               }
               var checkboxes = document.getElementsByName('service[]');
               var services = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 services.push(test);
                 }
               }
            
                var checkboxes = document.getElementsByName('Agent[]');
               var Agent = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 Agent.push(test);
                 }
               }
            
               var checkboxes = document.getElementsByName('service_on_call[]');
               var service_on_call = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 service_on_call.push(test);
                 }
               }
               var checkboxes = document.getElementsByName('Rating_by_Agent[]');
               var Rating_by_Agent = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 Rating_by_Agent.push(test);
                 }
               }
               
            
            
               var checkboxes = document.getElementsByName('action[]');
               var action = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 action.push(test);
                 }
               }
            
               var checkboxes = document.getElementsByName('approved[]');
               var approved = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 approved.push(test);
                 }
               }
            
               var checkboxes = document.getElementsByName('verified[]');
               var verified = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 verified.push(test);
                 }
               }
                var checkboxes = document.getElementsByName('telecaller[]');
               var telecaller = [];
              // alert(checkboxes.length);
               for (var i=0, n=checkboxes.length;i<n;i++) {
                 if (checkboxes[i].checked) 
                 {
                 test= checkboxes[i].value;
            
                 telecaller.push(test);
                 }
               }
            
                $('#loading').html('<img src="<?php echo base_url();?>uploads/loading.gif"> loading...');
            
               $.ajax({ 
                 type: 'POST', 
                 data: { "experience":arrayOfData,"service":services,"Agent":Agent,"service_on_call":service_on_call,"Rating_by_Agent":Rating_by_Agent,"action":action,"approved":approved,"verified":verified,"telecaller":telecaller},
                 url: '<?php echo base_url()?>employee/filter/viewdata', 
                 success: function(result){
                 console.log(result);
                 //location.reload();
                 document.getElementById("page-wrapper").innerHTML= result;
            
                 } 
               });
            
            
               
            }
        </script>
        <script>
            function activate(id) {
                     
                       $.ajax({ 
                      type: 'POST', 
                      url: '<?php echo base_url();?>handyman/deactivate/'+id, 
                      success: function(result){
                      var name = document.getElementById("name_"+id).innerText;
                       var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';
            
                        displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                           '<span aria-hidden="true">&times;</span>'+
                           '</button>'+
                           '<strong>'+name+'  Dectivate successfully.</strong>'+
                           '</div>';
                       var inactivebutton = '<button class="btn btn-small btn-primary btn-sm" onclick="deactivate('+id+')">Activate</button></td>';
                       document.getElementById("status_"+id).innerHTML= "Inactive";
                       document.getElementById("statusbutton_"+id).innerHTML= inactivebutton;
                       document.getElementById("msgdisplay").innerHTML= displaymsg;
            
                      } 
                    });
            }
            
            
                   
             
            function deactivate(id){
                      var name = document.getElementById("name_"+id).innerText;
                    var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';
            
                        displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                           '<span aria-hidden="true">&times;</span>'+
                           '</button>'+
                           '<strong>'+name+'  Activate successfully.</strong>'+
                           '</div>';    
                    
                    $.ajax({ 
                      type: 'POST', 
                      url: '<?php echo base_url();?>handyman/activatehandyman/'+id, 
                      success: function(){
                       var activatebutton = '<button class="btn btn-small btn-info btn-sm" onclick="activate('+id+')">Dectivate</button>';  
                       document.getElementById("status_"+id).innerHTML= "Active";
                       document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
                       document.getElementById("msgdisplay").innerHTML= displaymsg;
                      } 
                    });
                    
            }
            
            function approvefilter(id){
             
                    $.ajax({ 
                      type: 'POST', 
                      url: '<?php echo base_url();?>handyman/approve/'+id, 
                      success: function(){
                       var name = document.getElementById("name_"+id).innerText;
                        var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';
            
                        displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                           '<span aria-hidden="true">&times;</span>'+
                           '</button>'+
                           '<strong>'+name+'  Approve successfully.</strong>'+
                           '</div>'; 
                        var activatebutton = '<button class="btn btn-small btn-info btn-sm" onclick="activate('+id+')">Dectivate</button>';  
                       document.getElementById("status_"+id).innerHTML= "Active";
                       document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
                       document.getElementById("msgdisplay").innerHTML= displaymsg;
                      } 
                    });
            }
            
            function verify(id){
                 if (confirm("Are You Sure!") == true) {
                   var name = document.getElementById("name_"+id).innerText;
                    var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';
            
                        displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                           '<span aria-hidden="true">&times;</span>'+
                           '</button>'+
                           '<strong>'+name+'  Verified successfully.</strong>'+
                           '</div>';  
                    $.ajax({ 
                      type: 'POST', 
                      url: '<?php echo base_url();?>handyman/verify/'+id, 
                      success: function(){
                        var activatebutton = ' <button class="btn btn-small btn-danger btn-sm" onclick="approvefilter('+id+')">Approve</button>';
                        document.getElementById("status_"+id).innerHTML= "Verified";
                        document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
                        document.getElementById("msgdisplay").innerHTML= displaymsg;
                        document.getElementsByClassName("verified_by_"+id).innerHTML = "admin";
                       
                      } 
                    });
               }
                    
            }
            
            function deletehandyman(id){
                        if (confirm("Are You Sure!") == true) {
                          var name = document.getElementById("name_"+id).innerText;
                          var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';
            
                        displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                           '<span aria-hidden="true">&times;</span>'+
                           '</button>'+
                           '<strong>'+name+'  Delete successfully.</strong>'+
                           '</div>';  
                    
                    $.ajax({ 
                      type: 'POST', 
                      url: '<?php echo base_url();?>handyman/delete/'+id, 
                      success: function(){
                       $('#table_'+id).css('display', 'none');
                       document.getElementById("msgdisplay").innerHTML= displaymsg;
                        
                      } 
                    });
                    
                    }
            }
                    
            
            
        </script>
