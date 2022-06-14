<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{$meta_title}}</title>
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="robots" content="index, follow"/>
  <meta name="robots" content="noodp, noydir"/>
  @include('common.head')

</head>
<body>
  @include('common.user_header')
  <section>
    <div class="contentArea">

      @include('common.left_menu')

      <div class="rightBar">
        <div class="tableArea container-custom">
          <div class="panel panel-default">
            <div class="topHeading panel-heading"> <span></span><span></span><span></span>{{trans('custom.order_confirmation')}} #<strong>{{ $orderNumber }}</strong></div>         


            <div class="welcomeContent panel-body noPaddings">
             
               <div class="row">
        <div class="col-md-12">

            <div class="alert alert-success">
            <p>
              {{trans('custom.please_print_this_confirmation_for_your_records')}}
            </p>
            <p>
              {{trans('custom.this_unique_order_number_is_confirmation_of_receipt_of_your_order')}}
            </p>
            <p>
              {{trans('custom.please_transfer_the_payment_to_the_bank_details_listed_below')}} <a href="{{ url('orders/'.$orderNumber) }}">{{trans('custom.my_orders')}}</a>.
            </p>
                <?php
                /*
                Your Confirmation Code is: <strong>{{ $confirmationCode }}</strong><br /><br />
                */
                ?>
<!-- Your Order is pending for approval.
                <br /><br /> -->
                
                <?php
                if($payment_method == 'bank_transfer'){
                  /*
                  <a href="{{ url('orders/'.$orderNumber.'/payment_details') }}">Click here to update Payment details</a><br /><br />
                  */
                  
                  /*
                  {{ CustomHelper::WebsiteSettings('BANK_DETAILS') }}
                  */
                  //echo CustomHelper::WebsiteSettings('BANK_DETAILS');

                  $bank_details = '';

                  $BankAccounts = CustomHelper::BankAccounts();

                  if(!empty($BankAccounts) && count($BankAccounts) > 0){

                    $bank_details .= '<hr />';
                    $bank_details .= '<strong>'.trans('custom.bank_details').'</strong>: <br>';

                    foreach($BankAccounts as $BA){
                      //$bank_details .= '<p>';

                      $bank_details .= trans('custom.account_name').': '.$BA->account_name.'<br>';
                      $bank_details .= trans('custom.account_number').': '.$BA->account_number.'<br>';
                      $bank_details .= trans('custom.bank_name').': '.$BA->bank_name.'<br>';
                      $bank_details .= trans('custom.branch_address').': '.$BA->brance_address.'<br>';
                      $bank_details .= trans('custom.ifsc_code').': '.$BA->brance_address.': '.$BA->ifsc_code;

                      $bank_details .= '<br><br>';

                      //$bank_details .= '</p>';
                    }
                  }

                  echo $bank_details;
                  
                }
                ?>

                <p>
                  {{trans('custom.e_mail_has_been_sent_to_you_with_info')}}
                </p>
                <p>
                  {{trans('custom.your_order_will_be_sent_as_we_receive_your_payment')}}
                </p>

                <!-- Please print or save your Order Number and Confirmation Code for a reference. -->
            </div>
        </div>
    </div>


              </div>
            
            <!-- <a href="index.php" class="btnNext btn btn-default" ><i class="whites fa fa-angle-right" aria-hidden="true"></i> </a> </div> -->
          </div>
        </div>
      </div>
    </section>

    @include('common.footer')

    <script src="{{url('public/assets')}}/js/function.js"></script>

  </body>
  </html>