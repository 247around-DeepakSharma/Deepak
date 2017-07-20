<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
             <?php if(isset($error) && $error !==0) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $error . '</strong>
               </div>';
               }
               ?>

               <?php if(isset($sucess) && $sucess !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $sucess . '</strong>
               </div>';
               }
               ?>
             <?php if($this->session->flashdata('file_error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->flashdata('file_error') . '</strong>
               </div>';
               }
               ?>


            <h1 class="page-header">
               <b> Upload Delivered Products Excel</b>
            </h1>

             <form class="form-horizontal"  id="fileinfo" onsubmit="return submitForm();" name="file"  method="POST" enctype="multipart/form-data">
                <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Delivered Products Excel</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
                   <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >
               </div>

            </form>

         </div>
      </div>
   </div>
</div>
<?php $this->session->unset_userdata('file_error'); ?>
<!-- -->
<script>
function submitForm() {

  var fd = new FormData(document.getElementById("fileinfo"));
  fd.append("label", "WEBUPLOAD");
  $.ajax({
      url: "<?php echo base_url()?>employee/do_background_upload_excel/upload_snapdeal_file/delivered",
      type: "POST",
      data: fd,
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
  }).done(function( data ) {
     //console.log(data);
    alert(data);
    //location.reload();

  });
    alert('File upload will continue in the background...');
    //return false;
  //window.open('<?php echo base_url(); ?>employee/user');
}
</script>
<!--
<div class="chat-box">
    <input type="checkbox" />
    <label data-expanded="Close Notification" data-collapsed="Open Notification"></label>
    <div class="chat-box-content"><p id="notification"></p>
        <br/>
        <br/>


  <style type="text/css">

.chat-box {
  height: 300;
  font:normal normal 11px/1.4 Tahoma,Verdana,Sans-Serif;
  color:#333;
  width:100%; /* Chatbox width */
  border:1px solid #344150;
  border-bottom:none;
  background-color:white;
  position:fixed;

  bottom:0;
  z-index:9999;
  -webkit-box-shadow:1px 1px 5px rgba(0,0,0,.2);
  -moz-box-shadow:1px 1px 5px rgba(0,0,0,.2);
  box-shadow:1px 1px 5px rgba(0,0,0,.2);
}

.chat-box > input[type="checkbox"] {
  display:block;
  margin:0 0;
  padding:0 0;
  position:absolute;
  top:0;
  right:0;
  left:0;
  width:100%;
  height:26px;
  z-index:4;
  cursor:pointer;
  opacity:0;
  filter:alpha(opacity=0);
}

.chat-box > label {
  display:block;
  height:24px;
  line-height:24px;
  background-color:#344150;
  color:white;
  font-weight:bold;
  padding:0 1em 1px;
}

.chat-box > label:before {content:attr(data-collapsed)}

.chat-box .chat-box-content {
  padding:10px;
  display:none;
}

/* hover state */
.chat-box > input[type="checkbox"]:hover + label {background-color:#404D5A}

/* checked state */
.chat-box > input[type="checkbox"]:checked + label {background-color:#212A35}
.chat-box > input[type="checkbox"]:checked + label:before {content:attr(data-expanded)}
.chat-box > input[type="checkbox"]:checked ~ .chat-box-content {display:block}</style> -->
