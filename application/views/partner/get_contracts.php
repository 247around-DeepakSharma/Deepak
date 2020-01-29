<div class="right_col" role="main">
    <a href="<?php echo base_url(); ?>partner/download_price_sheet" class="btn btn-success" style="float:right;">Download Price Sheet</a>
    <table class="table table-bordered table-hover table-striped" style="background: #fff;">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Contract Type</th>
                                <th>Contract File</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <thead>
                            <?php
                            $sn =1;
                            foreach($contracts as $contractDetails){
                                ?>
                            <tr>
                                <td><?php echo $sn;?></td>
                                <td><?php echo $contractDetails['collateral_type'] ?></td>
                                <td><a target="_blank" href=<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$contractDetails['file']?>><?php echo $contractDetails['file'] ?></a></td>
                                <td><?php echo $contractDetails['document_description'] ?></td>
                                <td><?php echo date("d-M-Y", strtotime($contractDetails['start_date'])) ?></td>
                                <td><?php echo date("d-M-Y", strtotime($contractDetails['end_date'])) ?></td>
                                </tr>
                            <?php
                            $sn++;
                            }
                            ?>
                        </thead>
                        
                    </table>
</div>