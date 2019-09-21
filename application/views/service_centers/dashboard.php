<style>
    .loader_img{
    width: 22px;
    }
    #msl_info{margin: 10px 89px;width: 88%;padding: 0px;}
    #msl_info .x_title{background: #2c9d9c; color: #fff;}
    #msl_info .x_title>h2{margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;}
    #msl_info .x_body{
        margin-top: 0px;
        display: flex;
        font-size: 25px;
        border: 2px solid #2c9d9c;
        padding: 10px 0;
        flex-direction: row;
        justify-content: space-evenly;
    }
    #msl_info .x_body>div{
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #msl_info a{font-size:18px;color: #254d5d;}
    #msl_info .count{text-decoration: underline;font-size: 36px;}
</style>
<div class="row" style="margin: 0px;">
    <?php if($this->session->userdata("is_micro_wh")==1){ ?>
    <div class="col-md-10 col-md-offset-2" id="msl_info">
        <div class="x_title">
            <h2>MSL Security Amount</h2>
        </div>
        <div class="x_body">
            <div>
                <a><label>MSL Security Amount (Rs.)</label></a>
                <div class="count <?php if($msl['security']>0){ ?>text-success<?php }else{ ?>text-danger<?php }?>">
                    <strong><?php echo $msl['security']; ?>/-</strong>
                </div>
            </div>
            <div>
                <a><label>MSL Amount (Rs.)</label></a>
                <div class="count <?php if($msl['amount']>0){ ?>text-success<?php }else{ ?>text-danger<?php }?>">
                    <strong><?php echo $msl['amount']; ?>/-</strong>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="col-md-10 col-md-offset-2" style="margin: 10px 89px;width: 88%;padding: 0px;">
        <div class="x_title" style="background: #2c9d9c; color: #fff;">
            <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">Performance Summary</h2>
            <div class="clearfix"></div>
        </div>
        <div style="margin-top: 0px;display: flex;font-size: 25px;border: 2px solid #2c9d9c;padding: 10px 0;justify-content: center;border-bottom: none;">
            <b>Rating:</b> &nbsp;
            <b><span <?php if($rating > '3.5') { echo "class='text-success'";}else{echo "class='text-danger'";}?>><?php echo $rating; ?> /5</span></b> &nbsp;
            <div class="sf-escalation">
                <b> <span style="color:#333;"> | </span> Overall Escalation:</b>
                <b><span id="sf-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
            </div>
            <div class="sf-escalation">
                <b> <span style="color:#333;"> | </span> Current Month Escalation:</b>
                <b><span id="sf-cm-escalation-value" class="text-danger"></span><span class="text-danger">%</span></b>&nbsp;
            </div>
        </div>
        <div id="header_summary" style="border: 2px solid #2c9d9c;padding: 19px 12px 0px;">
            <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
        </div>
    </div>
    
    <div class="col-md-10 col-md-offset-2" style="margin: 10px 89px;width: 88%;padding: 0px;">
        <div class="x_title" style="background: #2c9d9c; color: #fff;">
            <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">Defective Part Summary</h2>
            <div class="clearfix"></div>
        </div>
        <div id="defective_header_summary" style="border: 2px solid #2c9d9c;padding: 19px 12px 0px;">
            <center>  <img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
        </div>
    </div>
    
    <div class="col-md-10 col-sm-10 col-xs-10" style="padding: 0px;margin: 10px 90px;border: 2px solid #2c9d9c; width: 88%;background: #fff;">
        <div class="x_title" style="background: #2c9d9c; color: #fff;">
            <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">TAT Reporting<button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                color: #fff;border: none;" data-toggle="tooltip"data-placement="right"title="(Booking Completed on x Day / Total Completed Bookings (Within Selected Range))*100">?</button></h2>
            <div class="clearfix"></div>
        </div>
        <div class="filter_container" style="margin-top: 10px;">
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Appliance</label>
                        <select class="form-control filter_table" id="service_id_Completed" name="services">
                            <option value="" selected="selected">All</option>
                            <?php foreach($services as $val){ ?>
                            <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Is Free <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                            color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Free For Customer">?</button></label>
                        <select class="form-control filter_table" id="free_paid_Completed" name="free_paid">
                            <option value="" selected="selected">All</option>
                            <option value="Yes">Yes (In Warranty)</option>
                            <option value="No">No (Out Of Warranty)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Is Upcountry</label>
                        <select class="form-control filter_table" id="upcountry_Completed" name="upcountry">
                            <option value="">All</option>
                            <option value="Yes">Yes</option>
                            <option value="No" selected="selected">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-3" style="    width: 18%;">
                <label for="">Booking Completed Date <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="By Default last 30 days">?</button></label>
                <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id_Completed">
            </div>
            <div class="form-group col-md-3">
                <label for="">Booking Status</label>
                <select class="form-control"  ng-model="status" id="completed_status_Completed">
                    <option value="">All</option>
                    <option value="Completed" ng-selected="true">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <button class="btn btn-primary" style="margin-top: 23px;background: #c1591c;border-color: #c1591c;" onclick="fetch_filtered_tat_report()">Apply Filters</button>
        </div>
        <div class="clearfix"></div>
        <hr style="border: 1px solid #2c9d9c;">
        <div class="col-md-12" id="tat_holder">
            <table class="table table-bordered" id="tat_table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Day 0</th>
                        <th>Day 1</th>
                        <th>Day 2</th>
                        <th>Day 3</th>
                        <th>Day 4</th>
                        <th>Day 5-Day 7</th>
                        <th>Day 8-Day 15</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Installation</b></td>
                        <td id="Installation_Completed_0" style="background: #c1591c;color: #fff;" class="blinking">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Completed_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Repair Without Spare</b></td>
                        <td id="Repair_without_part_Completed_0" style="background: #c1591c;color: #fff;" class="blinking">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Completed_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Repair With Spare</b></td>
                        <td id="Repair_with_part_Completed_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Completed_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>All Repair</b></td>
                        <td id="Repair_Completed_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Completed_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td id="total_Completed_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Completed_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-10 col-sm-10 col-xs-10" style="padding: 0px;margin: 10px 90px;border: 2px solid #2c9d9c; width: 88%;background: #fff;">
        <div class="x_title" style="background: #2c9d9c; color: #fff;">
            <h2 style="margin: 0px;padding: 5px 0px 5px 16px;font-size: 24px;">Open Bookings TAT report<button type="button"class="btn btn-default" style="margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                color: #fff;border: none;"></button></h2>
            <div class="clearfix"></div>
        </div>
        <div class="filter_container" style="margin-top: 10px;">
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 160px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Appliance</label>
                        <select class="form-control filter_table" id="service_id_Pending" name="services">
                            <option value="" selected="selected">All</option>
                            <?php foreach($services as $val){ ?>
                            <option value="<?php echo $val['id']?>"><?php echo $val['services']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Is Free <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                            color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="Free For Customer">?</button></label>
                        <select class="form-control filter_table" id="free_paid_Pending" name="free_paid">
                            <option value="" selected="selected">All</option>
                            <option value="Yes">Yes (In Warranty)</option>
                            <option value="No">No (Out Of Warranty)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin: 0px;padding: 0px 1px;width: 170px;">
                <div class="item form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label for="">Is Upcountry</label>
                        <select class="form-control filter_table" id="upcountry_Pending" name="upcountry">
                            <option value="">All</option>
                            <option value="Yes">Yes</option>
                            <option value="No" selected="selected">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-3" style="    width: 18%;">
                <label for="">Booking Completed Date <button type="button"class="btn btn-default" style="float: right;margin-bottom: 10px;padding: 1px 4px;margin-top: 0px;font-size: 8px;margin-left: 5px;background: #008000;
                    color: #fff;border: none;" data-toggle="tooltip"data-placement="left"title="By Default last 30 days">?</button></label>
                <input type="text" class="form-control" name="daterange_completed_bookings" id="completed_daterange_id_Pending">
            </div>
            <button class="btn btn-primary" style="margin-top: 23px;background: #c1591c;border-color: #c1591c;" onclick="fetch_filtered_pending_report()">Apply Filters</button>
        </div>
        <div class="clearfix"></div>
        <hr style="border: 1px solid #2c9d9c;">
        <div class="col-md-12" id="tat_holder">
            <table class="table table-bordered" id="tat_table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Day 0</th>
                        <th>Day 1</th>
                        <th>Day 2</th>
                        <th>Day 3</th>
                        <th>Day 4</th>
                        <th>Day 5-Day 7</th>
                        <th>Day 8-Day 15</th>
                        <th>>15</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Installation</b></td>
                        <td id="Installation_Pending_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Installation_Pending_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Repair Without Spare</b></td>
                        <td id="Repair_without_part_Pending_0" >
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_without_part_Pending_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Repair With Spare</b></td>
                        <td id="Repair_with_part_Pending_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_with_part_Pending_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>All Repair</b></td>
                        <td id="Repair_Pending_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="Repair_Pending_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td id="total_Pending_0">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_1">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_2">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_3">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_4">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_5">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_8">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                        <td id="total_Pending_16">
                            <center>  <img class="loader_img" src="<?php echo base_url(); ?>images/loader.gif" /> </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
    //        var d = new Date();
    //        n = d.getMonth();
    //        y = d.getFullYear();
    //        date = d.getDate();
        $('input[id="completed_daterange_id"]').daterangepicker({
           timePicker: true,
           timePickerIncrement: 30,
           locale: {
               format: 'YYYY-MM-DD'
           },
           //startDate: y+'-'+n+'-'+date
           startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
       });
        get_header_summary();
        get_defective_header_summary();
        get_sf_tat_report("Installation","Completed");
        get_sf_tat_report("Repair_with_part","Completed");
        get_sf_tat_report("Repair_without_part","Completed");
        get_sf_tat_report("Repair_with_part:Repair_without_part","Completed");
        get_sf_tat_report("not_set","Completed");
        get_sf_tat_report("Installation","Pending");
        get_sf_tat_report("Repair_with_part","Pending");
        get_sf_tat_report("Repair_without_part","Pending");
        get_sf_tat_report("Repair_with_part:Repair_without_part","Pending");
        get_sf_tat_report("not_set" ,"Pending");
        get_escalation_report();
    });
    function get_escalation_report(){
        $.ajax({
                method:'POST',
                url: '<?php echo base_url(); ?>employee/service_centers/get_sf_escalation/<?php echo $this->session->userdata('service_center_id')?>',
                success:function(res){
                    if(res === 'empty'){
                        $('#sf-escalation-value').html('0');
                        $('#sf-cm-escalation-value').html('0');
                    }else{
                        var data = JSON.parse(res);
                        $('#sf-escalation-value').html(data['total_escalation_per']);
                        $('#sf-cm-escalation-value').html(data['current_month_escalation_per']);
                    }
    
    
                }
            });
        }
    function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    function get_sf_tat_report(request_type,report_status){
        vendor_id = '<?php echo $this->session->userdata('service_center_id'); ?>';
        service_id = $("#service_id_"+report_status).val();
        free_paid = $("#free_paid_"+report_status).val();
        upcountry = $("#upcountry_"+report_status).val();
        completed_daterange_id = $("#completed_daterange_id_"+report_status).val();
        completed_status = $("#completed_status_"+report_status).val();
        var data = {'vendor_id':vendor_id,'services':service_id,'free_paid':free_paid,'upcountry':upcountry,'daterange_completed_bookings':completed_daterange_id,'status':completed_status,'request_type':request_type};
        if(report_status == "Completed"){
            url =  '<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/00/1';
        }
        else{
            url =  '<?php echo base_url(); ?>employee/dashboard/tat_calculation_full_view/00/1/0/Pending';
        }
        sendAjaxRequest(data,url,"POST").done(function(response){
            if(request_type == 'not_set'){
                request_type = 'total';
            }
            if(request_type == 'Repair_with_part:Repair_without_part'){
                request_type = 'Repair';
            }
            var obj = JSON.parse(response);
            if(obj[0]){
                 tat_0 = obj[0]['TAT_0']+' ('+obj[0]['TAT_0_per']+'%)';
                 tat_1 = obj[0]['TAT_1']+' ('+obj[0]['TAT_1_per']+'%)';
                 tat_2 = obj[0]['TAT_2']+' ('+obj[0]['TAT_2_per']+'%)';
                 tat_3 = obj[0]['TAT_3']+' ('+obj[0]['TAT_3_per']+'%)';
                 tat_4 = obj[0]['TAT_4']+' ('+obj[0]['TAT_4_per']+'%)';
                 tat_5 = obj[0]['TAT_5']+' ('+obj[0]['TAT_5_per']+'%)';
                 tat_8 = obj[0]['TAT_8']+' ('+obj[0]['TAT_8_per']+'%)';
                 tat_16 = obj[0]['TAT_16']+' ('+obj[0]['TAT_16_per']+'%)';
                if(report_status == 'Pending'){
                     tat_0 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_0  = tat_0+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_0_bookings']+'">';
                     tat_0  = tat_0+'<input type="submit" value="'+obj[0]['TAT_0']+' ('+obj[0]['TAT_0_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_1 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_1  = tat_1+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_1_bookings']+'">';
                     tat_1  = tat_1+'<input type="submit" value="'+obj[0]['TAT_1']+' ('+obj[0]['TAT_1_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_2 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_2  = tat_2+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_2_bookings']+'">';
                     tat_2  = tat_2+'<input type="submit" value="'+obj[0]['TAT_2']+' ('+obj[0]['TAT_2_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_3 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_3  = tat_3+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_3_bookings']+'">';
                     tat_3  = tat_3+'<input type="submit" value="'+obj[0]['TAT_3']+' ('+obj[0]['TAT_3_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_4 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_4  = tat_4+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_4_bookings']+'">';
                     tat_4  = tat_4+'<input type="submit" value="'+obj[0]['TAT_4']+' ('+obj[0]['TAT_4_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_5 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_5  = tat_5+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_5_bookings']+'">';
                     tat_5  = tat_5+'<input type="submit" value="'+obj[0]['TAT_5']+' ('+obj[0]['TAT_5_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_8 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_8  = tat_8+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_8_bookings']+'">';
                     tat_8  = tat_8+'<input type="submit" value="'+obj[0]['TAT_8']+' ('+obj[0]['TAT_8_per']+'%)'+'" class="btn btn-success"></form>';
                     
                     tat_16 = '<form action="<?php echo base_url()."employee/service_centers/pending_booking"?>" method="post" target="_blank">';
                     tat_16  = tat_16+'<input type="hidden" name="booking_id_status" value="'+obj[0]['TAT_16_bookings']+'">';
                     tat_16  = tat_16+'<input type="submit" value="'+obj[0]['TAT_16']+' ('+obj[0]['TAT_16_per']+'%)'+'" class="btn btn-success"></form>';
                }
                
                $("#"+request_type+"_"+report_status+"_0").html(tat_0); 
                $("#"+request_type+"_"+report_status+"_1").html(tat_1);
                $("#"+request_type+"_"+report_status+"_2").html(tat_2);
                $("#"+request_type+"_"+report_status+"_3").html(tat_3);
                $("#"+request_type+"_"+report_status+"_4").html(tat_4);
                $("#"+request_type+"_"+report_status+"_5").html(tat_5);
                $("#"+request_type+"_"+report_status+"_8").html(tat_8);
                $("#"+request_type+"_"+report_status+"_16").html(tat_16);
            }
            else{
                $("#"+request_type+"_"+report_status+"_0").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_1").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_2").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_3").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_4").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_5").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_8").text("0 (0.00%)");
                $("#"+request_type+"_"+report_status+"_16").text("0 (0.00%)");
            }
        }); 
    }
     $(function() {
    //     var d = new Date();
    //        n = d.getMonth();
    //        y = d.getFullYear();
    //        date = d.getDate();
        $('input[name="daterange_completed_bookings"]').daterangepicker({
             timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: "<?php echo date("Y-m-d", strtotime("-1 month")); ?>"
        //startDate: y+'-'+n+'-'+date
    });
    });
    function fetch_filtered_tat_report(){
    get_sf_tat_report("Installation","Completed");
    get_sf_tat_report("Repair_with_part","Completed");
    get_sf_tat_report("Repair_without_part","Completed");
    get_sf_tat_report("Repair_with_part:Repair_without_part","Completed");
    get_sf_tat_report("not_set" ,"Completed");
    }
    function fetch_filtered_pending_report(){
    get_sf_tat_report("Installation","Pending");
    get_sf_tat_report("Repair_with_part","Pending");
    get_sf_tat_report("Repair_without_part","Pending");
    get_sf_tat_report("Repair_with_part:Repair_without_part","Pending");
    get_sf_tat_report("not_set" ,"Pending");
    }
    function get_header_summary(){
       $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/service_centers/get_header_summary/',
         success: function (data) {
          $("#header_summary").html(data);   
    
         }
       });
    
    }
    
    function get_defective_header_summary(){
       $.ajax({
         type: 'POST',
         url: '<?php echo base_url(); ?>employee/service_centers/get_defective_part_header_summary/',
         success: function (data) {
          $("#defective_header_summary").html(data);   
    
         }
       });
    
    }
</script>
<style>
    .blinking{
    animation:blinkingText 0.5s infinite;
    }
    @keyframes blinkingText{
    0%{     background-color: #008000;    }
    49%{    background-color: #008000; }
    50%{    background-color: #c1591c; }
    99%{    background-color:#c1591c;  }
    100%{   background-color: #008000;    }
    }
</style>
