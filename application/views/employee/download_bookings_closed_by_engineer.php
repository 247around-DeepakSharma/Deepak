<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading"><h3><i class="fa fa-download" aria-hidden="true" style="margin-right: 5px"></i>Download Bookings Closed By Engineer </h3> </div>
                <div class="panel-body">
                    <?php if($this->session->userdata('error')) { 
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                    ?>
                    <form method="post" action="<?php echo base_url() ?>employee/engineer/download_engineer_closed_bookings">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Select Service Center</th>
                            <th>Start Date and End Date</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <th style="width:25%;">
                                <select class="form-control" id="service_center" name="service_center[]" placeholder="" multiple>
                                    <option selected value="All">All</option>
                                    <?php
                                    if(!empty($vendor)){
                                        foreach ($vendor as $key => $value) {                                          
                                    ?>    
                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                    <?php   
                                        }
                                    }
                                    ?>
                                </select>
                            </th>
                            <th style="width:25%;"> 
                                <input type="text" class="form-control" name="daterange" id="daterange"  />
                            </th>
                            <th style="width:25%;"> 
                                <button type="submit" class="btn btn-primary">Download</button>
                            </th>
                        </tr>
                    </table>
                    </form>
                </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){ $this->session->unset_userdata('error'); echo $this->session->userdata('error');}?>
<script type="text/javascript">
    $("#service_center").select2();
     
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01") ?>',
            endDate: '<?php echo date('Y-m-d'); ?>'
        });
    });
</script>



