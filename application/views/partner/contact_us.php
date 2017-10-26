<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">247around Point of Contacts:</h4>
</div>

<div class="modal-header">
    <h4 class="modal-title"> First Escalation Point</h4>
</div>
<div class="modal-body">
    <div class="card">
        <div class="container">
            <?php if (isset($account_manager_details) && !empty($account_manager_details)) { ?> 
                <h5><b><?php echo $account_manager_details[0]['full_name'] ?></b></h5> 
                <p><?php echo $account_manager_details[0]['phone'] ?></p>
                <p><?php echo $account_manager_details[0]['official_email'] ?></p>
            <?php } else { ?> 
                <h5><b>Mr. Vikas Singh</b></h5> 
                <p>9910043586</p>
                <p>escalations@247around.com</p>
            <?php } ?>

        </div>
    </div>
</div>
<div class="modal-header">
    <h4 class="modal-title"> Second Escalation Point</h4>
</div>
<div class="modal-body">
<?php if (isset($rm_details) && !empty($rm_details)) {
        foreach ($rm_details as $rm) { ?>
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
    <?php }
} ?>
</div>
<div class="modal-header">
    <h4 class="modal-title">Technical – CRM Related</h4>
</div>
<div class="modal-body">
    <div class="card">
        <div class="container">
            <h4><b>Mr. Anuj Aggarwal</b></h4> 
            <p>Director & CTO</p>
            <p>8826423424</p>
            <p>anuj@247around.com</p>
        </div>
    </div>
</div>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">247around Invoices Related</h4>
</div>
<div class="modal-body">
    <div class="card">
        <div class="container">
            <h5><b>Mr. Anuj Aggarwal</b></h5> 
            <p>Delhi Office</p>
            <p>8826423424</p>
            <p>anuj@247around.com</p>
        </div>
    </div>
</div>