<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>

<script type="text/javascript">
    
     function login_to_partner(partner_id){
        var c = confirm('Login to Partner CRM');
        if(c){
            $.ajax({
                url:'<?php echo base_url()."employee/login/allow_log_in_to_partner/" ?>'+partner_id,
                success: function (data) {
                    //console.log(data);
                    window.open("<?php echo base_url().'partner/home'?>",'_blank');
                }
            });
            
        }else{
            return false;
        }
    }
</script>


<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h1>Partners</h1>
        <?php if($this->session->userdata('user_group') != 'closure'){?>
        <div class="pull-right" style="margin:0px 30px 20px 0px;">
            <a href="<?php echo base_url();?>employee/partner/get_add_partner_form"><input class="btn btn-primary" type="Button" value="Add Partner"></a>
<!--            <a href="<?php echo base_url();?>employee/partner/upload_partner_brand_logo"><input class="btn btn-primary" type="Button" value="Upload Partner Brand Logo" style="margin-left:10px;"></a>-->
        </div>
        <?php }?>
        
        <table style="width:98%;" class="table table-striped table-bordered">
          
          <tr>
          	<th class='jumbotron'>ID</th>
                <th width="200px;" class='jumbotron' style="text-align: center">Company Name</th>
                <th width="200px;" class='jumbotron' style="text-align: center">Login</th>
                <th width="500px;" class='jumbotron' style="text-align: center">Appliances/Brands</th>
          	<th class='jumbotron' style="text-align: center">PoC Name</th>
          	<th class='jumbotron' style="text-align: center">PoC Phone</th>
          	<th class='jumbotron' style="text-align: center">PoC Email</th>
                <th class='jumbotron' style="text-align: center">Customer Care Phone</th>
                <th class='jumbotron' style="text-align: center">Go To Invoice Page</th>
                <th  class='jumbotron' style="text-align: center">Action</th>
                <th class='jumbotron' style="text-align: center">Generate Price</th>
                <th class='jumbotron' style="text-align: center">Modify Price</th>
                <th class='jumbotron' style="text-align: center">Send Summary Email</th>
                <th class='jumbotron' style="text-align: center">View History</th>
                 <th class='jumbotron' style="text-align: center">Notifications</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=($key+1).'.';?></td>
            <td>
                <a href="<?php echo base_url();?>employee/partner/editpartner/<?=$row['id'];?>"><?=$row['company_name'];?></a>
                <br/>
                <strong><?php echo $row['public_name'] ; ?> (<b><?php echo $row['code'] ; ?></b>)</strong>
            </td>
            <td>
                <a href="javascript:void(0)" class="btn btn-sm btn-success"  onclick='return login_to_partner(<?php echo $row['id']?>)' >Login</a>  
            </td>
           
                <td>
                    <?php
                    if (!empty($service_brands[$key])) {
                        $str = "";
                        foreach ($service_brands[$key] as $val) {
                            $str .= ' <b>'.$val['services'] .'</b> - '.$val['brand'].' ,';
                        }
                        echo (rtrim($str,","));
                        
                    }
                    ?>
                </td>

          	<td><?=$row['primary_contact_name'];?></td>
          	<td>
          	    <?=$row['primary_contact_phone_1'];?>
          	</td>
          	<td><?=$row['primary_contact_email'];?></td>
                <td><?=$row['customer_care_contact'];?></td>
                <td><a href="<?php echo base_url(); ?>employee/invoice/invoice_summary/partner/<?php echo $row['id']; ?>" target="_blank" class="btn btn-info">Invoice</a></td>
                
          	<td><?php if($row['is_active']==1){ ?>
                  <a class="btn btn-md btn-danger" href="<?php echo base_url() ?>employee/partner/deactivate/<?php echo $row['id'] ?>">Deactivate</a>       
                <?php } else {?>
                 <a class="btn btn-md btn-primary" href="<?php echo base_url() ?>employee/partner/activate/<?php echo $row['id'] ?>">Activate</a>                
                <?php } ?>
            </td>
            <td>
                <a href="<?php echo base_url();?>employee/service_centre_charges/generate_service_charges_view/<?php echo $row['id'];?>" class="btn btn-md btn-success">Gen. Price</a>  
            </td>
             <td>
                <a href="<?php echo base_url();?>employee/service_centre_charges/show_charge_list/<?php echo $row['id'];?>" class="btn btn-md btn-primary">Modify Price</a>  
            </td>
            <td>
                <a href="<?php echo base_url();?>BookingSummary/send_leads_summary_mail_to_partners/<?php echo $row['id'];?>" class="btn btn-md btn-color">Send Mail</a>  
            </td>
            <td>  <button type="button" class="btn btn-info btn-lg fa fa-eye" data-toggle="modal" data-target="#history_view" onclick="get_history_view(<?php echo $row['id']?>)" style="padding: 11px 6px;margin: 0px 10px;"></button></td>
          <td align="center">
              <?php 
              if(array_key_exists($row['id'],$push_notification)){
                     $tooltipText ='';
                      if(array_key_exists("subscription_count", $push_notification[$row['id']])){
                        $tooltipText =$tooltipText."Subscriptions: ".$push_notification[$row['id']]['subscription_count'];
                      }
                       if(array_key_exists("blocked_count", $push_notification[$row['id']])){
                        $tooltipText = $tooltipText.", Blocked: ".$push_notification[$row['id']]['blocked_count'];
                      }
                  if(isset($push_notification[$row['id']]['blocked_count']) && !isset($push_notification[$row['id']]['subscription_count'])){
                      echo '<button type="button" class="btn btn-info btn-lg glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="'.$tooltipText.'" style="padding: 11px 6px;margin: 0px 10px;"></button>';
                  }
                  else{
                      echo '<button type="button" class="btn btn-info btn-lg " data-toggle="tooltip" data-placement="left" title="'.$tooltipText.'" style="padding: 11px 6px;margin: 0px 10px;"><i class="fa fa-bell" aria-hidden="true"></i></button>';
                  }
              }
              else{
                  echo '<button type="button" class="btn btn-info btn-lg " style="padding: 11px 6px;margin: 0px 10px;"><i class="fa fa-spinner" aria-hidden="true"></i></button>';
              }
              ?>
          </td>
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
<!-- This model class is used Update History View-->
  <div class="modal fade" id="history_view" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Updated History View</h4>
        </div>
        <div class="modal-body">
            <div id="table_container"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
<script>
    function get_history_view(partnerID){
     $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/get_partner_vendor_updation_history_view/'+partnerID+'/partners/trigger_partners',
                success: function(response) {
                    console.log(response);
                    $("#table_container").html(response);
                }
            });
     }
    </script>