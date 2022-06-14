<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo (isset($meta_title))?$meta_title:'SlumberJill'?></title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="index, follow"/>
<meta name="robots" content="noodp, noydir"/>

@include('common.head')

</head>

<body>

@include('common.header')
<section class="fullwidth innerpage">
 	<div class="container">
		@include('users.nav')
		
		<div class="rightcontent">
			<div class="heading2">My Wallet</div>
			<div id="wallet-summary">
			<p>Currently you have only <i class="fa fa-inr"></i>988,275 in your wallet. <a href="javascript:void(0)" onclick="$('#wallet-summary').hide();$('#wallet-detail').show()">View Statement</a> </p>
			</div>
			
			<div class="fullwidth" id="wallet-detail" style="display: none">
          <div class="fullboxwidth">
            <div class="fullwidth c-heading contentac">
             <table id="dataTables-example" class="table table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-example_info">
              <thead>
                <tr role="row">
                  <th>Subject</th>
                  <th>Credit (Cr)</th>
                  <th>Debit (Dr)</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                                <tr>
                 <td>test</td>
                  <td><i class="fa fa-inr"></i>1,000,000</td>
                  <td></td>
                  <td>June 02 2019</td>
               
                </tr>
                                <tr>
                 <td>Debited towards order #118</td>
                  <td></td>
                  <td><i class="fa fa-inr"></i>11,725</td>
                  <td>June 02 2019</td>
               
                </tr>
                <tr><td colspan="4">
					<div class="totalamount">
					<strong>Credit (Total)</strong>: <i class="fa fa-inr"></i>1,000,000 <br> <strong>Debit (Total)</strong>: <i class="fa fa-inr"></i>11,725 <br><strong>Total Wallet Amount</strong>: <i class="fa fa-inr"></i>988,275
					</div>
					
					</td></tr>
                
              </tbody>
            </table>
            </div>
          </div>
       </div>
		</div>
	</div>
</section>
 
@include('common.footer')

</body>
</html>