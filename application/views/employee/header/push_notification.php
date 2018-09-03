<script type="text/javascript">
    //Show PopUp(Permission) Box
    (function(p,u,s,h){
        p._pcq=p._pcq||[];
        p._pcq.push(['_currentTime',Date.now()]);
        s=u.createElement('script');
        s.type='text/javascript';
        s.async=true;
        //Chhavi s.src='https://cdn.pushcrew.com/js/908eb0e349c471a50f7acbef48859bb1.js';
        s.src='https://cdn.pushcrew.com/js/f4b2558b9c7d18ada9bc6ed9466b76a4.js';
        h=u.getElementsByTagName('script')[0];
        h.parentNode.insertBefore(s,h);
    })(window,document);
    //End Permisssion Box
    //Create PushCreApi Object
        window._pcq = window._pcq || [ ];
        //Call For function if someone subscribe
        _pcq.push(['subscriptionSuccessCallback',callbackFunctionOnSuccessfulSubscription]);
   //Call back function after Subscription
 function callbackFunctionOnSuccessfulSubscription(subscriberId, values) {
     if(values.status !== 'ALREADYSUBSCRIBED'){    
        saveSubscriberID(subscriberId);
      }
}
//Save Subscriber ID in DataBase
  function saveSubscriberID(subscriberID){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>push_notification/save_push_notification_subscribers',
            data: {subscriberID: subscriberID},
            success: function (response) {
            }
        });
  }
</script>
