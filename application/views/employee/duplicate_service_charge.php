<form action="#" method="POST" name="delete_service_charges" id="delete_service_charges">
<table class="table priceList table-striped table-bordered">
    <thead>
        <tr>
            <th>SNo</th>
            <th>Partner ID</th>
            <th>Service ID</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Capacity</th>
            <th>Service/Product</th>
            <th>Service Category</th>
            <?php if($delete){ ?>
            <th>Customer Total</th>
            <th>Partner Offer</th>
            <th>Vendor Payout</th>
            <th>POD</th>
            <th>Upcountry</th>
            
            <th>Flat Partner Offer Upcountry</th>
            <th>Flat Vendor Upcountry Payout</th>
            <th>Flat Customer Upcountry Charges</th>
            <th>Select All <input type="checkbox" id="select_all" /></th>
            <?php } ?>
            
        </tr>
    </thead>
    <tbody>
        <?php foreach($duplicate as $key => $value){ ?>
        <tr>
            <td><?php echo $key+1; ?></td>
            <td><?php if(isset($public_name)) { echo $public_name;}  ?></td>
            <td><?php if(isset($value['services'])) {echo $value['services'];} else { echo $value['service_id'];}; ?></td>
            <td><?php echo $value['brand']; ?></td>
            <td><?php echo $value['category']; ?></td>
            <td><?php echo $value['capacity']; ?></td>
            <td><?php echo $value['product_or_services']; ?></td>
            <td><?php echo $value['service_category']; ?></td>
            <?php if($delete){ ?>
            <td><?php echo $value['customer_total']; ?></td>
            <td><?php echo $value['partner_net_payable']; ?></td>
            <td><?php echo $value['vendor_total']; ?></td>
            <td><?php echo $value['pod']; ?></td>
            <td><?php echo $value['is_upcountry']; ?></td>
            <td><?php echo $value['upcountry_partner_price']; ?></td>
            <td><?php echo $value['upcountry_vendor_price']; ?></td>
            <td><?php echo $value['upcountry_customer_price']; ?></td>
            <td><input type="checkbox" name="delete_charge[]" class="service_charge_id" value="<?php echo $value['id']; ?>" /></td>
            <?php } ?>
            
        </tr>
       <?php  }?>
    </tbody>
    
</table>
</form>
<?php if($delete){ ?>
<div class="col-md-12" style="margin-bottom: 60px;">
    <button onclick="delete_form()" class="btn btn-success col-md-offset-3 btn-md">Delete Charges</button>
</div>
<?php } ?>


<script>
$("#select_all").change(function () {
       $(".service_charge_id").prop('checked', $(this).prop("checked"));
 });
</script>