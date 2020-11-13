<style>
    #chat_table_filter{
        text-align: right;
    }
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
 
    .pull-right{
        padding: 0 0 0 19px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Whatsapp Chat History 

                    </h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="chat_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>Channel</th>
                                    <th>Direction</th>
                                    <th>Content</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <div class="row chat-window col-xs-6 col-md-3" id="chat_window_1" style="margin-left:10px;z-index:9999;right: 10px;">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading top-bar">
                        <div class="col-md-8 col-xs-8">
                            <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span> Chat - <span id="chat_number"></span></h3>
                        </div>
                        <div class="col-md-4 col-xs-4" style="text-align: right;">
                            <a href="#"><span id="minim_chat_window" class="glyphicon glyphicon-minus icon_minim"></span></a>
                            <a href="#"><span class="glyphicon hide glyphicon-remove icon_close" data-id="chat_window_1"></span></a>
                        </div>
                    </div>
                    <div class="panel-body msg_container_base">

                        <!---  MSG START -->

                        <!---  MSG END -->


                    </div>
                    <!--<div class="panel-footer">
                        <div class="input-group">
                            <input id="btn-inputchat" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." />
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" id="btn-chat">Send</button>
                            </span>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>


        <div class="btn-group dropup">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-cog"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#" id="new_chat"><span class="glyphicon glyphicon-plus"></span> Admin</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-list"></span> List All</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-remove"></span> Close</a></li>
                <li class="divider"></li>
                <li><a href="#"><span class="glyphicon glyphicon-eye-close"></span>Hide</a></li>
            </ul>
        </div>

    </div>

    <style>

        .col-md-2, .col-md-10{
            padding:0;
        }
        .panel{
            margin-bottom: 0px;
        }
        .chat-window{
            bottom:0;
            position:fixed;
            float:left;
            margin-left:10px;
        }
        .chat-window > div > .panel{
            border-radius: 5px 5px 0 0;
        }
        .icon_minim{
            padding:2px 10px;
        }
        .msg_container_base{
            background: #e5e5e5;
            margin: 0;
            padding: 0 10px 10px;
            max-height:600px;
            overflow-x:hidden;
        }
        .top-bar {
            background: #666;
            color: white;
            padding: 10px;
            position: relative;
            overflow: hidden;
        }
        .msg_receive{
            padding-left:0;
            margin-left:0;
        }
        .msg_sent{
            padding-bottom:20px !important;
            margin-right:0;
        }
        .messages {
            background: white;
            padding: 10px;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            max-width:100%;
        }
        .messages > p {
            font-size: 13px;
            margin: 0 0 0.2rem 0;
        }
        .messages > time {
            font-size: 11px;
            color: #ccc;
        }
        .msg_container {
            padding: 10px;
            overflow: hidden;
            display: flex;
        }
        img {
            display: block;
            width: 100%;
        }
        .avatar {
            position: relative;
        }
        .base_receive > .avatar:after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border: 5px solid #FFF;
            border-left-color: rgba(0, 0, 0, 0);
            border-bottom-color: rgba(0, 0, 0, 0);
        }

        .base_sent {
            justify-content: flex-end;
            align-items: flex-end;
        }
        .base_sent > .avatar:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 0;
            border: 5px solid white;
            border-right-color: transparent;
            border-top-color: transparent;
            box-shadow: 1px 1px 2px rgba(black, 0.2); 
        }

        .msg_sent > time{
            float: right;
        }



        .msg_container_base::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }

        .msg_container_base::-webkit-scrollbar
        {
            width: 12px;
            background-color: #F5F5F5;
        }

        .msg_container_base::-webkit-scrollbar-thumb
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #555;
        }

        .btn-group.dropup{
            position:fixed;
            left:0px;
            bottom:0;
        }

        #chat_window_1{
            width:21% !important;
        }

    </style>



    <script>


        //  function get_inventory_list(){
        chat_table = $('#chat_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    },
                    title: 'whatsapp_numbers',
                    action: newExportAction
                },
            ],
            "language": {
                "processing": "<div class='spinner'>\n\
                                        <div class='rect1' style='background-color:#db3236'></div>\n\
                                        <div class='rect2' style='background-color:#4885ed'></div>\n\
                                        <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                        <div class='rect4' style='background-color:#3cba54'></div>\n\
                                    </div>",
                "emptyTable": "No Data Found"
            },

            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": false,
            "ajax": {
                url: "<?php echo base_url(); ?>employee/whatsapp/get_whatsapp_log",
                type: "POST",
                data: function (d) {

                }
            }
        });
        //   }



        var oldExportAction = function (self, e, chat_table, button, config) {
            if (button[0].className.indexOf('buttons-excel') >= 0) {
                if ($.fn.dataTable.ext.buttons.excelHtml5.available(chat_table, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, chat_table, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, chat_table, button, config);
                }
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, chat_table, button, config);
            }
        };

        var newExportAction = function (e, chat_table, button, config) {
            var self = this;
            var oldStart = chat_table.settings()[0]._iDisplayStart;

            chat_table.one('preXhr', function (e, s, data) {
                // Just this once, load all data from the server...
                data.start = 0;
                data.length = chat_table.page.info().recordsTotal;

                chat_table.one('preDraw', function (e, settings) {
                    // Call the original action function 
                    oldExportAction(self, e, chat_table, button, config);

                    chat_table.one('preXhr', function (e, s, data) {
                        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                        // Set the property to what it was before exporting.
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });

                    // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                    setTimeout(chat_table.ajax.reload, 0);

                    // Prevent rendering of the full data to the DOM
                    return false;
                });
            });

            // Requery the server with the new one-time export settings
          
        };



        //get_inventory_list();



        $(document).on('click', '.panel-heading span.icon_minim', function (e) {
            var $this = $(this);
            if (!$this.hasClass('panel-collapsed')) {
                $this.parents('.panel').find('.panel-body').slideUp();
                $this.addClass('panel-collapsed');
                $this.removeClass('glyphicon-minus').addClass('glyphicon-plus');
            } else {
                $this.parents('.panel').find('.panel-body').slideDown();
                $this.removeClass('panel-collapsed');
                $this.removeClass('glyphicon-plus').addClass('glyphicon-minus');
            }
        });
        $(document).on('focus', '.panel-footer input.chat_input', function (e) {
            var $this = $(this);
            if ($('#minim_chat_window').hasClass('panel-collapsed')) {
                $this.parents('.panel').find('.panel-body').slideDown();
                $('#minim_chat_window').removeClass('panel-collapsed');
                $('#minim_chat_window').removeClass('glyphicon-plus').addClass('glyphicon-minus');
            }
        });
        $(document).on('click', '#new_chat', function (e) {
            var size = $(".chat-window:last-child").css("margin-left");
            size_total = parseInt(size) + 400;
            alert(size_total);
            var clone = $("#chat_window_1").clone().appendTo(".container");
            clone.css("margin-left", size_total);
        });
        $(document).on('click', '.icon_close', function (e) {
            //$(this).parent().parent().parent().parent().remove();
            $("#chat_window_1").remove();
        });


        $(document).on('click', '.chat_number', function (e) {
            //$(this).parent().parent().parent().parent().remove();
            var number = $(this).attr("data-number");
            $("#chat_number").text(number);
            var dataid = $(this).attr('data-id');
            $("#btn-chat").attr("data-id", dataid);


            $.ajax({
                url: '<?php echo base_url(); ?>employee/whatsapp/getChatByNumber/',
                type: 'GET',
                data: 'number=' + number,
                success: function (data) {
                    //called when successful
                    // console.log(data);
                    var result = JSON.parse(data);
                    //  console.log(result.result);
                    var chat = result.result;
                    var html = "";
                    $.each(chat, function (key, value) {
                        console.log(value);

                        if (value.direction == 'outbound') {


                            html = html + '<div class="row msg_container base_sent">' +
                                    '<div class="col-md-10 col-xs-10">' +
                                    '<div class="messages msg_sent">';

                            if (value.content == "content_type") {
                                html = html + '<p>' + value.content + '</p>';
                            } else {
                                html = html + '<p>' + value.content + '</p>';
                            }

                            html = html + '<time style="color:#ed0808;font-weight:900;" datetime="' + value.created_on + '">' + value.created_on + '</time>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="col-md-2 col-xs-2 avatar">' +
                                    '<img src="<?php echo base_url(); ?>images/logo.png" class=" img-responsive ">' +
                                    '</div>' +
                                    '</div>'

                        } else {

                            html = html + '<div class="row msg_container base_receive">' +
                                    '<div class="col-md-2 col-xs-2 avatar">' +
                                    '<img src="<?php echo base_url(); ?>images/dummy.jpg" class=" img-responsive ">' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-10">' +
                                    '<div class="messages msg_receive">';


                            if (value.content == "content_type") {
                                html = html + '<p>' + value.content + '</p>';
                            } else {
                                html = html + '<p>' + value.content + '</p>';
                            }


                            html = html + '<time style="color:#ed0808;font-weight:900;" datetime="' + value.created_on + '">' + value.created_on + '</time>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>'
                        }

                    });

    //console.log(html);
                    $(".msg_container_base").html(html);
                    //  $('#ajaxphp-results').html(data);
                },
                error: function (e) {
                    //called when there is an error
                    alert("Error in loading chat. please try after some time");
                }
            });
            
            //if($('#msg_container_base').isVisible()){
            if(document.getElementsByClassName("msg_container_base").style.display !== "none"){
            alert(1);
            }else{
             alert(2);
            $('.panel-heading span.icon_minim').click();
            }
        });

        $('.panel-heading span.icon_minim').click();


        $(document).on('click', '#btn-chat', function (e) {
            //$(this).parent().parent().parent().parent().remove();
            var chat_message = $("#btn-inputchat").val();
            var chat_number = $("#chat_number").text();
            var sid = $(this).attr("data-id");

            $.ajax({
                url: '<?php echo base_url(); ?>employee/whatsapp/send_whatsapp_to_any_number/',
                type: 'POST',
                data: 'message=' + chat_message + '&number=' + chat_number,
                success: function (data) {
                    //called when successful
                    var result = JSON.parse(data);
                    var chat = result.result;

                    $("#destination" + sid).click();
                    $("#btn-inputchat").val("");

                    //  $('#ajaxphp-results').html(data);
                },
                error: function (e) {
                    //called when there is an error
                    alert("Error in sending message. please try after some time");
                }
            });




        });




    </script>