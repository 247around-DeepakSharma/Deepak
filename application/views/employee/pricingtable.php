<?php if(isset($source)){ ?>

<!--<script type="text/javascript">
  $(function() {
    get_pricing_details();
  });

</script>-->
<script src="<?php echo base_url()?>js/report.js"></script>
<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-6 ">
             <h1 class="page-header"><b>Pricing Table</b></h1>
         </div>
         <div class="pull-right" style="margin-top: 40px;">
           <input input="text" placeholder="Search" class="form-control" id="search" />
         </div>
          
          <div class="col-md-12">
              <ul class="vendor_performance ">
                <li class="col-md-2">
                    <select onchange="get_pricing_details()" class="form-control"  id="source" >
                      <option value="" selected="">All Source</option>
                      <?php foreach ($source as  $value) { ?>
                        <option value="<?php echo $value['code'] ?>"> <?php echo $value['source']; ?></option>
                       <?php } ?>
                               
                    </select>
                </li>
<!--                <li class="col-md-2">
                        <select onchange="get_pricing_details()" class="form-control"  id="city" name="city" >
                           <option  disabled>Select city</option>
                           <option value="" selected>All City</option>
                           <?php foreach ($city as $cities) { ?>
                            <option value="<?php echo $cities['City'] ?>"> <?php echo $cities['City']; ?></option>
                          <?php } ?>
                                
                        </select>
                </li>-->
                <li class="col-md-2" >
                    <select  onchange="get_pricing_details()" class="form-control"  id="service" name="service" >
                        <option  disabled>Select Service</option>
                        <option value="">All Service</option>
                           <?php foreach ($services as $service) { ?>
                            <option value="<?php echo $service['id'] ?>"> <?php echo $service['services']; ?></option>
                          <?php } ?>
                               
                    </select>
                </li>

                <li class="col-md-2" >
                    <select onchange="get_pricing_details()" class="form-control"  id="category" name="category" >
                        <option  disabled>Select Category</option>
                        <option value="" selected>All Category</option>
                         <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo $category['category'] ?>"> <?php echo $category['category']; ?></option>
                          <?php } ?>
                       
                    </select> 
                </li>
                
                 <li class="col-md-2" >
                    <select onchange="get_pricing_details()" class="form-control"  id="capacity" name="capacity" >
                        <option  disabled>Select Capacity</option>
                        <option value="" selected>All Capacity</option>
                         <?php foreach ($capacities as $capacity) { ?>
                            <option value="<?php echo $capacity['capacity'] ?>"> <?php echo $capacity['capacity']; ?></option>
                          <?php } ?>
                       
                    </select> 
                </li>
                
                <li class="col-md-2"  style="border: 1px solid #bbb;" >
                    <select onchange="get_pricing_details()" class="form-control"  id="appliances" name="appliances" >
                        <option  disabled>Select Appliances</option>
                        <option value="" selected>All Appliances</option>
                        <?php foreach ($appliances as $service_category) { ?>
                            <option value="<?php echo $service_category['service_category'] ?>"> <?php echo $service_category['service_category']; ?></option>
                          <?php } ?>
                       
                    </select> 
                </li>
               
        </ul>
          </div>
          <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif"></div>
          <div class='col-md-12' style="margin-top:20px;">          
          <table class="table table-bordered  table-hover table-striped data paginated"  id="mytable" >
          
          </table>
          
          </div>
        <!-- end of row-->
      </div>
   </div>
</div>

<script type="text/javascript">
 $('#source').select2();
 $('#city').select2();
 $('#service').select2();
 $('#category').select2();
 $('#capacity').select2();
 $('#appliances').select2();

 $("#search").keyup(function () {
    var value = this.value.toLowerCase().trim();

    $("table tr").each(function (index) {
        if (!index) return;
        $(this).find("td").each(function () {
            var id = $(this).text().toLowerCase().trim();
            var not_found = (id.indexOf(value) == -1);
            $(this).closest('tr').toggle(!not_found);
            return not_found;
        });
    });
});
</script>



<?php  } if(isset($price)){  ?>

 <table class="table table-bordered  table-hover table-striped data"  >
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Category</th>
                    <th>Capacity</th>
                    <th>Appliance</th>
                    <th>Type</th>
                    <th>Active</th>
                    <th>Check box</th>
                    <th>Vendor Basic Charge</th>
                    <th>Vendor Tax</th>'
                    <th>Around Basic Charge</th>
                    <th>Around Tax</th>
                    <th>Customer Total</th>
                    <th>Partner Net Payable</th>
                    <th>Customer Net Payable</th>
                    <th>Action</th>
                </tr>
            </thead>
          
            <tbody>
            <?php $i = 1; foreach($price as $pricing_details){ ?>
            <tr>
            <td><?php echo $pricing_details['services']?></td>
            <td><?php echo $pricing_details['category']?></td>
            <td><?php echo $pricing_details['capacity']?></td>
            <td><?php echo $pricing_details['service_category']?></td>
             <td><?php echo $pricing_details['product_or_services']?></td>
            <td><p  id="<?php echo "active_p".$i?>" class="displaytrue"><?php echo $pricing_details['active']?></p>
             <input  type="text" name="acitve" value="<?php echo $pricing_details['active']?>" class="form-control displayfalse" id="active_input<?php echo $i; ?>"></input>
            </td>
            <td><p id="checkbox_p<?php echo $i; ?>" class="displaytrue"><?php echo $pricing_details['check_box']?></p>
            <input  type="text" name="check_box" value="<?php echo $pricing_details['check_box']?>" class="form-control displayfalse" id="checkbox_input<?php echo $i; ?>"></input>
            </td>
            <td><p id="vendor_basic_charges_p<?php echo $i; ?>" class="displaytrue" ><?php echo round($pricing_details['vendor_basic_charges'], 2);?></p>
             <input  type="text" name="vendor_basic_charges" value="<?php echo round($pricing_details['vendor_basic_charges'], 2)?>" class="form-control displayfalse" id="vendor_basic_charges_input<?php echo $i; ?>"></input>

            </td>
            <td><p id="vendor_tax_basic_charges_p<?php echo $i; ?>" class="displaytrue"><?php echo round($pricing_details['vendor_tax_basic_charges'], 2);?></p>
            <input   type="text" name="vendor_tax_basic_charges" value="<?php echo round($pricing_details['vendor_tax_basic_charges'],2)?>" class="form-control displayfalse" id="vendor_tax_basic_charges_input<?php echo $i; ?>"></input>
            </td>
            <td><p id="around_basic_charges_p<?php echo $i; ?>" class="displaytrue"> <?php echo round($pricing_details['around_basic_charges'] , 2) ?></p>
            <input  type="text" name="around_basic_charges" value="<?php echo round($pricing_details['around_basic_charges'],2)?>" class=" displayfalse form-control" id="around_basic_charges_input<?php echo $i; ?>"></input>
            </td>
            <td><p id="around_tax_basic_charges_p<?php echo $i; ?>" class="displaytrue"><?php echo round($pricing_details['around_tax_basic_charges'],2)?></p>
            <input type="text" name="around_tax_basic_charges" value="<?php echo round($pricing_details['around_tax_basic_charges'],2)?>" class="form-control displayfalse" id="around_tax_basic_charges_input<?php echo $i; ?>"></input>
            </td>
            <td><p id="customer_total_p<?php echo $i; ?>" class="displaytrue"><?php echo round($pricing_details['customer_total'], 2)?></p>
              <input  type="text" name="customer_total" value="<?php echo round($pricing_details['customer_total'],2)?>" class="form-control displayfalse" id="customer_total_input<?php echo $i; ?>"></input>
              </td>
            <td><p id="partner_net_payable_p<?php echo $i; ?>" class="displaytrue"><?php echo round($pricing_details['partner_net_payable'],2)?></p>
             <input type="text" name="partner_net_payable" value="<?php echo round($pricing_details['partner_net_payable'],2)?>" class="form-control displayfalse" id="partner_net_payable_input<?php echo $i; ?>"></input></td>
            <td><p id="customer_net_payable_p<?php echo $i; ?>" class="displaytrue"><?php echo round($pricing_details['customer_net_payable'],2)?></p>
             <input type="text" name="customer_net_payable" value="<?php echo round($pricing_details['customer_net_payable'],2)?>" class="form-control displayfalse" id="customer_net_payable_input<?php echo $i; ?>"></input></td>
            <td><button class="btn btn-success btn-md displaytrue " onclick="displayPricetableInput(<?php echo $i; ?>)" id="edit<?php echo $i; ?>">Edit</button>
            <button class="btn btn-success btn-md displayfalse"  onclick ="editPriceTable(<?php echo $i; ?>, <?php echo $pricing_details['id'] ;?>)" id="submit<?php echo $i; ?>">Submit</button>
            <button  style="margin-top:10px;" class="btn btn-primary btn-md displayfalse" onclick="displayPricetableInput(<?php echo $i; ?>)" id="cancel<?php echo $i; ?>">Cancel</button>
            </td>
            </tr>
           
            <?php $i++;} ?>
            </tbody>
          </table>

<?php } ?>

<style type="text/css">
  .displaytrue{
    display: inherit;
  }

  .displayfalse{
    display: none;
  }
</style>

<style type="text/css">
  div.pager {
    text-align: center;
    margin: 1em 0;
  }

  div.pager span {
    display: inline-block;
    width: 1.8em;
    height: 1.8em;
    line-height: 1.8;
    text-align: center;
    cursor: pointer;
    background: #bce8f1;
    color: #fff;
    margin-right: 0.5em;
  }

  div.pager span.active {
    background: #c00;
  }
</style>
