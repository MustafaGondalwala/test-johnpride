<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo (isset($meta_title))?$meta_title:'Johnpride'?></title>
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
			 <div class="main_inner_box">
			<div class="heading2">My Wallet</div>
			
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

					<?php
					$credit_total = 0;
					$debit_total = 0;
					$balance = 0;
					if(!empty($wallet) && count($wallet) > 0){
					  foreach($wallet as $wl){

						$credit_total = $credit_total + $wl->credit_amount;
						$debit_total = $debit_total + $wl->debit_amount;

						?>
						<tr>
						  <td>{{ $wl->description }}</td>
						  <td>{{ $wl->credit_amount }}</td>
						  <td>{{ $wl->debit_amount }}</td>
						  <td>
							<?php
							echo CustomHelper::DateFormat($wl->created_at, 'd M Y');
							?>
						  </td>

						</tr>

						<?php
					  }
					}
					$balance = $credit_total - $debit_total;
				   ?>
				   <tr><td colspan="4">
					 <div class="totalamount">
					   <strong>Credit (Total)</strong>: <i class="fa fa-inr"></i>{{ $credit_total }} <br> <strong>Debit (Total)</strong>: <i class="fa fa-inr"></i>{{ $debit_total }} <br><strong>Total Wallet Amount</strong>: <i class="fa fa-inr"></i>{{ $balance }}
					 </div>

				   </td></tr>

				  </tbody>
				</table>
				</div>
			  </div>
       		</div>
		

      <div id="wallet-summary">
      <p>Currently you have only <i class="fa fa-inr"></i>{{$balance}} in your wallet. <a href="javascript:void(0)" onclick="$('#wallet-summary').hide();$('#wallet-detail').show()">View Statement</a> </p>
      </div>
</div>
		</div>
	</div>
</section>
 
@include('common.footer')

</body>
</html>