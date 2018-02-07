<div class="right_col" role="main">
    <?php
    if ($this->session->userdata('error')) {
        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>' . $this->session->userdata('error') . '</strong>
                                    </div>';
    }
    if ($this->session->userdata('success')) {
        echo '<div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>' . $this->session->userdata('success') . '</strong>
                                    </div>';
    }
    ?>
    <div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12" style="margin-top: 60px;">
        <div class="x_panel">
            <div class="x_title">
                <h2>Reset Password</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form class="form-horizontal" action="<?php echo base_url();?>employee/login/process_reset_entity_password" method="post">
                    <div class="form-group">
                        <label for="old_pw">Current password:</label>
                        <input type="password" class="form-control" name="old_pw" id="old_pw" placeholder="Current Password" required="">
                    </div>
                    <div class="form-group">
                        <label for="new_pw">New Password:</label>
                        <input type="password" class="form-control" name="new_pw" id="new_pw" placeholder="New Password" required="">
                    </div>
                    <div class="form-group">
                        <label for="re_new_pw">Reenter Password:</label>
                        <input type="password" class="form-control" name="re_new_pw" id="re_new_pw" placeholder="Confirm Password" required="">
                    </div>
                    <div class="form-group text-center">
                        <input type="hidden" value="<?php echo $this->session->userdata("partner_id");?>" name="entity_id">
                        <input type="hidden" value="<?php echo $this->session->userdata("userType");?>" name="entity_type">
                        <button type="submit" class="btn btn-success">Reset Password</button>
                    </div>     
                </form>
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>