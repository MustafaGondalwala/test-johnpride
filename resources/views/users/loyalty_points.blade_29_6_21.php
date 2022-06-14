<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      <?php echo (isset($meta_title))?$meta_title:'Johnpride'?>
    </title>
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

          <?php 
             $login_user_name = (auth()->user()->name) ? auth()->user()->name : '';

               // $plugin_array = array(
               //       "1"=>"Privilege",
               //       "2"=>"Gold",
               //       "3"=>"Diamond",
               //       "4"=>"Platinum",
               //   );

               


              $current_step = "";
              $current_ids = "";
              if(!empty($loyaltyPointsDetailsForName))
                  {
                    $current_step = $loyaltyPointsDetailsForName['name'];

                      foreach ($loyality_master as $master) 
                      {
                        if($current_step ==  $master->name)
                        {
                            $current_ids =$master->id;
                            break;
                        }
                      }


                  }
  
                 // echo $current_ids;
      
          ?>


 <?php 
$credit_total = 0;
$debit_total = 0;
$balance = 0;
  if(!empty($loyaltyPoints) && count($loyaltyPoints) > 0)
  {
    foreach($loyaltyPoints as $lp)
    {
      $credit_total = $credit_total + $lp->credit_amount;
      $debit_total = $debit_total + $lp->debit_amount;
    }

   $balance = $credit_total - $debit_total;
}

?>

		  <!-- Loyalty new -->
		  <div class="loyalty-box">
			  <h2 class="heading1"> <img src="{{url('/')}}/images/logo01.png" alt=""> My Loyalty Rewards</h2>
			  <div class="avtar-box" dataName="{{$login_user_name}}"></div>
			  <?php if(!empty($current_step)){ ?>
        <p class="avtar-title">{{$login_user_name}}, You are a</p>
			  <h3 class="heading2"><strong>{{isset($current_step) ? $current_step:'' }}</strong> with {{$balance}} points</h3>




			  <ul class="progress-tracker" currentStep="{{$current_ids}}">

         <?php 
            foreach ($loyality_master as $master) 
            {
              ?>
              <li><span>{{$master->name}}</span></li>
              <?php
            }

         ?> 
          


			  </ul>

    <?php }else{ ?>

      <h3 class="heading2"><strong> Hey</strong>, you are just 1 order away to become our Privilege Customer.</h3>
    <?php }?>

 <?php 
//prd($loyaltyPointsDetails->toArray());

     if(!empty($loyaltyPointsDetailsForName) && $loyaltyPointsDetailsForName['haveCriteria'])
        {
            ?>

            <ul class="feature-box" style="display: none;">

               <?php 
                if($loyaltyPointsDetailsForName['freeShipping'] == true)
                { 
                  ?>

                    <li><span><img src="{{url('/')}}/images/free-shipping.png" alt="Free shipping"></span>Free shipping</li>

                  <?php
                }
                ?>

               <?php 
               if($loyaltyPointsDetailsForName['instant_refund'] > 0)
               { 
                ?>
                <li><span><img src="{{url('/')}}/images/instant-refund.png" alt="Instant Refund"></span>Instant Refund</li>

              <?php 
            } 
            if($loyaltyPointsDetailsForName['instant_refund_after_shipped'] > 0) 
              { 
                ?>
                  <li><span><img src="{{url('/')}}/images/instant-refund-after-product-pickup.png" alt="Instant Refund"></span>Instant Refund after Product Pickup</li>
                   <?php 
              }
               ?>



              <!--   <li><span><img src="{{url('/')}}/images/instant-refund-after-product-pickup.png" alt="Instant Refund after Product Pickup"></span>Instant Refund after Product Pickup</li> -->

              


            </ul>

            <?php
        }
  ?>



  <?php 
  if($balance >= 100)
    {
      ?>
<button type="button" id="btn_loyality" class="redeem-btn" data-toggle="modal" data-target="#myModal">Redeem Points</button>

      <?php

    } 
  ?>    

			  
				
			  <div class="last-item">
				<!-- <h4 class="heading4">Currently you have only {{$balance}} points in your account.</h4> -->
        <a href="javascript:void(0)" class="viewStatement">View Statement</a>
        <?php if(!empty($current_step)){ ?>
          <a href="javascript:void(0)" class="viewBenefits" data-toggle="modal" data-target="#benefitBox">To See Benefits <span></span></a>
        <?php } ?>
			  </div>
				
		  </div>
		  <!-- Loyalty new end -->
      <div id="benefitBox" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Benefits</h4> </div>
              <div class="modal-body">
                <div class="ben-list">
                  <div class="main-timeline">
                      <div class="timeline">
                          <div class="timeline-content">
                              <span class="timeline-icon"></span>
                              <h2 class="title">Privilege</h2>
                              <div class="belist">
                                <ul>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/customer-service-brown.png" alt="">
                                    </small> 
                                    <span>Personalized customer support</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/offers-brown.png" alt="">
                                    </small> 
                                    <span>Personalized offers</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Priority brown.png" alt="">
                                    </small> 
                                    <span>Order priority</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Points-brown.png" alt="">
                                    </small> 
                                    <!-- <span class="strong">Credit Points</span> -->
                                    <span>5% value of every point</span>
                                  </li>
                                </ul>
                              </div>
                          </div>
                      </div>
                   
                      <div class="timeline">
                          <div class="timeline-content">
                              <span class="timeline-icon"></span>
                              <h2 class="title">Gold</h2>
                              <div class="belist">
                              <div class="common-points">
                                <h4>All Privilege Benefits +</h4>
                              </div>
                                <ul>
                                  <!-- <li>
                                    <small>
                                      <img src="{{url('/')}}/images/customer-service-brown.png" alt="">
                                    </small> 
                                    <span>Personalized customer support</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/offers-brown.png" alt="">
                                    </small> 
                                    <span>Personalized offers</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Priority brown.png" alt="">
                                    </small> 
                                    <span>Order priority</span>
                                  </li> -->
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/gifts-brown.png" alt="">
                                    </small> 
                                    <span>Gifts on your Special Days</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/shippin-brown.png" alt="">
                                    </small> 
                                    <span>Free shipping</span>
                                    <span>order above 2999</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Points-brown.png" alt="">
                                    </small> 
                                    <!-- <span class="strong">Credit Points</span> -->
                                    <span>10% value of every point</span>
                                  </li>
                                </ul>
                              </div>
                          </div>
                      </div>
                      
                      <div class="timeline">
                          <div class="timeline-content">
                              <span class="timeline-icon"></span>
                              <h2 class="title">Diamond</h2>
                              <div class="belist">
                                <div class="common-points">
                                  <h4>All Gold Benefits +</h4>
                                </div>
                                <ul>
                                  <!-- <li>
                                    <small>
                                      <img src="{{url('/')}}/images/customer-service-brown.png" alt="">
                                    </small> 
                                    <span>Personalized customer support</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/offers-brown.png" alt="">
                                    </small> 
                                    <span>Personalized offers</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Priority brown.png" alt="">
                                    </small> 
                                    <span>Order priority</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/gifts-brown.png" alt="">
                                    </small> 
                                    <span>Gift from John pride on customer's Special Days</span>
                                  </li> -->
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/refund-brown.png" alt="">
                                    </small> 
                                    <span>Instant Refund after product pickup</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/shippin-brown.png" alt="">
                                    </small> 
                                    <span>Free shipping</span>
                                    <span>order above 1499</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Points-brown.png" alt="">
                                    </small> 
                                    <!-- <span class="strong">Credit Points</span> -->
                                    <span>15% value of every point</span>
                                  </li>
                                </ul>
                              </div>
                          </div>
                      </div>
                      <div class="timeline">
                          <div class="timeline-content">
                              <span class="timeline-icon"></span>
                              <h2 class="title">Platinum</h2>
                              <div class="belist">
                                <div class="common-points">
                                  <h4>All Diamond Benefits +</h4>
                                </div>
                                <ul>
                                  <!-- <li>
                                    <small>
                                      <img src="{{url('/')}}/images/customer-service-brown.png" alt="">
                                    </small> 
                                    <span>Personalized customer support</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/offers-brown.png" alt="">
                                    </small> 
                                    <span>Personalized offers</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Priority brown.png" alt="">
                                    </small> 
                                    <span>Order priority</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/gifts-brown.png" alt="">
                                    </small> 
                                    <span>Gift from John pride on customer's Special Days</span>
                                  </li> -->
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/shippin-brown.png" alt="">
                                    </small> 
                                    <span>Free shipping</span>
                                  </li>
                                  
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/refund-brown.png" alt="">
                                    </small> 
                                    <span>Instant Refund</span>
                                  </li>
                                  <li>
                                    <small>
                                      <img src="{{url('/')}}/images/Points-brown.png" alt="">
                                    </small> 
                                    <!-- <span class="strong">Credit Points</span> -->
                                    <span>25% value of every point</span>
                                  </li>
                                </ul>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>

		  <div class="fullwidth" id="wallet-detail" style="display: none">
			<div class="fullboxwidth">
			  <div class="fullwidth c-heading contentac">
				<table id="dataTables-example" class="table table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-example_info">
				  <thead>
					<tr role="row">
					  <th>Subject
					  </th>
					  <th>Credit (Cr)
					  </th>
					  <th>Debit (Dr)
					  </th>
					  <th>Date
					  </th>
					</tr>
				  </thead>
				  <tbody>
					<?php
	$credit_total = 0;
	$debit_total = 0;
	$balance = 0;
	if(!empty($loyaltyPoints) && count($loyaltyPoints) > 0){
	foreach($loyaltyPoints as $lp){
	$credit_total = $credit_total + $lp->credit_amount;
	$debit_total = $debit_total + $lp->debit_amount;
	?>
					<tr>
					  <td>{{ $lp->description }}
					  </td>
					  <td>{{ $lp->credit_amount }}
					  </td>
					  <td>{{ $lp->debit_amount }}
					  </td>
					  <td>
						<?php
	echo CustomHelper::DateFormat($lp->created_at, 'd M Y');
	?>
					  </td>
					</tr>
					<?php
	}
	}
	$balance = $credit_total - $debit_total;
	?>
					<tr>
					  <td colspan="4">
						<div class="totalamount">
						  <strong>Credit (Total)
						  </strong>: 
						  <i class="fa fa-inr">
						  </i>{{ $credit_total }} 
						  <br> 
						  <strong>Debit (Total)
						  </strong>: 
						  <i class="fa fa-inr">
						  </i>{{ $debit_total }} 
						  <br>
						  <strong>Total Points
						  </strong>: {{ $balance }}
						</div>
					  </td>
					</tr>
				  </tbody>
				</table>
			  </div>
			</div>
		</div>




        </div>
      </div>
    </section>



	

 <!-- The Modal -->
 <div class="modal loyality-modal" id="myModal">
	<div class="modal-dialog">
	  <div class="modal-content">
		<!-- Modal Header -->
		<div class="modal-header">
		  <h4 class="modal-title">Loyality Points Description
		  </h4>
		  <button type="button" class="close" data-dismiss="modal">&times;
		  </button>
		</div>
		<!-- Modal body -->
		<div class="modal-body">
		  <h4>You can redeem this points into your wallet
		  </h4>
		  <?php
      $total_redeem_balance = 0; 
      if(!empty($loyaltyPointsDetails) && $loyaltyPointsDetails['haveCriteria'])
      {
      $total_redeem_balance = $balance * $loyaltyPointsDetails['value_of_points'];
      ?>
      		  Your Redeem Point Value : 
      		  <b>
      			<i class="fa fa-inr">
      			</i> 
      			<?php echo  floor($total_redeem_balance); ?>
      		  </b>
      		  <?php
      }
      ?>
		</div>
		<!-- Modal footer -->
		<div class="modal-footer">
			<form method="post" action="" id="frm_redeem_points">
				<button class="redeem-btn">Redeem Now
				</button>
			</form>
		  <button type="button" class="btn btn-danger" data-dismiss="modal">Close
		  </button>
		</div>
	  </div>
	</div>
  </div>

    @include('common.footer')
    <script type="text/javascript">
      $('#btn_loyality').click(function()
                               {
        var _token = '{{ csrf_token() }}';
        $.ajax({
          url: "{{ url('users/get_loyality_point_detail') }}",
          type: "POST",
          data: {
            productId:productId}
          ,
          dataType:"JSON",
          headers:{
            'X-CSRF-TOKEN': _token}
          ,
          cache: false,
          beforeSend:function(){
          }
          ,
          success: function(resp){
            if(resp.success){
              window.location.reload();
            }
          }
        }
              );
      }
                              );
      $('#frm_redeem_points').on('submit', function (e) {
        e.preventDefault();
        var _token = '{{ csrf_token() }}';
        var user_id = '<?php echo auth()->user()->id ?>';
        $.ajax({
          url: "{{ url('users/redeem_loyality_point') }}",
          type: "POST",
          //data: {user_id:user_id, wallet_credit_amount:wallet_credit_amount, debit_layality_point: debit_layality_point},
          data: {
            user_id:user_id}
          ,
          dataType:"JSON",
          headers:{
            'X-CSRF-TOKEN': _token}
          ,
          cache: false,
          beforeSend:function(){
          }
          ,
          success: function(resp){
            //console.log(resp);
            if(resp.success){
              alert("Points has been redeemed Successfully");
              window.location.reload();
            }
          }
        }
              );
      }
                                );
    </script>
	<script>
		$(".avtar-box").text($(".avtar-box").attr("dataName").charAt(0));
		$(".progress-tracker").append("<li class='main-progress'><span></span></li>");

		$( document ).ready(function() {
			var currentStep = parseInt($(".progress-tracker").attr("currentstep"));
			if(currentStep >=1 ){
				$(".progress-tracker li:nth-child(1)").addClass('active');
				$(".main-progress span").css('width', '0%')
			}
			 if(currentStep >=2){
				$(".progress-tracker li:nth-child(2)").addClass('active');
				$(".main-progress span").css('width', '33.33%')
			}
			if(currentStep >=3){
				$(".progress-tracker li:nth-child(3)").addClass('active');
				$(".main-progress span").css('width', '66.66%');
			}
			if(currentStep >=4){
				$(".progress-tracker li:nth-child(4)").addClass('active');
				$(".main-progress span").css('width', '100%')
			}

			$('.progress-tracker li.active:last').addClass("current-step");
			$('.progress-tracker li.active:last').append("<span class='you-r-here'>You're here</span>");
		});
		
		$(".viewStatement").click(function(){
			$('#wallet-detail').toggle(500);
			if ($(this).text() == "View Statement")
			{$(this).text("Hide Statement")}
			else
			{$(this).text("View Statement")}
		})
	</script>
  </body>
</html>
