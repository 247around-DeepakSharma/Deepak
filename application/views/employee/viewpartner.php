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
      <div>
       
        <h1>Partners</h1>
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
        <div class="pull-right" style="margin:0px 30px 20px 0px;">
            <a href="<?php echo base_url();?>employee/partner/get_add_partner_form"><input class="btn btn-sm btn-primary" type="Button" value="Add Partner"></a>
            <a href="<?php echo base_url();?>employee/partner/download_partner_summary_details" class="btn btn-sm btn-success">Download Partner List</a>
<!--            <a href="<?php echo base_url();?>employee/partner/upload_partner_brand_logo"><input class="btn btn-primary" type="Button" value="Upload Partner Brand Logo" style="margin-left:10px;"></a>-->
        </div>
        <?php }?>
        
        <table class="table table-striped table-bordered">
          
          <tr>
          	<th class='jumbotron'>ID</th>
                <th class='jumbotron' style="text-align: center">Company Name</th>
                <th class='jumbotron' style="text-align: center">Login</th>
                <th class='jumbotron' style="text-align: center">Appliances/Brands</th>
          	<th class='jumbotron' style="text-align: center">PoC Name</th>
          	<th class='jumbotron' style="text-align: center">PoC Phone</th>
          	<th class='jumbotron' style="text-align: center">PoC Email</th>
                <th class='jumbotron' style="text-align: center">Customer Care Phone</th>
                <th class='jumbotron' style="text-align: center">Prepaid</th>
                <th class='jumbotron' style="text-align: center">Go To Invoice Page</th>
                <th class='jumbotron' style="text-align: center">Action</th>
                <th class='jumbotron' style="text-align: center">Generate Price</th>
                <th class='jumbotron' style="text-align: center">Modify Price</th>
                <th class='jumbotron' style="text-align: center">Send Summary Email</th>
                <th class='jumbotron' style="text-align: center">Notifications</th>
          </tr>

          
          <?php foreach($query as $key =>$row){ ?>
          <tr>
            <td><?=($key+1).'.';?></td>
            <td>
                <a href="<?php echo base_url();?>employee/partner/editpartner/<?=$row['id'];?>"><?=$row['company_name'];?></a>
                <br/>
                <strong><?php echo $row['public_name'] ; ?> (<b><?php echo $row['code'] ; ?></b>)</strong>
            </td>
            <td>
                <a href="javascript:void(0)" class="btn btn-sm btn-success"  onclick='return login_to_partner(<?php echo $row['id']?>)' title="<?php echo isset($row['clear_text']) && $row['clear_text']?$row['user_name'].'/'.$row['clear_text']:'';?>"><i class="fa fa-sign-in" aria-hidden="true"></i></a>  
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
                <td><?php if($row['is_prepaid'] == 1){?> <i class="fa fa-credit-card fa-2x" aria-hidden="true"></i><?php }?></td>
                <td><a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/invoice/invoice_summary/partner/<?php echo $row['id']; ?>" target="_blank" title="Go To Invoice"><i class="fa fa-inr" aria-hidden="true"></i></a></td>
                
          	<td><?php if($row['is_active']==1){ ?>
                    <a class="btn btn-sm btn-primary" href="<?php echo base_url() ?>employee/partner/deactivate/<?php echo $row['id'] ?>" title="Deactivate"><i class="fa fa-check" aria-hidden="true"></i></a>       
                <?php } else {?>
                    <a class="btn btn-sm btn-danger" href="<?php echo base_url() ?>employee/partner/activate/<?php echo $row['id'] ?>" title="Activate"><i class="fa fa-ban" aria-hidden="true"></i></a>                
                <?php } ?>
            </td>
            <td>
                <a  class="btn btn-sm btn-success" href="<?php echo base_url();?>employee/service_centre_charges/generate_service_charges_view/<?php echo $row['id'];?>" title="Generate charge"><i class="fa fa-plus" aria-hidden="true"></i></a>  
            </td>
             <td>
                 <a  class="btn btn-sm btn-warning" href="<?php echo base_url();?>employee/service_centre_charges/show_charge_list/<?php echo $row['id'];?>" title="Modify charge"><i class="fa fa-pencil" aria-hidden="true"></i></a>  
            </td>
            <td>
                <a href="<?php echo base_url();?>BookingSummary/send_leads_summary_mail_to_partners/<?php echo $row['id'];?>" class="btn btn-sm btn-color" title="Send Summary Email"><i class="fa fa-envelope" aria-hidden="true"></i></a>  
            </td>
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
                  else if(isset($push_notification[$row['id']]['unsubscription_count']) && !isset($push_notification[$row['id']]['subscription_count'])){
                      echo '<button type="button" class="btn btn-info btn-lg " data-toggle="tooltip" data-placement="left" title="'.$tooltipText.'" style="padding: 11px 6px;margin: 0px 10px;"><i class="fa fa-bell-slash" aria-hidden="true"></i></button>';
                  }
                  else if(isset($push_notification[$row['id']]['subscription_count'])){
                       echo '<button type="button" class="btn btn-info btn-lg " data-toggle="tooltip" data-placement="left" title="'.$tooltipText.'" style="padding: 11px 6px;margin: 0px 10px;"><i class="fa fa-bell" aria-hidden="true"></i></button>';
                  }
              }
              else{
                  echo '<button type="button" class="btn btn-sm btn-info title="Notification"><i class="fa fa-spinner" aria-hidden="true"></i></button>';
              }
              ?>
          </td>
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');}?>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');}?>
