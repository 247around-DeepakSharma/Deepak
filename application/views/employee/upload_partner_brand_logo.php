<div id="page-wrapper" style="margin-top: 30px;">
    <div class="row">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4>Upload Partner Brand Logo</h4>
            </div>
            <div class="panel-body">
                <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if($this->session->userdata('failed')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('failed') . '</strong>
                    </div>';
                    }
                    ?>
                <form enctype="multipart/form-data" action="<?php echo base_url(); ?>employee/partner/process_upload_partner_brand_logo" method="post" class="form-inline">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Choose Files</label>
                            <input type="file" class="form-control" name="partner_brand_logo" multiple/>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <select  class="booking_source form-control"  id="partner" name="partner" required>
                                <option selected="selected" disabled="disabled">Select Partner</option>
                                <?php foreach ($partner as $key => $values) { ?>
                                <option  value="<?php echo $values['partner_id']; ?>">
                                <?php echo $values['source']; }    ?>
                                </option>
                            </select>
                            <input type="hidden" name="partner_name" id="partner_name">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <input class="form-control btn btn-md btn-success" type="submit" value="UPLOAD"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
<script>
  $(document).ready(function() {
    $("#partner").change(function(){
        var partner_name = $("#partner option:selected").text();
      $("#partner_name").val(partner_name);
    });
  });
</script>



