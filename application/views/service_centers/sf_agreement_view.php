<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="<?php echo base_url('js/jquery.js'); ?>"></script>
        <script src="src="https://code.jquery.com/ui/1.11.4/jquery-ui.js""></script>
<!--        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <style>
            .removedisplay{
               display: none !important;
            }
        </style>
    </head>
    <body>
        
        <!-- Button trigger modal -->
        <button type="button" id="example" style="display:none;" class="btn btn-primary modal-lg" data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false">
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="margin-top: -80px;width: 90%;max-height: 450px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Service Franchise Agreement </h3>
                        <p id="a_success" style="color:green;" class="removedisplay"><?php echo SUCESS_MESSAGE_SF_AGREEMENT;?></p>
                        <p id="a_failure" style="color:red;" class="removedisplay"><?php echo FAILURE_MESSAGE_SF_AGREEMENT;?></p>
                    </div>
                    <div class="modal-body" style="max-height: 450px;overflow-y: scroll;overflow-x: hidden;">
                        <?php echo $template['template']; ?>
                    </div>
                    <div class="modal-footer">
                        <div class="checkbox">
                            <input type="checkbox" id="chk-agree" > I read and understand terms & conditions
                        </div>
                        
                        <?php if($skip_btn){ ?>
                        <button type="button" id="btn-skip" class="btn btn-secondary" data-dismiss="modal">Skip</button>
                        <?php } ?>
                        <button type="button" id="btn-close" style="float:right" class="btn btn-secondary removedisplay" data-dismiss="modal">Close</button>
                        <button type="button" id="btn-agree" class="btn btn-success" disabled>I agree</button>
                        
                        <input type="hidden" id="sf_id" value="<?php echo $sf_id; ?>"/>
                        
                    </div>
                </div>
            </div>
        </div>

        <script>
            $('document').ready(function () {

                $('#example').trigger('click');
                $('#chk-agree').change(function(){
                    if($('#chk-agree').is(':checked')){
                        $('#btn-agree').prop("disabled", false);
                    }else{
                        $('#btn-agree').prop("disabled", true);
                    }
                });
                
                $('#btn-agree').click(function(){                    
                    var sf_id = $('#sf_id').val();
                    $('#btn-agree').prop("disabled", true);
                    $('#chk-agree').prop("disabled", true);
                    $('#btn-agree').html('processing....');
                    //$('#a_success').removeCss('display', 'block');
                                
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>'+'employee/Sf_agreement/process_sf_agreement_request',
                        data:{sf_id:sf_id},
                        success: function(response){
                            response = JSON.parse(response);
                            console.log(response);
                            if(response.success){
                                $('#a_success').removeClass('removedisplay');
                                $('#btn-close').removeClass('removedisplay');
                                $('#btn-agree').addClass('removedisplay');
                                $('#btn-skip').addClass('removedisplay');
                                //window.location.href = '<?php// echo base_url(); ?>'+'service_center/dashboard';
                            } else {
                                $('#a_success').addClass('removedisplay');
                                $('#a_faliure').removeClass('removedisplay');
                            }
                        }
                            
                    });
                });
                $('#btn-skip').click(function(){
                    var sf_id = $('#sf_id').val();
                    var host = location.host;
                    var expire_date = new Date();
                    var midnight = new Date(expire_date.getFullYear(), expire_date.getMonth(), expire_date.getDate(), 23, 59, 59);
                    document.cookie = 'sf_skip_'+sf_id+'=1; sf_id='+ sf_id +'; expires='+midnight.toGMTString()+'; path=/';
                    //document.cookie = 'sf_id='+ sf_id +'; expires='+midnight.toGMTString()+'; path=/';
                    
                    window.location.href = '<?php echo base_url(); ?>'+'service_center/dashboard';
                });

            });
//            
//            $('#exampleModal').modal({
//                backdrop: 'static',
//                keyboard: false
//            });
        </script>
    </body>
</html>