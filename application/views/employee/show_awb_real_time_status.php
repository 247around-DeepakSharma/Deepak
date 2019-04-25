<style>
    .shipment_summary{
        border-top: 1px solid #eee;
        border-bottom: 1px solid #ccc;
        margin-bottom: 20px;
        margin-top: -21px;
        padding: 14px;
    }
    .table>tbody>tr>td {
        border-top: 0px;
    }
    .table>tbody>tr>td:first-child {
        border-top: 0px;
        border-left:1px solid #eee;
    }
    .shipment_details b{
        width: 13px;
        height: 13px;
        background: #fff;
        border: 2px solid #555;
        margin: 0px 0px 12px -15px;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        overflow: hidden;
        display: inline-block;
        float: left;
    }

    .awb_number_details .delivered_data{
        color: #fff!important ;
        background-color: #9bbb59!important;
    }

    .awb_number_details .pickup_data{
        color: #fff!important;
        background-color: #ffc000!important;
    }

    .awb_number_details .transit_data{
        color: #fff!important;
        background-color: #4f81bd!important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="awb_number_details">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php if ( isset($awb_details_by_api) && !empty($awb_details_by_api)) { ?>
                        <div class="row shipment_summary <?php if (!empty($awb_details_by_api)) {
                        echo $awb_details_by_api['items'][0]['status'] . '_data';
                    } ?>">
                            <div class="col-md-6 text-center">
                                <p><strong>Shipped Via :</strong> <span><?php echo ucwords($awb_details_by_api['items'][0]['carrier_code']); ?></span></p>
                            </div>
                            <div class="col-md-6 text-center">
                                <p><strong>Status :</strong> <span><?php echo ucwords($awb_details_by_api['items'][0]['status']); ?></span></p>
                            </div>
                        </div>
                        <div class="shipment_details col-md-8 col-md-offset-2">
                            <table class="table table-responsive">                
                                    <?php 
                                    if(!empty($awb_details_by_api['items'][0]['origin_info']['trackinfo'])){
                                    foreach ($awb_details_by_api['items'][0]['origin_info']['trackinfo'] as $val) { ?>
                                    <tr>
                                        <td class=" text-center">
                                            <?php
                                            if (isset($val['ItemNode'])) {
                                                echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/delivery-truck.svg'>";
                                            } else {
                                                switch ($val['checkpoint_status']) {
                                                    case 'delivered':
                                                        echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/checked_img.svg'>";
                                                        break;
                                                    case 'pickup':
                                                        echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/flag.svg'>";
                                                        break;
                                                    default:
                                                        echo '<b></b>';
                                                }
                                            }
                                            ?>
                                            <p><?php echo $val['Date']; ?></p>
                                        </td>
                                        <td><?php echo $val['StatusDescription'] . ' ' . $val['Details']; ?></td>
                                    </tr>
                                    <?php }} ?>
                            </table>
                        </div>
                    <?php } else if(isset($awb_details_by_db) && !empty($awb_details_by_db)) { ?> 
                            <div class="row shipment_summary <?php if (!empty($awb_details_by_db)) { echo $awb_details_by_db[0]['final_status'] . '_data'; } ?>">
                                <div class="col-md-6 text-center">
                                    <p><strong>Shipped Via :</strong> <span><?php echo ucwords($awb_details_by_db[0]['carrier_code']); ?></span></p>
                                </div>
                                <div class="col-md-6 text-center">
                                    <p><strong>Status :</strong> <span><?php echo ucwords($awb_details_by_db[0]['final_status']); ?></span></p>
                                </div>
                            </div>
                            <div class="shipment_details col-md-8 col-md-offset-2">
                                <table class="table table-responsive">
                                            <?php foreach ($awb_details_by_db as $val) { ?>
                                        <tr>
                                            <td class=" text-center">
                                                <?php
                                                if (!empty($val['checkpoint_item_node'])) {
                                                    echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/delivery-truck.svg'>";
                                                } else {
                                                    switch ($val['checkpoint_status']) {
                                                        case 'delivered':
                                                            echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/checked_img.svg'>";
                                                            break;
                                                        case 'pickup':
                                                            echo "<img style='width: 20px;float: left;margin: 0px 0px 12px -16px;display: inline-block;' src='/images/flag.svg'>";
                                                            break;
                                                        default:
                                                            echo '<b></b>';
                                                    }
                                                }
                                                ?>
                                                <p><?php echo $val['checkpoint_status_date']; ?></p>
                                            </td>
                                            <td><?php echo $val['checkpoint_status_description'] . ' ' . $val['checkpoint_status_details']; ?></td>
                                        </tr>
                                        <?php } ?>
                                </table>
                            </div>
                        <?php } else { ?>
                        <div class="alert alert-danger">
                            <p class="text-center">No Data Found</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>