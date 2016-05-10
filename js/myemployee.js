

 function activate(id) {
          
            $.ajax({ 
           type: 'POST', 
           url: '<?php echo base_url();?>employee/handyman/deactivate/'+id, 
           success: function(result){
           var name = document.getElementById("name_"+id).innerText;
           	var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';

             displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span>'+
                '</button>'+
                '<strong>'+name+'  Dectivate successfully.</strong>'+
                '</div>';
            var inactivebutton = '<button class="btn btn-small btn-primary btn-sm" onclick="deactivate('+id+')">Activate</button></td>';
            document.getElementById("status_"+id).innerHTML= "Inactive";
            document.getElementById("statusbutton_"+id).innerHTML= inactivebutton;
            document.getElementById("msgdisplay").innerHTML= displaymsg;

           } 
         });
}


        
  
function deactivate(id){
	        var name = document.getElementById("name_"+id).innerText;
        	var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';

             displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span>'+
                '</button>'+
                '<strong>'+name+'  Activate successfully.</strong>'+
                '</div>';    
         
         $.ajax({ 
           type: 'POST', 
           url: '<?php echo base_url();?>employee/handyman/activatehandyman/'+id, 
           success: function(){
            var activatebutton = '<button class="btn btn-small btn-info btn-sm" onclick="activate('+id+')">Dectivate</button>';  
            document.getElementById("status_"+id).innerHTML= "Active";
            document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
            document.getElementById("msgdisplay").innerHTML= displaymsg;
           } 
         });
         
}

function approvefilter(id){
  
         $.ajax({ 
           type: 'POST', 
           url: '<?php echo base_url();?>employee/handyman/approve/'+id, 
           success: function(){
           	var name = document.getElementById("name_"+id).innerText;
             var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';

             displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span>'+
                '</button>'+
                '<strong>'+name+'  Approve successfully.</strong>'+
                '</div>'; 
             var activatebutton = '<button class="btn btn-small btn-info btn-sm" onclick="activate('+id+')">Dectivate</button>';  
            document.getElementById("status_"+id).innerHTML= "Active";
            document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
            document.getElementById("msgdisplay").innerHTML= displaymsg;
           } 
         });
}

function verify(id){
	     var name = document.getElementById("name_"+id).innerText;
         var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';

             displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span>'+
                '</button>'+
                '<strong>'+name+'  Verified successfully.</strong>'+
                '</div>';  
         $.ajax({ 
           type: 'POST', 
           url: '<?php echo base_url();?>employee/handyman/verify/'+id, 
           success: function(){
             var activatebutton = ' <button class="btn btn-small btn-danger btn-sm" onclick="approvefilter('+id+')">Approve</button>';
             document.getElementById("status_"+id).innerHTML= "Verified";
             document.getElementById("statusbutton_"+id).innerHTML= activatebutton;
             document.getElementById("msgdisplay").innerHTML= displaymsg;
           } 
         });
         
}

function deletehandyman(id){
             if (confirm("Are You Sure!") == true) {
             	var name = document.getElementById("name_"+id).innerText;
             	var displaymsg = '<div class="alert alert-success alert-dismissible" role="alert">';

             displaymsg += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                '<span aria-hidden="true">&times;</span>'+
                '</button>'+
                '<strong>'+name+'  Delete successfully.</strong>'+
                '</div>';  
         
         $.ajax({ 
           type: 'POST', 
           url: '<?php echo base_url();?>employee/handyman/delete/'+id, 
           success: function(){
            $('#table_'+id).css('display', 'none');
            document.getElementById("msgdisplay").innerHTML= displaymsg;
             
           } 
         });
         
         }
}
         

