<!DOCTYPE html>
<html>
<head>
<title>Update Data</title>
<style>
  #form
  {
    width:600px;
    height: 300px;
    background-color:#fff;
      margin-left:400px; 
      margin-top:50px;
  }
  #table
  {
    padding-top:50px;
  }
  </style>
</head>
 
<body bgcolor="grey">
 <?php
  foreach($data as $row)
  {
  ?>
	<form method="post" id="form" action="http://localhost/CodeIgniter/index.php/WorldCricket/for_update/'".$row->Team_Name."'">
      <h2>For Updation</h2>
		  <table width="600" border="1" cellspacing="5" cellpadding="5" id="table">
          <tr>
            <td width="230">Enter Your Team Name </td>
            <td width="329"><input type="text" name="name" value="<?php echo $row->Team_Name;?>"readonly='true'/></td>
         </tr>
         <tr>
            <td>Enter Your Captain Name </td>
            <td><input type="text" name="captain" value="<?php echo $row->Team_Captain;?>"/></td>
         </tr>
         <tr>
            <td>Enter Your Counry</td>
            <td><input type="text" name="country" value="<?php echo $row->Team_Country;?>"/></td>
         </tr>
         <tr>
            <td>Enter Your Established Date </td>
            <td><input type="text" name="date" value="<?php echo $row->Team_Established;?>"/></td>
         </tr>
         <tr>
            <td colspan="2" align="center">
	          <input type="submit" name="update" value="Update_Records"/></td>
         </tr>
      </table>
	</form>
 <?php } ?>
</body>
</html>