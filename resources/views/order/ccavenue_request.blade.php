<html>
<head>
<title> Custom Form Kit </title>
</head>
<body>
<center>

<form method="post" name="redirect" action="{{$redirectUrl}}"> 
<?php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
?>
</form>
</center>
<script language='javascript'>document.redirect.submit();</script>
</body>
</html>

