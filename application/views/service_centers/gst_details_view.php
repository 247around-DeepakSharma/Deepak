<div id="page-wrapper" >
    <div class="container">
        <div class="row">
            <div class="panel-body">
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>GST Details</b></div>
                    <div class="panel-body form-horizontal">
                       
                        <div class="col-md-12">
                            <div class="col-md-6 col-md-offset-3">
                                <div  class="form-group ">
                                    <label  for="company_name" class="col-md-4">Company Name</label>
                                    <div class="col-md-8">
                                     <b> <?php echo $company_name?></b>
                                    </div>
                                    
                                </div>
                                <div  class="form-group ">
                                    <label  for="company_name" class="col-md-4">Company Address</label>
                                    <div class="col-md-8">
                                        <b> <?php echo $company_address?></b>
                                    </div>
                                    
                                </div>
                                <div  class="form-group ">
                                    <label  for="company_name" class="col-md-4">Company PAN</label>
                                    <div class="col-md-8">
                                         <b> <?php echo $company_pan_number?></b>
                                    </div>
                                    
                                </div>
                                <div  class="form-group ">
                                    <label  for="company_name" class="col-md-4">GST Registration Done?</label>
                                    <div class="col-md-8">
                                     <b>  <?php if($is_gst == 1){ echo "YES";} else { echo "NO";} ?> </b>
                                    </div>
                                   
                                </div>
                                 <div  class="form-group ">
                                    <label  for="gst_no" class="col-md-4">Company GST No</label>
                                    <div class="col-md-8">
                                        <?php echo $company_gst_number; ?>
                                    </div>
                                    
                                </div>
                                 <div  class="form-group ">
                                    <label  for="gst_no" class="col-md-4">Company GST File</label>
                                    <div class="col-md-8">
                                     <?php if( !empty($gst_certificate_file)){ ?> <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/<?php echo $gst_certificate_file; ?>">View Here</a>  <?php } ?>
                                        
                                    </div>
                                    
                                </div>
                                <div  class="form-group ">
                                    <label  for="gst_no" class="col-md-4">Signature Image File</label>
                                    <div class="col-md-8">
                                     <?php if( !empty($signature_file)){ ?> <a target="_blank" href="https://s3.amazonaws.com/<?php echo  BITBUCKET_DIRECTORY;?>/vendor-partner-docs/<?php echo $signature_file; ?>">View Here</a>  <?php } ?>
                                        
                                    </div>
                                    
                                </div>
                                <div  class="form-group ">
                                    <label  for="gst_no" class="col-md-4">Stamp Image File</label>
                                    <div class="col-md-8">
                                     <?php if( !empty($stamp_file)){ ?> <a target="_blank" href="https://s3.amazonaws.com/<?php echo  BITBUCKET_DIRECTORY;?>/sf-stamp/<?php echo $stamp_file; ?>">View Here</a>  <?php } ?>
                                        
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                   
                    </div>
                </div>

            </div>
        </div>
    </div>
    