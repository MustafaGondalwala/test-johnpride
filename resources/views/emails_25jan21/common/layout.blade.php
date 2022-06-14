<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Johnpride</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Maven+Pro&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
      <tr>
        <td>
          <table width="700" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style="border: 1px solid #ddd;">
            <tr>
              <td>

                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td style="padding: 20px 0px 20px 40px;">
                        <a href="{{url('')}}"><img src="<?php echo url('images/logo.png'); ?>" alt="" width="50" height="80"></a>
                      </td>
                    </tr>
                    <tr bgcolor="#A77736">
                      {{ $heading or '' }}

                      {{ $headingIcon or '' }}

                    </tr>



                    {{ $pageBlock or '' }}


                  </tbody>   
                </table>


                {{ $slot }}

              </td>
            </tr>

            {{ $footerBlock or '' }}

          </table>

        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>