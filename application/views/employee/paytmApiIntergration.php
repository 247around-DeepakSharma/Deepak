<script src="<?php echo base_url() ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/moment.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/pbkdf2.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/aes.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/cipher-core.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/mode-ecb.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/hmac-sha1.min.js"></script>

 <?php// echo $postData;?>
<script type="text/javascript">
    var appName = "24x7Around";
    var passPhrase = "Z1DBK3EH01ZUMPJU";
    var salt = "QW8QQW4VVKQEQYXVRRY3TTKMTXRHNCNSOPSXFZFF9LI37ZZZXQUSDUN8EGFTRQKN";
    var appConstant = "6VFKKLZ1Y4";
    var iv;
    var key2;
    var url = "http://sandbox.servify.in:5009/api/v1/ServiceRequest/fulfillRequest";
    
    var hash = hashKey({appC: appConstant, tzU: getCurrentTimeStamp()});
   
    function hashKey(data){
       console.log(data); 

    iv = ivConstant();

    var key = CryptoJS.PBKDF2(
            passPhrase,
            CryptoJS.enc.Hex.parse(salt),
            {
                keySize: 8,
                iterations: 64,
                hasher: CryptoJS.algo.SHA1
            });

            var d = new Date();
            var n = d.getTime();

            var cipher = CryptoJS.AES.encrypt(JSON.stringify(data), key, {
                iv: CryptoJS.enc.Hex.parse(iv),
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7
            });
            return cipher.toString();
        }
    
    console.log(hash);
    console.log(iv);
    
    $.ajax({
    url: '<?php echo base_url();?>partner/paytmApitest',
    method: 'POST',
    data: {dr9se2q:hash, co1cx2:iv, booking_id: '<?php echo $booking_id;?>' },
    success: function (data) {
            console.log(data);
        }
    });


    function ivConstant() {
        var key3 = "";
        var key1 = "";
        for (var i = 0; i < 8; i++) {
            key1 = Math.floor(10000000 + Math.random() * 89999999).toString(16);
            key3 += key1;
        }
        return key3.substring(0, 32);
    }

    function getCurrentTimeStamp() {
        return moment().valueOf();
    }
    

</script>