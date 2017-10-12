<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">247around Call Center - 9555000247</h4>
</div>       
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Calls & CRM Issues:</h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="container">
                            <h5><b>Mr. Devendra</b></h5> 
                            <p>Back Office Closure Champion</p>
                            <p>Delhi Office</p>
                            <p>8130572244 <span> <strong>|</strong> English and Hindi</span></p>
                            <p>booking@247around.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="container">
                            <h5><b>Mrs. Ranju</b></h5> 
                            <p>Back Office Closure Champion</p>
                            <p>Delhi Office</p>
                            <p>8130572244 <span> <strong>|</strong> Bengali and Hindi</span></p>
                            <p>booking@247around.com</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Regional Contacts:</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <?php if (isset($rm_details) && !empty($rm_details)) {
                    foreach ($rm_details as $rm) {
                        ?>
                        <div class="col-md-4">
                            <div class="long-card">
                                <div class="container">
                                    <h5><b>Mr. <?php echo ucwords($rm['full_name']); ?></b></h5> 
                                    <p>Escalation <?php echo ucwords(explode(',', $rm['designation'])[1]); ?></p>
                                    <p><?php echo ucwords($rm['designation']); ?></p>
                                    <p><?php echo ucwords($rm['office_centre']); ?> Office,<span><?php echo $rm['phone']; ?></span></p>
                                    <p><?php echo $rm['languages']; ?></p>
                                    <p><?php echo $rm['official_email']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Escalation Resolution Contact:</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:40%;">
                <div class="container">
                    <h5><b>Mr. Jaidev Sharma</b></h5> 
                    <p>Delhi Office</p>
                    <p>9582528116 <span> <strong>|</strong> English and Hindi</span></p>
                    <p>jaidevs@247around.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">TV Bracket Ordering:</h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:40%;">
                <div class="container">
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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">247around Invoices Related:</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:40%;">
                <div class="container">
                    <h5><b>Mr. Anuj Aggarwal</b></h5> 
                    <p>Delhi Office</p>
                    <p>8826423424</p>
                    <p>anuj@247around.com</p>
                </div>
            </div>
        </div>
    </div>
</div>