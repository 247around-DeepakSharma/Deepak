<div class="modal-header" style="    background: #164f4e;">
    <h4 class="modal-title" style="text-align: center;color: #fff;">247around Call Center - 9555000247</h4>
</div>       



<div class="modal-header" style="padding: 26px 0px;">
    <div class="col-md-12" style="margin-top: -12px;">
        <div class="col-md-6" style="padding-left: 16px;">
    <h4 class="modal-title">Regional Contacts:</h4>
    </div>
        <div class="col-md-6" style="padding-left: 28px;">
    <h4 class="modal-title">TV Bracket Ordering:</h4>
    </div>
        </div>
</div>
<div class="clear"></div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                <?php if (isset($rm_details) && !empty($rm_details)) {
                    foreach ($rm_details as $rm) {
                        ?>
                        <div class="col-md-12">
                            <div class="long-card" style="padding: 10px 16px;">
                                    <h5><b>Mr. <?php echo ucwords($rm['full_name']); ?></b></h5> 
                                    <p>Escalation <?php if(isset(explode(',', $rm['designation'])[1])){echo ucwords(explode(',', $rm['designation'])[1]);} ?></p>
                                    <p><?php echo ucwords($rm['designation']); ?></p>
                                    <p><?php echo ucwords($rm['office_centre']); ?> Office,<span><?php echo $rm['phone']; ?></span></p>
                                    <p><?php echo $rm['languages']; ?></p>
                                    <p><?php echo $rm['official_email']; ?></p>
                            </div>
                        </div>
                    <?php }
                } ?>
                    </div>
                <div class="col-md-6">
                    <div class="long-card" style="padding: 10px 16px;">
                    <h5><b>Ms. Vijaya</b></h5> 
                    <p>Back Office Closure Champion</p>
                    <p>Delhi Office</p>
                    <p>0120-4540185 <span> <strong>|</strong> English and Hindi</span></p>
                    <p>vijaya@247around.com,booking@247around.com</p>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>  

<div class="modal-header">
    <h4 class="modal-title">247around Spare Related:</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="card" style="padding: 10px 16px; height: 182px;">
                <div class="container">
<!--                    <h5><b>Mr. Sachin Sharma</b></h5> 
                    <p>Delhi Office</p>
                    <p>‭+91 9810558247</p>
                    <p>sachins@247around.com</p>-->
                    <h5><b>Pickup & Dispatch</b></h5> 
                    <p>Delhi Office</p>
                    <p>warehouse_noida@247around.com</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" style="padding: 10px 16px;">
                <div class="container">
<!--                    <h5><b>Mr. Chandan Singh</b></h5> 
                    <p>Delhi Office</p>
                    <p>‭+91 8448965247</p>
                    <p>chandans@247around.com</p>-->
                    <h5><b>Escalation</b></h5> 
                    <p><b>Mr. Sachin Sharma</b></p>
                    <p>Delhi Office</p>
                    <p>‭+91 9810558247</p>
                    <p>sachins@247around.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-header">
    <h4 class="modal-title">247around Invoices Related:</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:40%;">
                <div class="container">
                    <h5><b>Miss. Geeta Gupta</b></h5> 
                    <p>Delhi Office</p>
                    <p>‭+91 9717165247</p>
                    <p>geetag@247around.com</p>
                </div>
            </div>
        </div>
    </div>
</div>