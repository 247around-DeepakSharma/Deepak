<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTabs" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs-1" role="tab" data-toggle="tab" aria-expanded="true">
                                    Contacts
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabs-2" role="tab" data-toggle="tab" aria-expanded="true" >
                                    Holidays
                                </a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="tabs-1">
                                <div class="x_title">
                                         <h2>Contacts</h2>
                                            <div class="clearfix"></div>
                                            </div>
                                <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>First Escalation Point</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <?php   if (isset($account_manager_details) && !empty($account_manager_details)) {
                                   foreach ($account_manager_details as $am) { ?>                  
                                       <div class="col-md-4 col-sm-4 col-xs-12">
                                           <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                                   <div class="left col-xs-12">
                                                       <h2><b><?php echo $am['full_name'] ?></b></h2>
                                                       <p style="padding: 10px 0px;"> <strong>About: </strong> Escalation <?php echo $am['state'] ?></p>
                                                       <ul class="list-unstyled">
                                                           <li><i class="fa fa-phone"></i> Phone: <?php echo $am['phone'] ?></li>
                                                           <li><i class="fa fa-envelope"></i> Email: <?php echo $am['official_email'] ?></li>
                                                       </ul>
                                                   </div>
                                           </div>
                                       </div>
                                <?php } ?>
                            <?php } else { ?>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                    <div class="left col-xs-12">
                                        <h2><b>Mr. Vikas Singh</b></h2>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-phone"></i> Phone: 9910043586</li>
                                            <li><i class="fa fa-envelope"></i> Email: escalations@247around.com</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Second Escalation Point</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <?php if (isset($rm_details) && !empty($rm_details)) {
                            foreach ($rm_details as $rm) { ?>                  
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                            <div class="left col-xs-12">
                                                <h2><b>Mr. <?php echo ucwords($rm['full_name']); ?></b></h2>
                                                <p><strong>About: </strong> Escalation <?php if(isset(explode(',', $rm['designation'])[1])){echo ucwords(explode(',', $rm['designation'])[1]);} ?> </p>
                                                <ul class="list-unstyled">
                                                    <li><i class="fa fa-newspaper-o"></i> Office: <?php echo ucwords($rm['office_centre']); ?></li>
                                                    <li><i class="fa fa-phone"></i> Phone: <?php echo $rm['phone']; ?></li>
                                                    <li><i class="fa fa-envelope"></i> Email: <?php echo $rm['official_email']; ?></li>
                                                </ul>
                                            </div>
                                    </div>
                                </div>
<!--                            <div class="col-md-3">
                                    //stuff here
                                </div>-->
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
    
                                
                                
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>247around Spare Part Warehouse Related</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                         <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                 <div class="left col-xs-12">
                                     <h2><b>Mr. Sachin Sharma</b></h2>
                                     <p><strong>About: </strong> Inventory </p>
                                     <ul class="list-unstyled">
                                        <li><i class="fa fa-phone"></i> Phone: 9810558247 /  9910368974</li>
                                        <li><i class="fa fa-envelope"></i> Email: warehouse_noida@247around.com </li>
                                     </ul>
                                 </div>
                         </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                         <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                 <div class="left col-xs-12">
                                     <h2><b>Mr. Chandan Singh</b></h2>
                                     <p><strong>About: </strong> Inventory </p>
                                     <ul class="list-unstyled">
                                        <li><i class="fa fa-phone"></i> Phone: 8448965247 /  8949687620</li>
                                        <li><i class="fa fa-envelope"></i> Email: warehouse_noida@247around.com </li>
                                     </ul>
                                 </div>
                         </div>
                     </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                         <div class="well profile_view" style="width:100%!important;display: inline-block;background: #fff;padding: 5px;">
                                 <div class="left col-xs-12">
                                     <h2><b>Mr. Aditya Gupta</b></h2>
                                     <p><strong>About: </strong> Spare Part Reconciliation </p>
                                     <ul class="list-unstyled">
                                        <li><i class="fa fa-phone"></i> Phone: 8745050887</li>
                                        <li><i class="fa fa-envelope"></i> Email: adityag@247around.com </li>
                                     </ul>
                                 </div>
                         </div>
                     </div>
                </div>
            </div>
        </div>
    </div>                            
                                
   <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>247around Invoices Related</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-12 col-sm-12 col-xs-12 profile_details">
                        <div class="well profile_view" style="width:100%!important;">
                            <div class="left col-xs-12">
                                <h2><b>Miss. Geeta Gupta</b></h2>
                                <p><strong>About: </strong> Accountant </p>
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-phone"></i> Phone: 9717165247</li>
                                    <li><i class="fa fa-envelope"></i> Email: geetag@247around.com </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Technical – CRM Related</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-12 col-sm-12 col-xs-12 profile_details">
                        <div class="well profile_view" style="width:100%!important; height: 120px;">
                            <div class="left col-xs-12">
                                <h2></h2>
                                <p></p>
                                <ul class="list-unstyled">
                                    <li></li>
                                    <li><i class="fa fa-envelope"></i> Email: support@247around.com</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="tab-pane" id="tabs-2">
                                  <div class="x_title">
                                         <h2>Holiday List  <?php echo date("Y") ?></h2>
        <div style='border-radius: 5px;background: #EEEEEE;margin-bottom: 10px;width:330px;margin-top: 4px;margin-left: 9px;' class='col-md-6'><b>NOTE:</b> <i>Checkmark shows Holiday declared.</i></div>
                                            <div class="clearfix"></div>
                                            </div>
                                 <table class="table table-bordered table-hover table-striped">
                                     <thead style="background: #2a3f54;color: #fff;">
                    <tr>
                        <th>S.N.</th>
                        <th style="padding:5px;text-align: center">DATE</th>
                        <th style="padding:5px;text-align: center">DAY</th>
                        <th style="padding:5px;text-align: center">EVENT</th>
                        <th style="padding:5px;text-align: center">DELHI</th>
                        <th style="padding:5px;text-align: center">CHENNAI</th>
                        <th style="padding:5px;text-align: center">MUMBAI</th>
                        <th style="padding:5px;text-align: center">KOLKATA</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($holidayList as $key => $value) { ?>		
                        <tr>
                            <td ><?php echo ($key + 1) . '.' ?></td>
                            <td style="padding:1px;text-align: center"><?php echo date('d M Y', strtotime($value['event_date'])) ?></td>
                            <td style="padding:1px;text-align: center"><?php echo date('l', strtotime($value['event_date'])) ?></td>
                            <td style="padding:1px;text-align: center;"><?php echo $value['event_name'] ?></td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['delhi'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['chennai'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['mumbai'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['kolkata'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
    