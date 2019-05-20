<div id="page-wrapper"> 
  <div class="container-fluid">
    <div class="row">
    	<h1 class="page-header"  >
      	Add New Brands 
    	</h1>
    	<form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/booking/process_add_new_brand_form" >
    	  <table class="table table-striped table-bordered" style="width:500px;border: 2px solid #edeaea;">
    	  	<tr>
    	  	  <th>S. No.</th>
    	  	  <th>Appliance</th>
    	  	  <th>Brand</th>
    	  	</tr>
    	  	<?php $count = 1; ?>
            <?php for($i=1;$i<=10;$i++){?>
        	  <tr>
        		<td><?php echo $count++;?>.</td>
        		<td width="200px;">
        			<select type="text" class="form-control"  id="<?php echo 'service_'.$i ?>" name="new_brand[]" 
        				value="<?php echo set_value('new_brand'); ?>" onchange="get_hint(<?php echo $i ?>)">
                                    <option selected disabled>Select</option>
        				<?php foreach($services as $key => $values) {?>
        			  	<option  value=<?=$values->id;?>>
        				<?php echo $values->services; }?>
        			  	</option>
        			</select>
        		</td>
        		<td width="200px;">
                            <input type="text" class="form-control" disabled="" name="brand_name[]" id="<?php echo 'brands_'.$i ?>" value="<?php echo set_value('brand_name'); ?>" onblur="remove_hint()" onkeyup="show_hint(<?php echo $i ?>)">
                            <div id="<?php echo 'show_hInt_'.$i ?>" style="width: 174px;position: absolute;border: 1px solid;background: #e3ffe1;padding: 3px; display: none;" class="show_hint"></div>
        		</td>
        	  </tr>
        	<?php }?>
    	  </table>
    	   
          	<div><input type="Submit" value="Save" class="btn btn-primary btn-lg">
          	<input type="Reset" value="Cancel" class="btn btn-danger btn-lg"></div>
           
    	</form>
    </div>
  </div>
</div>
<script>
 function sendAjaxRequest(postData, url,type) {
        return $.ajax({
            data: postData,
            url: url,
            type: type
        });
    }
    var allBrands;
    var brand_value;
    var hintArray = [];
    function getBrandHintArray(){
        for(var i=0;i<window.allBrands.length;i++){
              var allBrandValue = window.allBrands[i].toLowerCase();
              var currentBrandValue = window.brand_value.toLowerCase();
              var index = allBrandValue.indexOf(currentBrandValue);
            if(index === 0){
                window.hintArray.push(window.allBrands[i]);         
            }
        }
    }
    function createHintsList(row_id){
        var hintString = '';
        for(var i=0;i< window.hintArray.length;i++){
            var hintString = hintString+ window.hintArray[i];
            var hintString = hintString+"<br>";
        }
        if(hintString){
            document.getElementById("show_hInt_"+row_id).style.display = 'block';
            document.getElementById("show_hInt_"+row_id).innerHTML = hintString;
        }
        else{
            document.getElementById("show_hInt_"+row_id).style.display = 'none';
        }
    }
    function get_hint(row_id){
            document.getElementById("brands_"+row_id).disabled = false;
            serviceID = $("#service_"+row_id).val();
            document.getElementById("brands_"+row_id).value = "";
            var data = {};
                    url =  '<?php echo base_url(); ?>employee/booking/get_all_brands/'+serviceID;
                    post_request = "GET";
                     sendAjaxRequest(data,url,post_request).done(function(response){
                          window.allBrands = JSON.parse(response);
                        });
        }
        function show_hint(row_id) {
              window.hintArray = [];
              window.brand_value = $("#brands_"+row_id).val();
              getBrandHintArray();
              createHintsList(row_id);
        }
        function remove_hint(){
            var elements = document.getElementsByClassName('show_hint');
            for(var t=0; t<elements.length; t++) { 
              elements[t].style.display='none';
            }
        }
        
    </script>