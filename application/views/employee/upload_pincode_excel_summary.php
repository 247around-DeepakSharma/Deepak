<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
<style type="text/css">
    table{
        width: 99%;
        padding: 0;
        margin: 0;
        border: 0;
        border-collapse: collapse;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;
        font-size: 14;
        margin:0px; 
        padding:0px; 
    }

    th{
        height: 30px;
        background-color: #4CBA90;
        color: white;
    }


</style>

<div id="page-wrapper" style="width:140%;">
    <div class="">
        <div class="row">
            <div style="width:100%;margin-left:10px;margine-right:5px;">
                <h1 align="left">Service centre to Pincode mapping Summary</h1>
                
                <br>
                <br>
                <br>                
                
                <h4>
                    Pincodes added: <?php echo $pincode; ?>
                </h4>
                
                <h4>
                    Errors: <?php echo $error; ?>
                </h4>
                
<!--
                <table cellpadding="0" cellspacing="0" border-collapse="collapse">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Vendor Id</th>
                        <th>Appliance</th>
                        <th>Brand</th>
                        <th>Area</th>
                        <th>Pincode</th>
                        <th>Region</th>
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach ($booking as $key => $row) { ?>

                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?= $row['Vendor_ID']; ?></td>
                        <td><?= $row['Appliance']; ?></td>
                        <td><?= $row['Brand']; ?></td>
                        <td><?= $row['Area']; ?></td>
                        <td><?= $row['Pincode']; ?></td>
                        <td><?= $row['Region']; ?></td>
                    </tr>

                    <?php } ?>

                </table>

-->
            </div>
        </div>
    </div>
</div>

