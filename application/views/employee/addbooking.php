<?php
                        $brands=array('appliance_brand1','appliance_brand2','appliance_brand3','appliance_brand4');
                        $category=array('appliance_category1','appliance_category2','appliance_category3','appliance_category4');
                        $capacity=array('appliance_capacity1','appliance_capacity2','appliance_capacity3','appliance_capacity4');
                        $priceList=array('priceList1','priceList2','priceList3','priceList4');
                        $i=0;
                        $j=1;
                        $k=2;
                        $l=3;

                    ?>


<script>

    function getBrandForService(service_id)
        {
            var service_id = $("#service_id").val();
            //alert(service_id);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/getBrandForService/' + service_id,
                success: function (data) {
                    $("#appliance_brand1,#appliance_brand2,#appliance_brand3,#appliance_brand4").html(data);
                    //$("#appliance_brand").html(data);
                }
            });
        }

    function getCategoryForService(service_id)
        {
            $("#priceList").html("<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>");

        var service_id = $("#service_id").val();
                //alert(service_id);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/getCategoryForService/' + service_id,
                    success: function (data) {
                      $("#appliance_category1,#appliance_category2,#appliance_category3,#appliance_category4").html(data);
                      //$("#appliance_category").html(data);

                //getCapacityForCategory(service_id, $("#appliance_category").val());
                getCapacityForCategory(service_id, $("#appliance_category1,#appliance_category2,#appliance_category3,#appliance_category4").val());
            }
        });
    }

    function getCapacityForCategory(service_id, category, i)
        {

      $("#priceList[i]").html("<tr><th>Service Category</th><th>Total Charges</th><th>Selected Services</th></tr>");

        var service_id = $("#service_id").val();
        //var category= $("#appliance_category").val();
                //alert(category);

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/getCapacityForCategory/' + service_id + "/" + category,
                    success: function (data) {
                      if(i==0)
                      {

                      $("#appliance_capacity1").html(data);

                                if (data != "<option></option>") {
                                  var capacity= $("#appliance_capacity1").val();

                    getPricesForCategoryCapacity(i);
                } else {

                    $("#appliance_capacity1").html(data);
                    var capacity="NULL";
                    getPricesForCategoryCapacity(i);
                }
                //alert (data);
                }
                else if(i==1)
                       {

                      $("#appliance_capacity2").html(data);

                                if (data != "<option></option>") {
                                  var capacity= $("#appliance_capacity2").val();

                    getPricesForCategoryCapacity(i);
                } else {

                                                $("#appliance_capacity2").html(data);
                    getPricesForCategoryCapacity(i);
                }
                //alert (data);
                }

                else if(i==2)
                       {

                      $("#appliance_capacity3").html(data);

                                if (data != "<option></option>") {

                                  var capacity = $("#appliance_capacity3").val();
                    getPricesForCategoryCapacity(i);
                } else {

                                  $("#appliance_capacity3").html(data);
                    getPricesForCategoryCapacity(i);
                }

                }
                else if(i==3)
                     {

                      $("#appliance_capacity4").html(data);

                                if (data != "<option></option>") {

                                  var capacity = $("#appliance_capacity4").val();
                    getPricesForCategoryCapacity(i);
                } else {

                                          $("#appliance_capacity4").html(data);
                    getPricesForCategoryCapacity(i);
                }

                }


            }
        });
    }

    function getPricesForCategoryCapacity(i)
        {
            var service_id = $("#service_id").val();

          if(i==0)
          {
             var category = $("#appliance_category1").val();
             if($("#appliance_capacity1").val()!="")
             {
             var capacity= $("#appliance_capacity1").val();
             }
             else{
              var capacity = "NULL";
             }
           }
          else if(i==1)
          {
            var category = $("#appliance_category2").val();
            if($("#appliance_capacity2").val()!="")
             {
             var capacity= $("#appliance_capacity2").val();
             }
             else{
              var capacity = "NULL";
             }

          }
          else if(i==2)
          {
            var category = $("#appliance_category3").val();
            if($("#appliance_capacity3").val()!="")
             {
             var capacity= $("#appliance_capacity3").val();
             }
             else{
              var capacity = "NULL";
             }

          }
          else if(i==3)
          {
            var category = $("#appliance_category4").val();
            if($("#appliance_capacity4").val()!="")
             {
             var capacity= $("#appliance_capacity4").val();
             }
             else{
              var capacity = "NULL";
             }

          }

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/getPricesForCategoryCapacity/' + service_id + "/" + category + "/" + capacity,
                success: function (data) {
                  if(i==0)
                  {
                    $("#priceList1").html(data);
                  }
                  else if(i==1)
                  {
                    $("#priceList2").html(data);
                  }
                  else if(i==2)
                  {
                    $("#priceList3").html(data);
                  }
                  else if(i==3)
                  {
                    $("#priceList4").html(data);
                  }

            }
        });

    }

  //function to calculate total price and services

  function service1()
  {
      var total_price=0;

      var service_name ='';
      $('#priceList1 .Checkbox1:checked').each(function(){

      //total_price+=parseInt($(this).val());

      service_name+=($(this).attr('name')).toString()+',';

      });
      //alert(total_price);
      //alert(service_name);
      $('#items_selected1').val(service_name);
      //$('#total_price1').val(total_price);
    }

function service2()
  {
      var total_price=0;

      var service_name ='';
      $('#priceList2 .Checkbox1:checked').each(function(){

      //total_price+=parseInt($(this).val());

      service_name+=($(this).attr('name')).toString()+',';

      });
      //alert(total_price);
      //alert(service_name);
      $('#items_selected2').val(service_name);
      //$('#total_price2').val(total_price);
    }

    function service3()
  {
      var total_price=0;

      var service_name ='';
      $('#priceList3 .Checkbox1:checked').each(function(){

      //total_price+=parseInt($(this).val());

      service_name+=($(this).attr('name')).toString()+',';

      });
      //alert(total_price);
      //alert(service_name);
      $('#items_selected3').val(service_name);
      //$('#total_price3').val(total_price);
    }

    function service4()
  {
      var total_price=0;

      var service_name ='';
      $('#priceList4 .Checkbox1:checked').each(function(){

      //total_price+=parseInt($(this).val());

      service_name+=($(this).attr('name')).toString()+',';

      });
      //alert(total_price);
      //alert(service_name);
      $('#items_selected4').val(service_name);
      //$('#total_price4').val(total_price);
    }

    $(document).ready(function()
    {
      $('#potential_value1, #potential_value2, #potential_value3, #potential_value4, #potential_value1_label, #potential_value2_label, #potential_value3_label, #potential_value4_label, #query_remarks, #booking_remarks, #query_date1, #query_timeslot1,#query_address, #query_pincode,#newbrand1,#newbrand2,#newbrand3,#newbrand4').hide();
      $("#query").click(function()
        {
          $("#booking_timeslot1").hide();
          $("#booking_date1").hide();
          $("#booking_remarks").hide();
          $("#total_price1").hide();
          $("#total_price2").hide();
          $("#total_price3").hide();
          $("#total_price4").hide();
          $("#total_price1_label").hide();
          $("#total_price2_label").hide();
          $("#total_price3_label").hide();
          $("#total_price4_label").hide();
          $("#booking_address").hide();
          $("#booking_pincode").hide();
          $("#potential_value1").show();
          $("#potential_value2").show();
          $("#potential_value3").show();
          $("#potential_value4").show();
          $("#potential_value1_label").show();
          $("#potential_value2_label").show();
          $("#potential_value3_label").show();
          $("#potential_value4_label").show();
          $("#query_address").show();
          $("#query_pincode").show();
          $("#query_timeslot1").show();
          $("#query_date1").show();
          $("#query_remarks").show();
      });
      $("#booking").click(function()
        {
          $("#query_remarks").hide();
          $("#query_date1").hide();
          $("#query_timeslot1").hide();
          $("#query_address").hide();
          $("#query_pincode").hide();
          $("#potential_value1").hide();
          $("#potential_value2").hide();
          $("#potential_value3").hide();
          $("#potential_value4").hide();
          $("#potential_value1_label").hide();
          $("#potential_value2_label").hide();
          $("#potential_value3_label").hide();
          $("#potential_value4_label").hide();
          $("#booking_address").show();
          $("#booking_pincode").show();
          $("#booking_timeslot1").show();
          $("#booking_date1").show();
          $("#booking_remarks").show();
          $("#total_price1").show();
          $("#total_price2").show();
          $("#total_price3").show();
          $("#total_price4").show();
          $("#total_price1_label").show();
          $("#total_price2_label").show();
          $("#total_price3_label").show();
          $("#total_price4_label").show();
      });
    });

  function newApplianceBrand1(brandname)
  {
    //var service_id = $("#service_id").val();
    //alert(service_id);
    if(brandname=='Other')
    {
      //alert("OK");
      $("#newbrand1").show(); 
    }
  }
  function newApplianceBrand2(brandname)
  {
    if(brandname=='Other')
    {
      $("#newbrand2").show(); 
    }
  }
  function newApplianceBrand3(brandname)
  {
    if(brandname=='Other')
    {
      $("#newbrand3").show(); 
    }
  }
  function newApplianceBrand4(brandname)
  {
    if(brandname=='Other')
    {
      $("#newbrand4").show(); 
    }
  }

  

  function validate()
    {
      //alert("OK");
      if(document.myForm.quantity.value=='1' && document.myForm.type.value!= 'Query' && document.myForm.total_price1.value=='')
      {
        alert("Please enter total price for 1st appliance.");
        document.myForm.total_price1.focus() ;
            return false;
      }
      if(document.myForm.quantity.value=='2' && document.myForm.type.value!= 'Query' && (document.myForm.total_price1.value=='' || document.
        myForm.total_price2.value==''))
      {
        alert("Please enter total price for appliance 1 and 2.");
            return false;
      }
      if(document.myForm.quantity.value=='3' && document.myForm.type.value!= 'Query' && (document.myForm.total_price1.value=='' || document.
        myForm.total_price2.value=='' || document.myForm.total_price3.value=='' ))
      {
        alert("Please enter total price for appliance 1,2 and 3.");
            return false;
      }
      if(document.myForm.quantity.value=='4' && document.myForm.type.value!= 'Query' && (document.myForm.total_price1.value=='' || document.
        myForm.total_price2.value=='' || document.myForm.total_price3.value=='' || document.myForm.
        total_price4.value==''))
      {
        alert("Please enter total price for appliance 1,2,3 and 4.");
            return false;
      }

      return true;
    }
</script>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <h1 class="page-header">
                    Add Booking
                </h1>
              <form name="myForm" class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/booking/bookingconfirmation" onsubmit="service1(),service2(),service3(),service4(),amount()" method="POST" enctype="multipart/form-data">

              <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                <label for="name" class="col-md-2">User Name</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="name" value = "<?php echo $name; ?>" disabled>
                  <?php echo form_error('name'); ?>
                </div>
              </div>

                  <div class="form-group <?php
                       if (form_error('booking_primary_contact_no')) {
                           echo 'has-error';
                  } ?>">
                      <label for="booking_primary_contact_no" class="col-md-2">Primary Contact Number</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control"  id="booking_primary_contact_no" name="booking_primary_contact_no" value = "<?php echo $phone_number; ?>">
                          <?php echo form_error('booking_primary_contact_no'); ?>
                      </div>
                  </div>

                  <div class="form-group <?php if (form_error('booking_alternate_contact_no')) {echo 'has-error'; } ?>">
                <label for="booking_alternate_contact_no" class="col-md-2">Alternate Contact No</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  id="booking_alternate_contact_no" name="booking_alternate_contact_no" value = "<?php if(isset($alternate_phone_number)){echo $alternate_phone_number;} ?>">
                  <?php echo form_error('booking_alternate_contact_no'); ?>
                </div>
              </div>

              <div class="form-group <?php if( form_error('type') ) { echo 'has-error';} ?>">
                  <label for="type" class="col-md-2">Type</label>
                  <div class="col-md-6">

                    <input style="width:150px;height:20px;display:inline;" id="query" type="radio" class="form-control" name="type" value="Query" required>Query
                    <input style="width:150px;height:20px;display:inline;" id="booking" type="radio" class="form-control" name="type" value="Booking" required>Booking

                    <?php echo form_error('type'); ?>


                </div>
              </div>

              <div class="form-group <?php if (form_error('source_code')) { echo 'has-error';} ?>">
                <label for="source_name" class="col-md-2">Booking Source</label>
                <div class="col-md-6">

                    <select type="text" class="form-control"  id="source_code" name="source_code" value = "<?php echo set_value('source_code'); ?>" required>
                      <?php foreach ($sources as $key => $values) { ?>

                        <option  value=<?= $values->code; ?>>
                            <?php echo $values->source; }    ?>
                        </option>

                      <?php echo form_error('source_code'); ?>

                    </select>
                </div>
              </div>
                <div class="form-group ">
                <label for="source_name" class="col-md-2">Partner Source</label>
                <div class="col-md-6">

                    <select class="form-control"  id="partner_source" name="partner_source"  >
                      <option value="">Please Select Partner source</option>
                      <option>Snapdeal</option>
                      <option>Flipkart</option>
                      <option>Ebay</option>
                      <option>Offline</option>
                      <option>Call Center</option>
                    </select>
                </div>
              </div>

                <div class="form-group">
                      <label for="order id" class="col-md-2">Order ID</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control"  id="order id" name="order_id" placeholder="Please Enter order id">
                          <?php echo form_error('booking_primary_contact_no'); ?>
                      </div>
                  </div>

              <div class="form-group <?php if (form_error('service_id')) { echo 'has-error';} ?>">
                <label for="service_name" class="col-md-2">Service Name</label>
                <div class="col-md-6">

                    <select type="text" class="form-control"  id="service_id" name="service_id" value = "<?php echo set_value('service_id'); ?>" onChange="getBrandForService(this.value), getCategoryForService(this.value);"  required>
                      <?php foreach ($services as $key => $values) { ?>

                        <option  value=<?= $values->id; ?>>

                            <?php echo $values->services; }    ?>

                        </option>

                      <?php echo form_error('service_id'); ?>

                    </select>
                </div>
              </div>


              <div class="form-group <?php
                if (form_error('quantity')) { echo 'has-error'; } ?>">
                <label for="quantity" class="col-md-2">Quantity</label>
                <div class="col-md-6">
                  <select class="form-control"  id="quantity" name="quantity" value = "<?php echo set_value('quantity'); ?>"  required>
                      <option>1</option>
                      <option>2</option>
                      <option>3</option>
                      <option>4</option>
                  </select>
                  <?php echo form_error('quantity'); ?>
                </div>
              </div>

               <div class="form-group">
                      <label for="order id" class="col-md-2">Serial Number</label>
                      <div class="col-md-6">
                          <input type="text" class="form-control"  id="serial_number" name="serial_number" placeholder="Please Enter Serial Number">
                         
                      </div>
                  </div>

                    <div style="height:900px;">

                <div style="float:left">
                <!--------------------1-------------------->
                <input style="width:180px;" type="text" class="form-control"  id="newbrand1" name="newbrand1" value = "" placeholder="Enter new brand">
                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_brand" class="col-md-1">Brand</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $brands[0]; ?>" name="<?php echo $brands[0]; ?>" value = "<?php echo set_value($brands[0]); ?>" onChange="newApplianceBrand1(this.value);" required>

                           <option>

                           </option>

                           </select>
                          <?php echo form_error('appliance_brand'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_category" class="col-md-1">Category</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $category[0]; ?>" name="<?php echo $category[0]; ?>" value = "<?php echo set_value($category[0]); ?>" onChange="getCapacityForCategory(service_id,this.value,<?php echo $i;?>);" >

                            <option >

                            </option>
                        </select>
                          <?php echo form_error('appliance_category'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="capacity" class="col-md-1">Capacity</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control" id="appliance_capacity1" name="appliance_capacity1" value = "<?php echo set_value('appliance_capacity1'); ?>" onChange="getPricesForCategoryCapacity(<?php echo $i;?>);" placeholder="Enter Capacity">

                           <option >

                           </option>
                        </select>
                          <?php echo form_error('appliance_capacity'); ?>

                      </div>
                </div>

                <div class="form-group">
                  <div  style="width:300px;" class="col-md-1">

                    <table class="table table-striped table-bordered" name="<?php echo $priceList[0]; ?>" id="<?php echo $priceList[0]; ?>">
                      <tr><th>Service Category</th>
                          <th>Total Charges</th>
                          <th>Selected Services</th>
                      </tr>

                    </table>
                  </div>
                </div>
                </div>

                <!--------------------2-------------------->

                <div style="float:left">
                <input style="width:180px;" type="text" class="form-control"  id="newbrand2" name="newbrand2" value = "" placeholder="Enter new brand">
                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_brand" class="col-md-1">Brand</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $brands[1]; ?>" name="<?php echo $brands[1]; ?>" value = "<?php echo set_value($brands[1]); ?>" onChange="newApplianceBrand2(this.value);" required>

                           <option>

                           </option>

                           </select>
                          <?php echo form_error('appliance_brand'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_category" class="col-md-1">Category</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $category[1]; ?>" name="<?php echo $category[1]; ?>" value = "<?php echo set_value($category[1]); ?>" onChange="getCapacityForCategory(service_id,this.value,<?php echo $j;?>);" >

                            <option >

                            </option>
                        </select>
                          <?php echo form_error('appliance_category'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="capacity" class="col-md-1">Capacity</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control" id="<?php echo $capacity[1]; ?>" name="<?php echo $capacity[1]; ?>" value = "<?php echo set_value('appliance_capacity'); ?>" onChange="getPricesForCategoryCapacity(<?php echo $j;?>);" placeholder="Enter Capacity">

                           <option >

                           </option>
                        </select>

                        <?php echo form_error('appliance_capacity'); ?>
                      </div>
                </div>

                <div class="form-group">
                  <div style="width:300px;" class="col-md-1">

                    <table class="table table-striped table-bordered" name="<?php echo $priceList[1]; ?>" id="<?php echo $priceList[1]; ?>">
                      <tr><th>Service Category</th>
                          <th>Total Charges</th>
                          <th>Selected Services</th>
                      </tr>

                    </table>
                  </div>
                </div>
                </div>

                <!--------------------3-------------------->
                <div style="float:left">
                <input style="width:180px;" type="text" class="form-control"  id="newbrand3" name="newbrand3" value = "" placeholder="Enter new brand">
                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_brand" class="col-md-1">Brand</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $brands[2]; ?>" name="<?php echo $brands[2]; ?>" value = "<?php echo set_value($brands[2]); ?>" onChange="newApplianceBrand3(this.value);" required>

                           <option>

                           </option>

                           </select>
                          <?php echo form_error('appliance_brand'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_category" class="col-md-1">Category</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $category[2]; ?>" name="<?php echo $category[2]; ?>" value = "<?php echo set_value($category[2]); ?>" onChange="getCapacityForCategory(service_id,this.value,<?php echo $k;?>);" >

                            <option >

                            </option>
                        </select>
                          <?php echo form_error('appliance_category'); ?>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_capacity" class="col-md-1">Capacity</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control" id="<?php echo $capacity[2]; ?>" name="<?php echo $capacity[2]; ?>" value = "<?php echo set_value('appliance_capacity'); ?>" onChange="getPricesForCategoryCapacity(<?php echo $k;?>);" placeholder="Enter Capacity">

                           <option >

                           </option>
                        </select>

                        <?php echo form_error('appliance_capacity'); ?>
                      </div>
                </div>

                <div class="form-group">
                  <div style="width:300px;" class="col-md-1">

                    <table class="table table-striped table-bordered" name="<?php echo $priceList[2]; ?>" id="<?php echo $priceList[2]; ?>">
                      <tr><th>Service Category</th>
                          <th>Total Charges</th>
                          <th>Selected Services</th>
                      </tr>

                    </table>
                  </div>
                </div>
                </div>
                <!--------------------4-------------------->
                <div style="float:left">
                <input style="width:180px;" type="text" class="form-control"  id="newbrand4" name="newbrand4" value = "" placeholder="Enter new brand">
                <div class="form-group <?php if( form_error('appliance_brand') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_brand" class="col-md-1">Brand</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $brands[3]; ?>" name="<?php echo $brands[3]; ?>" value = "<?php echo set_value($brands[3]); ?>" onChange="newApplianceBrand4(this.value);" required>

                           <option>

                           </option>

                           </select>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_category') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="appliance_category" class="col-md-1">Category</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control"  id="<?php echo $category[3]; ?>" name="<?php echo $category[3]; ?>" value = "<?php echo set_value($category[3]); ?>" onChange="getCapacityForCategory(service_id,this.value,<?php echo $l;?>);" >

                            <option >

                            </option>
                        </select>
                      </div>
                </div>

                <div class="form-group <?php if( form_error('appliance_capacity') ) { echo 'has-error';} ?>">
                  <label style="width:80px;" for="capacity" class="col-md-1">Capacity</label>
                      <div style="width:180px;" class="col-md-1">
                        <select style="width:180px;" type="text" class="form-control" id="<?php echo $capacity[3]; ?>" name="<?php echo $capacity[3]; ?>" value = "<?php echo set_value('appliance_capacity'); ?>" onChange="getPricesForCategoryCapacity(<?php echo $l;?>);" placeholder="Enter Capacity">

                           <option >

                           </option>
                        </select>

                        <?php echo form_error('appliance_capacity'); ?>
                      </div>
                </div>

                <div class="form-group">
                  <div style="width:300px;" class="col-md-1">

                    <table class="table table-striped table-bordered" name="<?php echo $priceList[3]; ?>" id="<?php echo $priceList[3]; ?>">
                      <tr><th>Service Category</th>
                          <th>Total Charges</th>
                          <th>Selected Services</th>
                      </tr>

                    </table>
                  </div>
                </div>
                </div>

            </div>

                <?php //}?>


              <div style="float:left;width:270px;">
                  <div class="form-group <?php
                    if (form_error('model_number1')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="model_number1" class="col-md-2">Appliance Model</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="model_number1" id="model_number1" value = "<?php echo set_value('model_number1'); ?>" placeholder="Enter Model" >
                            <?php echo form_error('model_number1'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_tags1')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="appliance_tags1" class="col-md-2">Appliance Tag</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="appliance_tags1" id="appliance_tags1" value = "<?php echo set_value('appliance_tags1'); ?>" placeholder="Enter Tag" >
                            <?php echo form_error('appliance_tags1'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('purchase_year1')) {echo 'has-error'; } ?>">
                    <label style="width:100px;" for="purchase_year1" class="col-md-2">Purchase Year</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="purchase_year1" id="purchase_year1" value = "<?php echo set_value('purchase_year1'); ?>" placeholder="Enter Yr of Purchase" >
                            <?php echo form_error('purchase_year1'); ?>
                      </div>
                    </div>
              </div>

              <div style="float:left;width:270px;">
                  <div class="form-group <?php
                    if (form_error('model_number2')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="model_number2" class="col-md-2">Appliance Model</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="model_number2" id="model_number2" value = "<?php echo set_value('model_number2'); ?>" placeholder="Enter Model" >
                            <?php echo form_error('model_number2'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_tags2')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="appliance_tags2" class="col-md-2">Appliance Tag</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="appliance_tags2" id="appliance_tags2" value = "<?php echo set_value('appliance_tags2'); ?>" placeholder="Enter Tag" >
                            <?php echo form_error('appliance_tags2'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('purchase_year2')) {echo 'has-error'; } ?>">
                    <label style="width:100px;" for="purchase_year2" class="col-md-2">Purchase Year</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="purchase_year2" id="purchase_year2" value = "<?php echo set_value('purchase_year2'); ?>" placeholder="Enter Yr of Purchase" >
                            <?php echo form_error('purchase_year2'); ?>
                      </div>
                    </div>
              </div>

              <div style="float:left;width:270px;">
                  <div class="form-group <?php
                    if (form_error('model_number3')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="model_number3" class="col-md-2">Appliance Model</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="model_number3" id="model_number3" value = "<?php echo set_value('model_number3'); ?>" placeholder="Enter Model" >
                            <?php echo form_error('model_number3'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_tags3')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="appliance_tags3" class="col-md-2">Appliance Tag</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="appliance_tags3" id="appliance_tags3" value = "<?php echo set_value('appliance_tags3'); ?>" placeholder="Enter Tag" >
                            <?php echo form_error('appliance_tags3'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('purchase_year3')) {echo 'has-error'; } ?>">
                    <label style="width:100px;" for="purchase_year3" class="col-md-2">Purchase Year</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="purchase_year3" id="purchase_year3" value = "<?php echo set_value('purchase_year3'); ?>" placeholder="Enter Yr of Purchase" >
                            <?php echo form_error('purchase_year3'); ?>
                      </div>
                    </div>
              </div>

              <div style="float:left;width:270px;">
                  <div class="form-group <?php
                    if (form_error('model_number4')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="model_number4" class="col-md-2">Appliance Model</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="model_number4" id="model_number4" value = "<?php echo set_value('model_number4'); ?>" placeholder="Enter Model" >
                            <?php echo form_error('model_number4'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('appliance_tags4')) { echo 'has-error'; } ?>">
                      <label style="width:100px;" for="appliance_tags4" class="col-md-2">Appliance Tag</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="appliance_tags4" id="appliance_tags4" value = "<?php echo set_value('appliance_tags4'); ?>" placeholder="Enter Tag" >
                            <?php echo form_error('appliance_tags4'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('purchase_year4')) {echo 'has-error'; } ?>">
                    <label style="width:100px;" for="purchase_year4" class="col-md-2">Purchase Year</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="purchase_year4" id="purchase_year4" value = "<?php echo set_value('purchase_year4'); ?>" placeholder="Enter Yr of Purchase" >
                            <?php echo form_error('purchase_year4'); ?>
                      </div>
                    </div>
              </div>


              <div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('items_selected1')) { echo 'has-error'; } ?>">
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected1" id="items_selected1" value = "<?php echo set_value('items_selected1'); ?>" placeholder="Enter Selected Services" >
                            <?php echo form_error('items_selected1'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('total_price1')) {echo 'has-error'; } ?>">
                    <label id="total_price1_label" style="width:100px;" for="total_price" class="col-md-2">Total Price</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="total_price1" id="total_price1" value = "<?php echo set_value('total_price1'); ?>" placeholder="Enter Total Price" >
                            <?php echo form_error('total_price1'); ?>
                      </div>
                    </div>
                    </div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('items_selected2')) { echo 'has-error'; } ?>">
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected2" id="items_selected2" value = "<?php echo set_value('items_selected2'); ?>" placeholder="Enter Selected Services" >
                            <?php echo form_error('items_selected2'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('total_price2')) {echo 'has-error'; } ?>">
                    <label id="total_price2_label" style="width:100px;" for="total_price" class="col-md-2">Total Price</label>
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="total_price2" id="total_price2" value = "<?php echo set_value('total_price2'); ?>" placeholder="Enter Total Price" >
                            <?php echo form_error('total_price2'); ?>
                      </div>
                    </div>
                    </div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('items_selected3')) { echo 'has-error'; } ?>">
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected3" id="items_selected3" value = "<?php echo set_value('items_selected3'); ?>" placeholder="Enter Selected Services" >
                            <?php echo form_error('items_selected3'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('total_price3')) {echo 'has-error'; } ?>">
                    <label id="total_price3_label" style="width:100px;" for="total_price3" class="col-md-2">Total Price</label>
                      <div style="width:100px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="total_price3" id="total_price3" value = "<?php echo set_value('total_price3'); ?>" placeholder="Enter Total Price" >
                            <?php echo form_error('total_price3'); ?>
                      </div>
                    </div>
                    </div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('items_selected4')) { echo 'has-error'; } ?>">
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="hidden" class="form-control"  name="items_selected4" id="items_selected4" value = "<?php echo set_value('items_selected4'); ?>" placeholder="Enter Selected Services" >
                            <?php echo form_error('items_selected4'); ?>
                      </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('total_price4')) {echo 'has-error'; } ?>">
                    <label id="total_price4_label" style="width:100px;" for="total_price4" class="col-md-2">Total Price</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="total_price4" id="total_price4" value = "<?php echo set_value('total_price4'); ?>" placeholder="Enter Total Price" >
                            <?php echo form_error('total_price4'); ?>
                      </div>
                    </div>
                    </div>


                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('potential_value1')) {echo 'has-error'; } ?>">
                    <label id="potential_value1_label" style="width:100px;" for="total_price" class="col-md-2">Potential Value</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="potential_value1" id="potential_value1" value = "<?php echo set_value('potential_value1'); ?>" placeholder="Enter Potential Value" >
                            <?php echo form_error('potential_value1'); ?>
                      </div>
                    </div>
                    </div>
                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('potential_value2')) {echo 'has-error'; } ?>">
                    <label id="potential_value2_label" style="width:100px;" for="total_price" class="col-md-2">Potential Value</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="potential_value2" id="potential_value2" value = "<?php echo set_value('potential_value2'); ?>" placeholder="Enter Potential Value" >
                            <?php echo form_error('potential_value2'); ?>
                      </div>
                    </div>
                    </div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('potential_value3')) {echo 'has-error'; } ?>">
                    <label id="potential_value3_label" style="width:100px;" for="potential_value3" class="col-md-2">Potential Value</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="potential_value3" id="potential_value3" value = "<?php echo set_value('potential_value3'); ?>" placeholder="Enter Potential Value" >
                            <?php echo form_error('potential_value3'); ?>
                      </div>
                    </div>
                    </div>

                    <div style="float:left;width:270px;">
                    <div class="form-group <?php
                    if (form_error('potential_value4')) {echo 'has-error'; } ?>">
                    <label id="potential_value4_label" style="width:100px;" for="potential_value4" class="col-md-2">Potential Value</label>
                      <div style="width:150px;" class="col-md-6">
                        <input style="width:150px;" type="text" class="form-control"  name="potential_value4" id="potential_value4" value = "<?php echo set_value('potential_value4'); ?>" placeholder="Enter Potential Value" >
                            <?php echo form_error('potential_value4'); ?>
                      </div>
                    </div>


                    </div>
                    

                  </div>

                    <div class="form-group <?php
                    if (form_error('booking_date')) {
                        echo 'has-error';
                    } ?>">
                        <label id="booking_date1" for="booking_date" class="col-md-2">Booking Date</label>
                        <label id="query_date1" for="booking_date" class="col-md-2">Query Date</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control"  id="booking_date" name="booking_date" value = "<?php echo set_value('booking_date'); ?>" required>
                            <?php echo form_error('booking_date'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_timeslot')) {
                        echo 'has-error';
                    } ?>">
                        <label id="booking_timeslot1" for="booking_timeslot" class="col-md-2">Booking Time</label>
                        <label id="query_timeslot1" for="booking_timeslot" class="col-md-2">Query Time</label>
                        <div class="col-md-6">
                            <select class="form-control" id="booking_timeslot" name="booking_timeslot" value = "<?php echo set_value('booking_timeslot'); ?>"  required>

                                <option>10AM-1PM</option>
                                <option>1PM-4PM</option>
                                <option>4PM-7PM</option>
                            </select>
                            <?php echo form_error('booking_timeslot'); ?>
                        </div>
                    </div>

                    <div class="form-group <?php
                    if (form_error('booking_address')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="booking_address" for="booking_address" class="col-md-2">Booking Address</label>
                        <label id="query_address" for="booking_address" class="col-md-2">Address</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_address" value = "<?php echo $home_address; ?>">
                            <?php echo form_error('booking_address'); ?>
                        </div>
                    </div>

                     <div class="form-group <?php
                    if (form_error('booking_city')) {
                        echo 'has-error';
                    }
                    ?>">
                        <label id="city" for="booking_city" class="col-md-2">Booking City</label>
                       
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_city" value = "<?php echo $city; ?>">
                            <?php echo form_error('booking_city'); ?>
                        </div>
                    </div>

                     <div class="form-group <?php if( form_error('state') ) { echo 'has-error';} ?>">
                  <label for="home_address" class="col-md-2">Booking State</label>
                      <div class="col-md-6">
                        <input type="text" class="form-control"  name="booking_state" value = "<?php echo $state; ?>" placeholder="Enter  State.">
                        <?php echo form_error('state'); ?>
                      </div>  
                 </div>


                    <div class="form-group <?php
                    if (form_error('booking_pincode')) {
                        echo 'has-error';
                    } ?>">
                        <label id="booking_pincode" for="booking_pincode" class="col-md-2">Booking Pincode</label>
                        <label id="query_pincode" for="booking_address" class="col-md-2">Pincode</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="booking_pincode" value = "<?php if(isset($pincode)){echo $pincode;} ?>" placeholder="Enter Area Pin" required>
                            <?php echo form_error('booking_pincode'); ?>
                        </div>
                    </div>

                    <div id="booking_remarks" class="form-group <?php
                    if (form_error('booking_remarks')) {
                        echo 'has-error';
                    } ?>">
                        <label for="booking_remarks" class="col-md-2">Booking Remark</label>
                        <div class="col-md-6">
                            <textarea class="form-control"  name="booking_remarks" value = "<?php echo set_value('booking_remarks'); ?>"></textarea>
                            <?php echo form_error('booking_remarks'); ?>
                        </div>
                    </div>

                    <div id="query_remarks" class="form-group <?php
                    if (form_error('query_remark')) {
                        echo 'has-error';
                    } ?>">
                        <label for="query_remarks" class="col-md-2">Query Remark</label>
                        <div class="col-md-6">
                            <textarea class="form-control"  name="query_remarks" value = "<?php echo set_value('query_remarks'); ?>"></textarea>
                            <?php echo form_error('query_remarks'); ?>
                        </div>
                    </div>

                  <div class="form-group <?php
                  if (form_error('amount_due')) {
                      echo 'has-error';
                  }
                  ?>">

                      <div class="col-md-6">
                          <input type="hidden" class="form-control" id="amount_due" name="amount_due" value = "<?php echo set_value('total_price'); ?>" placeholder="Total Amount Due" >
<?php echo form_error('amount_due'); ?>
                      </div>
                  </div>

                  <div class="form-group <?php
if (form_error('user_email')) {
                        echo 'has-error';
                    } ?>">
                        <div class="col-md-6">
                            <input type="hidden" class="form-control"  name="user_email" value = "<?php echo $user_email; ?>">
                            <?php echo form_error('user_email'); ?>
                        </div>
                    </div>

                  <div class="form-group <?php
                       if (form_error('user_id')) {
                        echo 'has-error';
                    } ?>">
                        <div class="col-md-6">
                            <input type="hidden" class="form-control"  name="user_id" value = "<?php echo $user_id; ?>">
                            <?php echo form_error('user_id'); ?>
                        </div>
                  </div>

                  <div class="form-group">
                        <div class="col-md-10">
                            <center><input type= "submit" name="submit" class="btn btn-danger btn-lg" value ="Save" onclick="return(validate())" style="width:10%">
                            <?php echo "<a id='edit' class='btn btn-lg btn-primary' href=".base_url()."employee/user/user_details/$phone_number>Cancel</a>";?>
                            </center>
                        </div>
                    </div>

              </form>
            </div>
        </div>
    </div>
</div>