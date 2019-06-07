<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading"><h3>SF Penalty Summary </h3> </div>
                <div class="panel-body">
                    <form method="post" action="<?php echo base_url() ?>employee/vendor/download_vendor_penalty_summary">
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



