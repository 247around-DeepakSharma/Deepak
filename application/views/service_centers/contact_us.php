<div class="modal-header" style="    background: #164f4e;">
    <h4 class="modal-title" style="text-align: center;color: #fff;">247around Call Center - 9555000247</h4>
</div>       



<div class="modal-header" style="padding: 26px 0px;">
    <div class="col-md-12" style="margin-top: -12px;">
        <div class="col-md-6" style="padding-left: 16px;">
    <h4 class="modal-title">Regional Contacts:</h4>
    </div>
        <div class="col-md-6" style="padding-left: 28px;">
    <!--<h4 class="modal-title">TV Bracket Ordering:</h4>-->
    </div>
        </div>
</div>
<div class="clear"></div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                
               <div class="col-md-6">
                     <?php if (isset($new_asm_details) && !empty($new_asm_details)) {
                    foreach ($new_asm_details as $asm) {
                        ?>
                        <div class="col-md-12">
                            <div class="long-card" style="padding: 10px 16px;">
                                    <h5><b>Mr. <?php echo ucwords($asm['full_name']); ?></b></h5> 
                                    <p>Escalation <?php if(isset(explode(',', $asm['designation'])[1])){echo ucwords(explode(',', $asm['designation'])[1]);} ?></p>
                                    <p><i class="fa fa-user"></i> <?php echo ucwords($asm['designation']); ?></p>
                                    <p><i class="fa fa-newspaper-o"></i> <?php echo ucwords($asm['office_centre']); ?> Office, <i class="fa fa-phone"></i> <span><?php echo $asm['phone']; ?></span></p>
                                    <p><i class="fa fa-language"></i> <?php echo $asm['languages']; ?></p>
                                    <p><i class="fa fa-envelope"></i> <?php echo $asm['official_email']; ?></p>
                            </div>
                        </div>
                    <?php }
                } ?>
                </div>
                
                <div class="col-md-6">
                   <?php
                   //print_r($new_rm_details);
                   ?>
                <?php if (isset($new_rm_details) && !empty($new_rm_details)) {
                    foreach ($new_rm_details as $rm) {
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
            <div class="card" style="padding: 10px 2px; height: 300px;">
                <div class="container" style="width: 100%; height:100%; word-break: break-all;">
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
            <div class="card" style="padding: 10px 2px; height: 300px;">
                <div class="container" style="width: 100%; height:100%; word-break: break-all;">
                    <h5><b>Aurangabad Warehouse</b></h5> 
                    <p><i class="fa fa-newspaper-o"></i> Dhade No-1 Plot 13, Phase 3 Near <br>Ekdant Udyog Mitra Coprative <br>Estate Chitegaon, Ta Paithan, <br>Dist Aurangabad</li></p>
                    <p><i class="fa fa-envelope"></i> warehouse_aurangabad@247around<br>.com</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="padding: 10px 2px; height: 300px;">
                <div class="container" style="width: 100%; height:100%; word-break: break-all;">
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

<div class="row">
<!--    <div class="col-md-6">
        <div class="modal-header">
            <h4 class="modal-title">247around Invoices Related:</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="container">
                            <h5><b>Mr. Sonu Yadav</b></h5> 
                            <p><i class="fa fa-newspaper-o"></i> Delhi Office</p>
                            <p>‭<i class="fa fa-phone"></i> +91 9717165247</p>
                            <p><i class="fa fa-envelope"></i> sonuy@247around.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
    <div class="col-md-12">
        <div class="modal-header col-md-6">
            <h4 class="modal-title">CRM Training and Invoice Queries<br/>Helpline Number:</h4>
        </div>
        <div class="modal-header col-md-6">
            <h4 class="modal-title">Spare Approval<br/>Helpline:</h4>
        </div> 
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card" style="padding: 10px 10px; height:110px;">
                            <p>‭<i class="fa fa-phone"></i> +91 7428532447</p>
                            <p hidden>‭<i class="fa fa-phone"></i> +91 9650702247</p>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="card">
                        <div class="card" style="padding: 10px 10px; height:110px;">
                            <p>‭<i class="fa fa-envelope"></i> spare-approval@247around.com</p>
                            <br>
                            <p style="font-size: 85%;">‭ for sending
                                        video in panel replacement and gas charging cases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <div class="col-md-12">
        <div class="modal-header col-md-6">
            <h4 class="modal-title">For Spares / Accessories Purchase<br/>Pls Contact:</h4>
        </div> 
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card col-md-6" style="padding: 10px 10px; height:150px;">
                            <p>‭<i class="fa fa-phone"></i> +91 7428913247</p>
                            <p>‭<i class="fa fa-phone"></i> +91 9910132247</p>
                            <p>‭<i class="fa fa-envelope"></i> spare-purchase@247around.com</p>
                            <p style="font-size: 100%;">‭ Order along with your SF name can be sent on Whatsapp also</p>   
                        </div>
                    </div>
                </div>
           <!--       <div class="col-md-6">
                    <div class="card">
                        <div class="card" style="padding: 10px 10px; height:110px;">
                            <p>‭<i class="fa fa-envelope"></i> spare-approval@247around.com</p>
                            <br>
                            <p style="font-size: 85%;">‭ for sending
                                        video in panel replacement and gas charging cases</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
