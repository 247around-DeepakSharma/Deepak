<div style="position: inherit;padding: 0 30px;border-left: 1px solid #e7e7e7">
<div style="padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;">
<div class="row" >
<di style="width:100%" >
<h2 style="padding-bottom: 9px;margin: 40px 0 20px; border-bottom: 1px solid #eee;">SF Engineer Not Assign Report</h2>
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
                  line-height: 1.42857143;     text-align: center;">No</th>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;     text-align: center;">SF Name</th>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;     text-align: center;">Today Engineer Not Assigned </th>
               <th  style="border-bottom-width: 2px;border: 1px solid #ddd;
                  vertical-align: bottom;padding: 8px;
                  line-height: 1.42857143;     text-align: center;">Past Engineer Not Assigned </th>

            </tr>
         </thead>
         <tbody>
            <?php if(isset($data)){ foreach ($data as $key => $value) { ?>
            <tr>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $key +1; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $value['service_center_name']; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $value['today_unassigned']; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $value['past_unassigned']; ?></td>
            </tr>
            <?php   }  } else { $count = 1; ?>
            <tr>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $count; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $service_center_name; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $today_unassigned; ?></td>
               <td style="    border: 1px solid #ddd;    padding: 8px;
                  line-height: 1.42857143;
                  vertical-align: top;    text-align: center;"><?php echo $past_unassigned; ?></td>
            </tr>
                
            <?php $count++; }?>
         </tbody>
      </table>
   </div>
</div>
