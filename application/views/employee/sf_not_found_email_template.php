<div bgcolor="#ffffff">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif;max-width:620px">
    <tbody>
      <tr>
        <td width="100%" bgcolor="#e0e0e0" style="padding:0 0px"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="max-width:620px">
            <tbody>
              <tr>
                <td><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:24px 0;background-color: #2C9D9C;"><a href="#"><img src="https://aroundhomzapp.com/images/logo.jpg" alt="" style="border:0" width="" height="" class="CToWUd"></a></td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
      <tr>
        <td width="100%" bgcolor="#e0e0e0" style="padding:0 5px"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="max-width:610px;margin-bottom:18px;border-bottom:1px solid #ddd">
            <tbody>
              <tr>
                <td bgcolor="#ffffff"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tbody>
                      <tr>
                        <td align="left" style="padding:16px 20px 0px;min-width:276px;border-radius:3px 3px 0 0;font:bold 16px/1.65 Arial;color:#000;font-weight:bold">Hi !</td>
                      </tr>
                      <tr>
                        <td align="left" style="font:bold 16px/1.65 Century Gothic;color:#515151;padding:0px 20px 5px">SF is missing for the below details</td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style="font:13.5px/1.5 Arial;color:#000000;padding:0px 10px 8px;border-top: 1px solid #eaeaea;"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tbody>
                      <tr>
                        <td><table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
                            <tbody>
                              <tr>
                                <td style="padding:10px 10px 0px;"><table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="top" width="10" style="padding:3px 0 0 0"></td>
                                        <td style="font:13.5px/1.5 Arial;color:#000000" valign="top"><span style="color:#909090">Booking Details</span></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="top" width="10" style="padding:4px 0"></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000"><span style="color:#000000">Booking ID:</span></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000" align="right"><span style="color:#000000"><?php if(isset($booking_id)){echo $booking_id;}?></span></td>
                                      </tr>
                                      <tr>
                                        <td valign="top" width="10" style="padding:4px 0"></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000"><span style="color:#000000">Booking Pincode:</span></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000" align="right"><span style="color:#000000"><?php if(isset($booking_pincode)){echo $booking_pincode;}?></span></td>
                                      </tr>
                                      <tr>
                                        <td valign="top" width="10" style="padding:4px 0"></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000"><span style="color:#000000">Booking City</span></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000" align="right"><span style="color:#000000"><?php if(isset($city)){echo $city;} ?></span></td>
                                      </tr>
                                      <tr>
                                        <td valign="top" width="10" style="padding:4px 0"></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000"><span style="color:#000000">Partner</span></td>
                                        <td style="padding:4px 0;font:13.5px/1.5 Arial;color:#000000" align="right"><span style="color:#000000"><?php if(isset($partner_name)){echo $partner_name;} ?></span></td>
                                      </tr>
                                    </tbody>
                                  </table></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" style="padding:8px 0;border-top:1px solid #eaeaea;border-radius:0 0 2px 2px"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tbody>
                      <tr>
                        <td align="center"><table cellpadding="0" cellspacing="0" border="0" align="center">
                            <tbody>
                              <tr>
                                <td style="padding:10px 6px"><table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                    <tbody>
                                      <tr>
                                          <?php
                                          if(isset($booking_id)){
                                          ?>
                                        <td style="background-color:#2C9D9C;color:#ffffff;border-radius:2px;border:1px solid #2C9D9C;font:14px/1.5 Arial;text-align:center;height:42px;width:182px"><a href=<?php echo base_url()?>employee/vendor/get_add_vendor_to_pincode_form/<?php echo $booking_id?> style="display:inline-block;outline:medium none;text-decoration:none;color:#ffffff;font-weight:bold;text-align:center;min-height:42px;width:182px;line-height:42px;vertical-align:middle" target="_blank"><p style="font:normal 16px Arial">Assign Service Center</p></a></td>
                                      <?php
                                          }
                                      ?>
                                      </tr>
                                    </tbody>
                                  </table></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
    </tbody>
  </table>
</div>