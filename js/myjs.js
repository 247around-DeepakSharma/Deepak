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
                    
    $('.addprice').click(function(e){
         e.preventDefault();
         var lastRepeatingGroup = $('.repeatingSection').last();
         var cloned = lastRepeatingGroup.clone(true)  
         cloned.insertAfter(lastRepeatingGroup);
         resetAttributeNames(cloned)
     });
                     
    // Delete a repeating section
    $('.deleteprice').click(function(e){
         e.preventDefault();
         var current_fight = $(this).parent('div');
         var other_fights = current_fight.siblings('.repeatingSection');
         if (other_fights.length === 0) {
             alert("You should add atleast one Price");
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


      
