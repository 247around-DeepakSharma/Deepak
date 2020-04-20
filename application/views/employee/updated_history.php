<style>
    #bracket_allocation_table_filter{
        display:none;
    }
</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/directives/directives.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<div class="right_col" role="main"  >
    <h3 align="center"><?php echo $entity ?> Updated Tables History View</h3>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" style="height: auto;">
                            <table id="bracket_allocation_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th><?php echo $entity ?></th>
                                        <th>Updated By</th>
                                        <th>Updated Date</th>
                                        <th>Updated Fields</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i=0;
                                    foreach($updation_history as $partner=>$data){
                                        foreach($data['data'] as $index=>$updated_columns){
                                            $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $data['public_name'];?></td>
                                       <td><?php echo $data['updated_by'][$index]?></td>
                                         <td><?php echo date("d-M-Y", strtotime($data['update_date'][$index]))?></td>
                                        <td><?php echo implode(",<br>",$updated_columns)?></td>
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                    </div>
                   
                </div>
            
            </div>
        </div>