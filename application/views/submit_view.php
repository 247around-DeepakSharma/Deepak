<html>
<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
<title>Display records</title>
<style type="text/css">
  #form
  {
    width:600px;
    height: 400px;
    background-color:#fff;
      margin-left:300px; 
      margin-top:50px;
  }
</style>
</head>
 
<body bgcolor="grey">
    <table class="table"width="200px">
  <thead class="thead-dark" >
    <tr>
      <th scope="col">Sr NO</th>
      <th scope="col">Team Name</th>
      <th scope="col">Team Captain</th>
      <th scope="col">Team Country</th>
      <th scope="col">TeamEstablished</th>
      <th scope="col">Update</th>
      <th scope="col"><a href="http://localhost/CodeIgniter/index.php/WorldCricket/show"><button type="button" class="btn btn-light"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="           http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
      </svg></button></a></th></tr>

 
 </thead>
 <tbody>
  <?php
  $i=1;
  foreach($data as $row)
  {
  echo "<tr>";
  echo "<td scope='row'>".$i."</td>";
  echo "<td>".$row->Team_Name."</td>";
  echo "<td>".$row->Team_Captain."</td>";
  echo "<td>".$row->Team_Country."</td>";
  echo "<td>".$row->Team_Established."</td>";

  //For button
  echo "<td><a href=''http://localhost/CodeIgniter/index.php/WorldCricket/show_teamname_forupdate/'".$row->Team_Name."''><button type='button' class='btn btn-primary'><svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-pencil-fill' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
     <path fill-rule='evenodd' d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/></button></a>
       </svg>

    <a href='http://localhost/CodeIgniter/index.php/WorldCricket/delete_row/'".$row->Team_Name."''><button type='button' class='btn btn-danger'><svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-x'  fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
       <path fill-rule='evenodd' d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/>
       </svg></button> </a></td>";

  echo "</tr>";
  $i++;
  }
   ?>
 </tbody>
</table>
	</body>
</html>
