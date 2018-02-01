<style>
    .col-md-3{
        width: 24%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>First Escalation Point</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                        <div class="well profile_view">
                                <?php if (isset($account_manager_details) && !empty($account_manager_details)) { ?> 
                                    <div class="left col-xs-12">
                                        <h2><b><?php echo $account_manager_details[0]['full_name'] ?></b></h2>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-phone"></i> Phone: <?php echo $account_manager_details[0]['phone'] ?></li>
                                            <li><i class="fa fa-envelope"></i> Email: <?php echo $account_manager_details[0]['official_email'] ?></li>
                                        </ul>
                                    </div>
                                <?php } else { ?> 
                                    <div class="left col-xs-12">
                                        <h2><b>Mr. Vikas Singh</b></h2>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-phone"></i> Phone: 9910043586</li>
                                            <li><i class="fa fa-envelope"></i> Email: escalations@247around.com</li>
                                        </ul>
                                    </div>
                                <?php } ?>
                        </div>
                    </div>
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
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Technical – CRM Related</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-6 col-sm-6 col-xs-12 profile_details">
                        <div class="well profile_view" style="width:100%!important;">
                            <div class="left col-xs-12">
                                <h2><b>Mr. Anuj Aggarwal</b></h2>
                                <p><strong>About: </strong> Director & CTO </p>
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-phone"></i> Phone: 8826423424</li>
                                    <li><i class="fa fa-envelope"></i> Email: anuj@247around.com</li>
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
                    <h2>247around Invoices Related</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-6 col-sm-6 col-xs-12 profile_details">
                        <div class="well profile_view" style="width:100%!important;">
                            <div class="left col-xs-12">
                                <h2><b>Mr. Aditya Gupta</b></h2>
                                <p><strong>About: </strong> Accountant </p>
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
</div>