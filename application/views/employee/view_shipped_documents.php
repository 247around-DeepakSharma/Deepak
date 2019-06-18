<div class="page-wrapper">
    <div class="row"style="margin:10px"> 
        <div class="clear"></div>
        <div class="panel panel-info  class-md-3" > 
            <div class="panel-heading" style="padding-top:1px;padding-bottom:1px">
                <h4 class="col-md-10" style="color: black;margin: 12px 0px;padding: 0px;">Courier Documents</h4> 
                <a href="<?php echo base_url();?>employee/accounting/shipped_documents" target="_blank" style="text-align:right;margin-left: 7%;background: #2c9d9c;border: #2c9d9c;" class="btn btn-primary">Add Courier Details</a>
            </div>    
            
        </div>
        <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
            }
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('error') . '</strong>
                        </div>';
            }
        ?>
        <?php if($this->session->userdata('user_group') != 'closure'){?>
        <?php }?>
        
            <table class="table table-bordered table-condensed " id="mytable"> 

                <thead> 
                    <tr> 
                        <th class="jumbotron">SNo.</th> 
                        <th class="jumbotron col-sm-2">Sender</th> 
                        <th class="jumbotron col-sm-2">Receiver</th> 
                        <th class="jumbotron">Document Type</th>
                        <th class="jumbotron">Invoice ID</th>
                        <th class="jumbotron">AWB No.</th> 
                        <th class="jumbotron">Courier Name</th> 
                        <th class="jumbotron">Courier File</th>
                        <th class="jumbotron col-sm-2">Contact Person</th>
                        <th class="jumbotron col-sm-1">Shipment Date</th>
                        <th class="jumbotron col-sm-1">Create Date</th>
                        <th class="jumbotron col-sm-2">Remarks</th>
                        <th class="jumbotron col-sm-1">Action</th>
                    </tr> 
                </thead> 
                <tbody> 
                    <?php
                    $start = 0;
                    foreach ($courier_details as $courier) {
                        ?> 
                        <tr> 
                            <td> 
                                <?php echo ++$start ?> 
                            </td> 
                            <td> 
                                <?php 
                                echo $courier->sender_entity_type."(".$courier->sender_entity_name.")";
                                ?> 
                            </td> 
                            <td> 
                                <?php 
                                  echo $courier->receiver_entity_type."(".$courier->receiver_entity_name.")";
                                ?> 
                            </td> 
                            <td> 
                                <?php echo ucfirst($courier->document_type) ?> 
                            </td>
                            <td> 
                                <?php echo ucfirst($courier->partner_invoice_id) ?> 
                            </td>
                            <td> 
                                <?php echo $courier->AWB_no ?> 
                            </td> 
                            <td> 
                                <?php echo ucfirst($courier->courier_name) ?>
                            </td> 
                            <td> 
                                <!--<img src="file/ echo $courier->courier_file ?> "/>--> 
                                <div class="col-md-1">
                                        <?php
                                        $src = base_url() . 'no_image/image.png';
                                        $image_src = $src;
                                        if (isset($courier) && !empty($courier->courier_file)) {
                                            //Path to be changed
                                            $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $courier->courier_file;
                                            $image_src = base_url() . 'images/view_image.png';
                                            ?>
                                    <a href="<?php echo $src ?>" target="_blank" title="View Image"><img src="<?php echo $image_src ?>" width="25px" height="25px" style="border:1px solid black;align-items: center" /></a>
                                    <?php
                                        }
                                        ?>
                                    
                                    </div>
                            </td> 
                            <td> 
                                <?php 
                                if($courier->contact_person_name)
                                    echo ucwords(strtolower($courier->contact_person_name));
                                else
                                    echo ucwords(strtolower($courier->contact_person_id));
                                    ?>
                            </td> 
                            <td> 
                                <?php echo date('d-m-Y', strtotime($courier->shipment_date)) ?> 
                            </td> 
                            <td> 
                                <?php echo date('d-m-Y', strtotime($courier->create_date)) ;?> 
                            </td> 
                            <td> <?php
                                
                                echo ucfirst($courier->remarks); ?> 
                            </td> 
                            <td>
                                <div class="row">
                                    <div class="col-sm-3"style="margin-left:15%; padding: 0%">
                                        <a class="btn btn-primary btn-sm" href="<?php echo base_url();?>employee/accounting/update_shipped_document/<?php echo $courier->id;?>"  target="_blank" title="Update" style="text-align:center">
                                            <i class="fa fa-edit" aria-hidden="true"> </i></a>
                                    </div>
                                    <div class="col-sm-3 border" style="margin-left:15%;padding: 0%">
                                        <a  class="btn btn-danger btn-sm" href="<?php echo base_url();?>employee/accounting/delete_shipped_document/<?php echo $courier->id;?>" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </td> 
                        </tr> 
                        <?php
                    }
                    ?> 
                </tbody> 
            </table>
    </div>
</div>
<script type="text/javascript"> 
        $(document).ready(function() { 
            $("#mytable").dataTable({
                dom: 'Bfrtip',
                buttons: [
                   'excel', 'pageLength'
                ]
            }); 
        }); 
</script>
<style>
    #mytable_filter{
        text-align: right;
    }
</style>