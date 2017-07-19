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
                <th width="200px;" class='jumbotron' style="text-align: center">Public Name (Partner Code)</th>
                <th width="500px;" class='jumbotron' style="text-align: center">Appliances/Brands</th>
          	<th class='jumbotron' style="text-align: center">PoC Name</th>
          	<th class='jumbotron' style="text-align: center">PoC Phone</th>
          	<th class='jumbotron' style="text-align: center">PoC Email</th>
          	<th class='jumbotron' style="text-align: center">Owner Name</th>
          	<th class='jumbotron' style="text-align: center">Owner Phone</th>
          	<th class='jumbotron' style="text-align: center">Owner Email</th>
                <th class='jumbotron' style="text-align: center">Go To Invoice Page</th>
          	<th colspan="2" class='jumbotron' style="text-align: center">Action</th>
          </tr>

          
          <?php foreach($query as $key =>$row){?>
          <tr>
            <td><?=($key+1).'.';?></td>
            <td><a href="<?php echo base_url();?>employee/partner/editpartner/<?=$row['id'];?>"><?=$row['company_name'];?></a></td>
            <td><?php echo $row['public_name'] ; ?> (<b><?php echo $row['code'] ; ?></b>)</td>
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
          	<td><?=$row['owner_name'];?></td>
          	<td>
          	    <?=$row['owner_phone_1'];?>
          	</td>
          	
          	<td><?=$row['owner_email'];?></td>
                <td><a href="<?php echo base_url(); ?>employee/invoice/invoice_summary/partner/<?php echo $row['id']; ?>" target="_blank" class="btn btn-info">Invoice</a></td>
                <td>
                    <a href="javascript:void(0)" class="btn btn-md btn-success"  onclick='return login_to_partner(<?php echo $row['id']?>)' <?php echo ($row['is_active'] == 0)?'disabled=""':'' ?>  title="<?php echo isset($row['clear_text']) && $row['clear_text']?$row['user_name'].'/'.$row['clear_text']:'';?>">Login</a>  
              
                </td>
          	<td><?php if($row['is_active']==1){ ?>
                  <a class="btn btn-md btn-danger" href="<?php echo base_url() ?>employee/partner/deactivate/<?php echo $row['id'] ?>">Deactivate</a>       
                <?php } else {?>
                 <a class="btn btn-md btn-primary" href="<?php echo base_url() ?>employee/partner/activate/<?php echo $row['id'] ?>">Activate</a>                
                <?php } ?>
            </td>
            
          </tr>
          <?php } ?>
        </table>


        
      </div>
    </div>
</div>      
