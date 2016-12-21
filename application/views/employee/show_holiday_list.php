<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;"><b>Holiday List 2017</b></center></div>
        <div style='border-radius: 5px;background: #EEEEEE;margin-top: 10px;margin-bottom: 10px;width:330px;' class='col-md-6'><b>NOTE:</b> <i>Checkmark shows Holiday declared.</i></div>
        <div class="panel-body">
            <table class="table table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">DATE</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">DAY</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">EVENT</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">DELHI</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">CHENNAI</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">MUMBAI</th>
                        <th class="jumbotron" style="padding:5px;text-align: center">KOLKATA</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($data as $key => $value) { ?>		
                        <tr>
                            <td ><?php echo ($key + 1) . '.' ?></td>
                            <td style="padding:1px;text-align: center"><?php echo date('d M Y', strtotime($value['event_date'])) ?></td>
                            <td style="padding:1px;text-align: center"><?php echo date('l', strtotime($value['event_date'])) ?></td>
                            <td style="padding:1px;text-align: center;"><?php echo $value['event_name'] ?></td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['delhi'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['chennai'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['mumbai'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                            <td style="padding:1px;text-align: center">
                                <?php
                                if ($value['kolkata'] == 1){ ?>
                                <img src="<?php echo base_url()?>images/ok.png" height="20px" width="20px"/>
                                <?php }?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>

        </div>
    </div>
</div>