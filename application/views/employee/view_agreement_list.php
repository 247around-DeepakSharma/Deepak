<?php $s3_folder = 'sf_agreements'; 
$link_url = S3_WEBSITE_URL . $s3_folder . '/';
?>
<div class="container">
<table class="table table-bordered table-responsive-md table-striped text-center">
            <thead>
              <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Name</th>
                <th class="text-center">Agreement Accepted</th>
                <th class="text-center">Agreement Link</th>
              </tr>
            </thead>
            <tbody>
            <?php
                if(count($results)>0):
                    foreach($results as $rows):?>
                      <tr>
                        <td class="pt-3-half" contenteditable="false"><?php echo $rows['id']; ?></td>
                        <td class="pt-3-half" contenteditable="false"><a href="<?php echo base_url();?>employee/vendor/editvendor/<?php echo $rows['id']; ?>"><?php echo $rows['name']; ?></a></td>
                        <td class="pt-3-half" contenteditable="false"><?php echo $rows['agreement_status']; ?></td>
                        <td class="pt-3-half" contenteditable="false"><a href="<?php echo $link_url.$rows['agreement_file']?>" target="_blank"><?php echo $rows['agreement_file']; ?></a></td>
                      </tr>
                <?php
                    endforeach;
                else: ?>
                <tr>
                    No record found
                </tr>
                <?php
                endif;
                ?>
        </tbody>
      </table>
</div>