<style type="text/css">
    table{
          width: 80%;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;    
        padding: 2px;
    }
    
    th{
        height: 50px;
        background-color: #4CBA90;
        color: white;
    }
    tr:nth-child(even) {background-color: #f2f2f2}


</style>

<div id="page-wrapper" >
  <div class="">
    <div class="row">
      <div style="margin-left:10px;margine-right:5px;">
        <h1 align="left" style="color:blue;margin-left:400px;"><b>Users Found</b></h1>
        <b><?php if(empty($users)) {echo "No user found of this name";}?></b>
          <table>
            <thead>
              <tr>
                <th style="width:5%;">S No.</th>
                <th style="width:15%;">User Name</th>
                <th style="width:12%;">Phone No.</th>
                <th style="width:30%;">Address</th>
                <th style="width:10%;">Pincode</th>
                <th style="width:20%;">Email</th>
              </tr>
              <?php $count = 1; ?>
              <?php foreach($users as $key =>$row){?>      
                <tr id="row_color">
                    <td><?php echo $count; $count++;?></td>
                    <td><a href="<?php echo base_url();?>employee/user/user_details/<?=$row->phone_number;?>"><?=$row->name;?></a></td>
                    <td><?= $row->phone_number; ?></td>
                    <td><?= $row->home_address; ?></td>
                    <td><?= $row->pincode; ?></td>
                    <td><?= $row->user_email; ?></td>
                </tr>
              <?php } ?>
            </thead>
          </table>
      </div>
    </div>
  </div>
</div>