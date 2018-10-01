<style>
    table tr td{
        text-align: center;
    }
</style>
<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;"><b>Partner Contract List</b></center></div>
        <div class='col-md-12'>
        <div style='border-radius: 5px;background: #EEEEEE;margin-top: 10px;margin-bottom: 10px;width:330px;' class='col-md-6'><b>NOTE:</b> <i>Click on checkmarks to view documents.</i></div>
        </div>
        <div class="panel-body">
                <table class="table table-condensed table-bordered table-striped table-responsive">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">PARTNER NAME</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">CONTRACT</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">START DATE</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">END DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 0;
                    foreach($data as $value){ $i++; ?>		
                    <tr>
                        <td ><?php echo $i.'.'?></td>
                        <td><?php echo $value->public_name; ?></td>
                        <td><?php if($value->collateral_tag != NULL){ ?> 
                            <img src="<?php echo base_url()?>images/ok.png" width="20" height="20" onclick="show_contract_file('<?php echo $value->file; ?>')"/>
                            <?php } ?>
                        </td>
                        <td><?php echo $value->start_date; ?></td>
                        <td><?php echo $value->end_date; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<script>
    function show_contract_file(filename){
       var href = 'https://s3.amazonaws.com/bookings-collateral-test/vendor-partner-docs/'+filename;
       window.open(href,'_blank');
    }
</script>
