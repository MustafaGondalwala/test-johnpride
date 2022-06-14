<?php 
$MERCHANT_KEY = $key; // add your id
$SALT = $salt; // add your id

$curl = $payumoney_response_url;
//$PAYU_BASE_URL = "https://test.payu.in";
$PAYU_BASE_URL = $payumoney_base_url;
$action = '';
$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$posted = array();
$posted = array(
    'key' => $MERCHANT_KEY,
    'txnid' => $txnid,
    'amount' => $amount,
    'firstname' => $firstname,
    'email' => $email,
    'phone' => $phone,
    'productinfo' => $productinfo,
    'surl' => $surl,
    'curl' => $curl,
    'furl' => $curl,
    'udf1' => $udf1,
    'service_provider' => $service_provider,
);

if(empty($posted['txnid'])) {
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} 
else 
{
    $txnid = $posted['txnid'];
}
$hash = '';
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
    $hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';  
    foreach($hashVarsSeq as $hash_var) {
        $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
        $hash_string .= '|';
    }
    $hash_string .= $SALT;

    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
} 
elseif(!empty($posted['hash'])) 
{
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}


?>


<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
           payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    Processing.....
        <form action="<?php echo $action; ?>" method="post" name="payuForm"><br />
            <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" /><br />
            <input type="hidden" name="hash" value="<?php echo $hash ?>"/><br />
            <input type="hidden" name="txnid" value="<?php echo $txnid ?>" /><br />
            <input type="hidden" name="amount" value="<?php echo $amount ?>" /><br />
            <input type="hidden" name="firstname" id="firstname" value="<?php echo $firstname ?>" /><br />
            <input type="hidden" name="email" id="email" value="<?php echo $email ?>" /><br />
            <input type="hidden" name="phone" id="phone" value="<?php echo $phone ?>" /><br />
            <input type="hidden" name="productinfo" value="<?php echo $productinfo ?>"><br />

            <input type="hidden" name="curl" value="<?php echo $curl ?>" /><br />
            <input type="hidden" name="surl" value="<?php echo $curl ?>" /><br />
            <input type="hidden" name="furl" value="<?php echo $curl ?>" /><br />
            <input type="hidden" name="service_provider" value=""  /><br />
            <input type="hidden" name="udf1" value="{{$udf1}}"  /><br />
            <?php
            if(!$hash) { ?>
                <input type="submit" value="Submit" />
            <?php } ?>
        </form>
  </body>
</html>