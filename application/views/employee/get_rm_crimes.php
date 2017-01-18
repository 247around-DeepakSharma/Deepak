<div style="position: inherit;padding: 0 30px;border-left: 1px solid #e7e7e7">
    <div style="padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;">
        <div class="row" >
            <di style="width:100%" >
                <h2 style="padding-bottom: 9px;margin: 40px 0 20px; border-bottom: 1px solid #eee;">RM Missed Target Report</h2>
                <div  style="     margin-bottom: 20px;
                      background-color: #fff;
                      border: 1px solid transparent;
                      border-radius: 4px;
                      box-shadow: 0 1px 1px rgba(0,0,0,.05);
                      border-color: #ddd;">
                    <div style="    padding: 15px;" >
                        <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" 
                               style="margin-top:10px;     width: 100%;
                               max-width: 100%;
                               margin-bottom: 20px;    background-color: transparent;    display: table;border: 1px solid #ddd;">
                            <thead >
                                <tr>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD">No</th>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD">Regional Manager</th>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD">Booking Updated</th>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD">Booking Not Updated </th>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD"> Total Booking </th>
                                    <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                                         vertical-align: bottom;padding: 8px;
                                         line-height: 1.42857143;     text-align: center;background: #DDDDDD">Old Missed Targets</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $key => $value) {?>
                                    <tr>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo ($key+1); ?></td>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo $value['rm_name']; ?></td>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo $value['update']; ?></td>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo $value['not_update']; ?></td>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo $value['total_crimes']; ?></td>
                                        <td style="    border: 1px solid #ddd;    padding: 8px;
                                            line-height: 1.42857143;
                                            vertical-align: top;    text-align: center;"><?php echo $value['old_crimes']; ?></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table> 
                    </div>
                </div>
