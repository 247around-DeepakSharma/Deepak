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
                                    <p><i class="fa fa-user"></i> <?php echo ucwords($rm['designation']); ?></p>
                                    <p><i class="fa fa-newspaper-o"></i> <?php echo ucwords($rm['office_centre']); ?> Office, <i class="fa fa-phone"></i> <span><?php echo $rm['phone']; ?></span></p>
                                    <p><i class="fa fa-language"></i> <?php echo $rm['languages']; ?></p>
                                    <p><i class="fa fa-envelope"></i> <?php echo $rm['official_email']; ?></p>
                            </div>
                        </div>
                    <?php }
                } ?>
                    </div>
                <div class="col-md-6">
                    <div class="long-card" style="padding: 10px 16px;">
                    <h5><b>Ms. Vijaya</b></h5> 
                    <p><i class="fa fa-user"></i> Back Office Closure Champion</p>
                    <p><i class="fa fa-newspaper-o"></i> Delhi Office</p>
                    <p><i class="fa fa-phone"></i> 8506902678 <span> <strong>|</strong> <i class="fa fa-language"></i> English and Hindi</span></p>
                    <p><i class="fa fa-envelope"></i> vijaya@247around.com,booking@247around.com</p>
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
        <div class="col-md-4">
            <div class="card" style="padding: 10px 2px; height: 182px;">
                <div class="container">
<!--                    <h5><b>Pickup & Dispatch</b></h5> 
                    <p>Delhi Office</p>
                    <p>warehouse_noida@247around.com</p>-->
                    <h5><b>Ghaziabad Warehouse</b></h5> 
                    <p><i class="fa fa-newspaper-o"></i> 56, Anand Industrial Estate, Mohan <br>Nagar , Ghaziabad – Uttar <br>Pradesh - 201007</p>
                    <p><i class="fa fa-envelope"></i> warehouse_noida@247around.com</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="padding: 10px 2px; height: 182px;">
                <div class="container">
                    <h5><b>Aurangabad Warehouse</b></h5> 
                    <p><i class="fa fa-newspaper-o"></i> Dhade No-1 Plot 13, Phase 3 Near <br>Ekdant Udyog Mitra Coprative <br>Estate Chitegaon, Ta Paithan, <br>Dist Aurangabad</li></p>
                    <p><i class="fa fa-envelope"></i> warehouse_aurangabad@247around<br>.com</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="padding: 10px 2px; height: 182px;">
                <div class="container">
<!--                <h5><b>Escalation</b></h5> 
                    <p><b>Mr. Sachin Sharma</b></p>
                    <p>Delhi Office</p>
                    <p>‭+91 9810558247</p>
                    <p>sachins@247around.com</p>-->
                    <h5><b>Escalation</b></h5>
                    <p><b>Mr. Chandan Singh</b></p>
                    <p><i class="fa fa-envelope"></i> warehouse@247around.com</p>
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
                    <p><i class="fa fa-newspaper-o"></i> Delhi Office</p>
                    <p>‭<i class="fa fa-phone"></i> +91 9717165247</p>
                    <p><i class="fa fa-envelope"></i> geetag@247around.com</p>
                </div>
            </div>
        </div>
    </div>
</div>