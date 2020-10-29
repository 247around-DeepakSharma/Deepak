<html>
<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<title>World Cricket Team Information With Caption
</title>
<style>
	#form
	{
		width:50%;
		height: 70%;
		background-color:#fff;
	    margin-left:20%; 
	    margin-top:5%;
	    border-radius: 5px;
	   
	} 
	.btn1
	{
		margin-left: 250px;
	}
    .form-control1 
    {
    	padding-left: 10px;
    	padding-right: 10px;
    }
	</style>
</head>
<body>
	<div class="p-3 mb-2 bg-info ">
    <form method="post" id="form" enctype="multipart/form-data" action="http://localhost/CodeIgniter/index.php/WorldCricket/Insert_team">
		<h2>Team Information</h2>
		 <div class="form-group">
		 	<div class="form-control1">
           <label for="formGroupExampleInput">Team Name</label>
		     <input type="text" name="T_name"  class="form-control" id="formGroupExampleInput">
		   <label for="formGroupExampleInput">Team Captain</label>
			 <input type="text" name="Captain_Name"  class="form-control" id="formGroupExampleInput">
		   <label for="formGroupExampleInput">Country Name</label>
		     <select name="Country_Name"  class="form-control" id="formGroupExampleInput">
				<option>India</option>
				<option>Pakistan</option>
				<option>Australia</option>
				<option>Newziland</option>
				<option>Srilanka</option>
			 </select>
		   <label for="formGroupExampleInput">Country Flag</label><br>
		       <input type="file" name="fileToUpload" id="fileToUpload" >
		      </select> <br>
		     <label for="formGroupExampleInput">Established Date</label>
		     <input type="text" name="Established_Date"  class="form-control" id="formGroupExampleInput">
		    </div><br>
		     <input type="submit" name="save"  class="btn btn-primary btn1">
		 </div>
	</form>
	</div>
  </body>
</html