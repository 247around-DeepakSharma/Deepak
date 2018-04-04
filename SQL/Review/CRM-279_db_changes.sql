UPDATE `sms_template` SET `template` = 'Dear Customer, Request for your %s for %s is confirmed for %s with booking id %s. In case of any support, call 9555000247. 247Around, %s Partner.' WHERE `tag` = 'add_new_booking';

UPDATE `sms_template` SET `template` = 'We have received reschedule request for your %s service to %s. If you have not asked for reschedule, give missed call @ 01139586111 or call 9555000247.' WHERE `tag` = 'reschedule_booking';

UPDATE `sms_template` SET `template` = 'Dear Customer, we were unable to contact you for your %s. As per policy we will cancel the call after 3 unsuccessful attempts. Kindly call us on @ 9555000247 so that we can fix up an appointment at the earliest. 247around.' WHERE `tag` = 'call_not_picked_other';

UPDATE `sms_template` SET `template` = 'Dear Customer, the %s of your %s has been cancelled in our system. Kindly contact us on 180042525252 in case you want to log a call again. %s' WHERE `tag` = 'cancel_booking';

UPDATE `sms_template` SET `template` = 'Dear Customer, Your %s %s is completed now for your booking id %s. If you are HAPPY with the service, give miss call @ 01139588220. If not, give miss call @ 01139588224. 247Around' WHERE `tag` = 'complete_booking';


