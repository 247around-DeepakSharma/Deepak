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
                <h1 align="left">Added Service Centres</h1>
                <table cellpadding="0" cellspacing="0" border-collapse="collapse">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Service Id</th>
                        <th>Category</th>
                        <th>Capacity</th>
                        <th>Service Category</th>
                        <th>Vendor Price</th>
                        <th>Around Markup</th>
                        <th>Service Charges</th>
                        <th>Service Tax</th>
                        <th>Total Charges</th>
                    </tr>

                    </thead>

                    <?php $count = 1; ?>
                    <?php foreach ($booking as $key => $row) { ?>

                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?= $row['service_id']; ?></td>
                        <td><?= $row['category']; ?></td>
                        <td><?= $row['capacity']; ?></td>
                        <td><?= $row['service_category']; ?></td>
                        <td><?= $row['vendor_price']; ?></td>
                        <td><?= $row['around_markup']; ?></td>
                        <td><?= $row['service_charges']; ?></td>
                        <td><?= $row['service_tax']; ?></td>
                        <td><?= $row['total_charges']; ?></td> 
                    </tr>

                    <?php } ?>

                </table>

            </div>
        </div>
    </div>
</div>

