<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ship Plans</title>
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
      <div class="container-custom">
        <div class="panel panel-default"> 
          <div class="topHeading panel-heading"><span></span><span></span><span></span>Reseller Ship Plans</div>
          <div class="panel-body">
            <div class="col-md-12">
            <div class="aboutText">
              <div class="shipMent col-md-12">
                <h2>Shipment Charges</h2>
                <p>**Minimum charge per shipment is <span><i class="gstNumber fa fa-inr" aria-hidden="true"></i> 25</span></p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="panelAccount  panel panel-default">
                <div class="panel-heading">Clothing</div>
                <div class="panel-body accountInfo widthLg noPaddings">
                  <div class="col-md-12 businessInfo noPaddings">
                    <div class="table teble-responsive noMargins">
                      <table class="tableClothing table table-striped text-center noMarginsBottom">
                        <tr class="topTr">
                          <th class="text-center" width="22%">On actual parcel <p>weight</p></th>
                          <th class="text-center" width="26%">National<p>per kg</p></th>
                          <th class="text-center" width="26%">Regional<p>per kg</p></th>
                          <th class="text-center" width="26%">Local<p>per kg</p></th>
                        </tr>
                        <tr>
                          <td>0-10 kg</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                        </tr>
                        <tr>
                          <td>10-50 kg</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                        </tr>
                        <tr>
                          <td>50-100 kg</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                        </tr>
                        <tr>
                          <td>> 100 kg</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                          <td><i class="fa fa-inr" aria-hidden="true"></i> 20</td>
                        </tr>
                      </table>
                    </div>
                    </div>
                </div>
              </div>
              </div>            
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('common.footer')

</body>
</html>
