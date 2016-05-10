<div id="page-wrapper">
<div class="container-fluid">
   <div class="row">
      <div class="col-lg-12">
         <?php if(isset($success) && $success !==0) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $success . '</strong>
            </div>';
            }
            ?>
         <!-- <h3><?php if(isset($countarray)){echo $countarray;}?></h3>-->
      </div>
      <div class="col-lg-12" style="margin-top:23px;">
         <form class="form-horizontal"  action="<?php echo base_url()?>service/pricing" method="POST" id="myform" >
            <div class="form-group <?php if( form_error('handyman_id') ) { echo 'has-error';} ?>" >
               <label for="handyman name" class="col-md-2">Handyman Name </label>
               <div class="col-md-3">
                  <select  name="handyman_id" class="js-example-basic-multiple form-control" >
                     <?php foreach ($handyman as $value) { ?>
                     <option <?php if(set_value('handyman_id') == $value['id']) { echo "selected";}?> value="<?php echo $value['id']?>"><?php echo $value['name']; ?></option>
                     <?php }?>
                  </select>
               </div>
               <?php echo form_error('handyman_id'); ?>
            </div>
            <div class="repeatingSection form-group <?php if( form_error('service[]') ) { echo 'has-error';} ?> <?php if( form_error('price[]') ) { echo 'has-error';} ?> ">
               <input type="hidden" name="fighter_a_id_1" id="fighter_a_id_1" value="" />
               <input type="hidden" name="fighter_b_id_1" id="fighter_b_id_1" value="" />
               <label for="service" class="col-md-2">Search</label>
               <div class="col-md-3">
                  <input type="text" class="form-control" id="fighter_a_id_1" name="service[]"   required>
                  <?php echo form_error('service[]'); ?>
               </div>
               <div class="col-md-3">
                  <input type="text" class="form-control" id="fighter_b_id_1" name="price[]" placeholder="Price"  required>
                  <?php echo form_error('price[]'); ?>
               </div>
               <div class="col-md-3  deleteFight">
                  <button class="btn btn-small btn-info btn-sm">Delete</button>
               </div>
            </div>
            <div class="formRow formRowRepeatingSection text-center">
               <button class="addFight btn btn-small btn-primary btn-sm">Add </button>  <input type="submit" class="btn btn-small btn-default btn-sm" value="Save " />
            </div>
         </form>
      </div>
   </div>
</div>
<script>
   // Add a new repeating section
   var attrs = ['for', 'id', 'name'];
   function resetAttributeNames(section) { 
    var tags = section.find('input, label'), idx = section.index();
    tags.each(function() {
      var $this = $(this);
      $.each(attrs, function(i, attr) {
        var attr_val = $this.attr(attr);
        if (attr_val) {
            $this.attr(attr, attr_val.replace(/_\d+$/, '_'+(idx + 1)))
        }
      })
    })
   }
                   
   $('.addFight').click(function(e){
        e.preventDefault();
        var lastRepeatingGroup = $('.repeatingSection').last();
        var cloned = lastRepeatingGroup.clone(true)  
        cloned.insertAfter(lastRepeatingGroup);
        resetAttributeNames(cloned)
    });
                    
   // Delete a repeating section
   $('.deleteFight').click(function(e){
        e.preventDefault();
        var current_fight = $(this).parent('div');
        var other_fights = current_fight.siblings('.repeatingSection');
        if (other_fights.length === 0) {
            alert("You should atleast have one fight");
            return;
        }
        current_fight.slideUp('slow', function() {
            current_fight.remove();
            
            // reset fight indexes
            other_fights.each(function() {
               resetAttributeNames($(this)); 
            })  
            
        })
        
            
    });
   $(".js-example-basic-multiple").select2();
   
   
   
</script>
<style>
   form {
   font-family: Helvetica;
   color: #555;
   }
   .repeatingSection { 
   padding: 20px;
   margin: 20px;
   border-bottom: 1px solid #ddd  
   }
   .addFight{
   margin-left: -154px;
   margin-right: 36px;
   }
   form > .formRow {
   padding: 20px;
   background: #ddd; 
   margin: 20px 0 0 0;    
   }
</style>