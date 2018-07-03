    1<div id="page-wrapper" >
<div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
                 <?php if ($this->session->userdata('failed')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><?php echo $this->session->userdata('failed') ?></strong>
                    </div>
             <?php } ?>
        
           
                 <?php if ($this->session->userdata('success')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong><?php echo $this->session->userdata('success') ?></strong>
                    </div>
             <?php } ?>
        
             <?php
//             
               $this->session->unset_userdata('success');
               $this->session->unset_userdata('failed');
//            
             ?>
        
<div class="panel panel-info" >
    <div 
        class="panel-heading">
        Annual Charges Table
        
               </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Partner Name</th>
                            <th>Invoice ID</th>   
                            <th>From</th>
                            <th>To</th>
                            <th>Amount Paid</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($annual_charges_data as $row)  
                            {  //print_r($row);
                               ?>
                        <tr>
                            <td><?php echo $row->public_name;?></td>
                            <td><a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY?>/invoices-excel/<?php echo $row->invoice_file_excel;?>"><?php echo $row->invoice_id;?></a></td>
                            <td><?php echo date('jS M, Y', strtotime($row->from_date));?></td>
                            <td><?php echo date('jS M, Y', strtotime($row->to_date));?></td>
                            <td><?php echo $row->amount_collected_paid;?></td>
                           
                           
                        </tr>
                        <?php }  
                            ?>  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

   