<div  id="page-wrapper">
    <div class="row">
        <div class="row">
            <h1 class="col-md-6 col-sm-12 col-xs-12">Dealers</h1>
        
        <?php if($this->session->userdata('user_group') != 'closure'){?>
            <div class="col-md-6 col-sm-12 col-xs-12" style="margin-top: 20px;margin-bottom: 10px;">
            <a href="<?php echo base_url();?>employee/dealers/add_dealers_form"><input class="btn btn-primary pull-right" type="Button" value="Add Dealer"></a>
        </div>
        <?php }?>
        </div>
        
        <div class="row">
            <div class="dealer_listing container-fluid">
                <table class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Dealer Name</th>
                            <th>Partner-Appliance-Brands</th>
                            <th>Dealer Phone Number</th>
                            <th>Dealer Email</th>
                            <th>Owner Name</th>
                            <th>Owner Phone Number</th>
                            <th>Owner Email</th>
                            <th>City</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach($dealers as $key => $value) { ?>
                        <tr>
                            <td><?php echo $sn; ?></td>
                            <td><?php echo $value['dealer_name']?></td>
                            <td><?php
                                    if (!empty($dealers_mapping[$key])) {
                                        $str = "";
                                        foreach ($dealers_mapping[$key] as $val) {
                                            $str .= ' <b>'.$val['public_name'] .'</b> - '.$val['services'].'</b> - '.$val['brand'].' ,';
                                        }
                                        echo (rtrim($str,","));

                                    }
                                    ?>
                            </td>
                            <td><?php echo $value['dealer_phone_number_1']?></td>
                            <td><?php echo $value['dealer_email']?></td>
                            <td><?php echo $value['owner_name']?></td>
                            <td><?php echo $value['owner_phone_number_1']?></td>
                            <td><?php echo $value['owner_email']?></td>
                            <td><?php echo $value['city']?></td>
                            <td> <?php if($value['active'] === '1') { ?>
                                <span class="label label-success">Active</span>
                                <?php } else if($value['active'] === '0'){ ?>
                                <span class="label label-danger">Deactivate</span>
                                <?php } ?>
                            </td>
                            
                        </tr>
                        <?php $sn++; }?>
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
        
</div>