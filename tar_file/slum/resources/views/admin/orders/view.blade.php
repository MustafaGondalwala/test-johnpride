@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Customers - {{ config('app.name') }}
    @endslot


    <?php
    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Orders View </h1> 

            <?php
            if(!empty($back_url)){
                ?>
                <a href="{{ url($back_url) }}" class="btn btn-sm btn-success pull-right">Back</a>
                <?php
            }
            ?>
            </div>
        </div>
   </div>


 
        

        <?php
        if(!empty($res) && $res->count() > 0){
           
           //pr($res); ?>
            <div class="table-responsive ordership">
				<div class="orderheading">
				 <div class="col-sm-6 col-md-3 ">
					<label>Order Id :</label> {{$res->order_id}}
				</div>
				 <div class="col-sm-6 col-md-3 ">
					<label>Added on:</label> <?php $added_on = CustomHelper::DateFormat($res->created_at, 'd F y'); ?>{{$added_on}}
				</div>
				 <div class="col-sm-6 col-md-3 ">
					<label>Order Status:</label> <?php echo $order_model->orderStatus($res->order_status); ?></div>
				<div class="col-sm-6 col-md-3">
					<label>Payment Status:</label> <?php echo  ($res->payment_status==1)?'Recieved':'Pending';?>  
				</div>
				 </div>
				
               <div class="row"> 
				 <div class="col-md-6 form-group addressfilds">
					 <h4><strong>Billing Address</strong></h4>
                        <p><span>Name :</span> {{$res->billing_first_name.' '.$res->billing_last_name}}</p>
                        <p><span>Email :</span>  {{$res->billing_email}}</p>
					 	<p><span>Phone :</span> {{$res->billing_phone}}</p> 
                        <p><span>Address :</span>  {{$res->billing_address1}}, {{$res->billing_address2}}, {{$billing_city->name}}</p> 
                        <p><span>Pin Code :</span> {{$res->billing_pincode}}</p>
                        <p><span>State :</span> {{$billing_state->name}}</p> 
                        <p><span>Country :</span> {{$billing_country->name}}</p> 
				 </div>
				 
                <div class="col-md-6 form-group addressfilds">
                   <h4><strong>Shipping Address</strong></h4>
					<p><span>Name :</span> {{$res->shipping_first_name.' '.$res->shipping_last_name}}</p> 
					<p><span>Email :</span> {{$res->shipping_email}}</p> 
					<p><span>Phone :</span> {{$res->shipping_phone}}</p> 
					<p><span>Address :</span> {{$res->shipping_address1}}, {{$res->shipping_address2}}, {{$shipping_city->name}}</p> 
					<p><span>Pin Code :</span> {{$res->shipping_pincode}}</p> 
					<p><span>State :</span> {{$shipping_state->name}} </p>
					<p><span>Country :</span> {{$shipping_country->name}} </p>                       
                </div>
				 
				 
            </div>

                <?php if($res->order_products->count()) { ?>
                <table class="table table-striped">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total (Rs)</th>
                        
                        
                    </tr>

                       <?php foreach($res->order_products as $items) { ?>
                   
                        <tr>

                            <td>{{$items->product_name}} <br>

                                 <?php 
                             if(!empty($items->products_images))
                                { 
                                 ?>
                             <img src="{{ url($items->products_images) }}" style="width: 75px; height:  75;"> <br>
                             <?php } ?>

                             <?php if(!empty($items->size)) { ?>
                                <b>Size :</b> {{$items->size}} <br>
                             <?php } ?>

                              <?php if(!empty($items->length)) { ?>
                                <b>Length :</b> {{$items->length}} Meter <br>
                             <?php } ?>


                             <?php

                             $design_data = [];
                             if(!empty($items->fabric_generator))
                             {
                                $design_data = json_decode($items->fabric_generator); 
                                //pr($design_data);?>

                             <?php if(!empty($design_data->layout)) { ?>
                                <b>Layout :</b> {{$design_data->layout}} <br>
                             <?php } ?>

                                <b>Rotate :</b> {{$design_data->rotate}} <br>
        
                                <b>Scale :</b> {{$design_data->scale}} <br>

                            <?php } ?>
                              

                            </td>
                            <td>{{$items->price}}</td>
                            <td>{{$items->qty}}</td>
                            <td>{{$items->price*$items->qty}}</td>
                        </tr>
                        <?php } ?>

                        

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Sub Total</b></td>
                            <td>{{$res->sub_total}}</td>
                        </tr>

                        <?php if($res->discount > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Discount</b></td>
                            <td>{{$res->discount}}</td>
                        </tr>

                        <?php } ?>

                        <?php if($res->tax > 0){?>

                         <tr>
                            <td></td>
                            <td></td>
                            <td><b>Tax</b></td>
                            <td>{{$res->tax}}</td>
                        </tr>

                        <?php } ?>

                        <?php if($res->shipping_charge > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Shipping Charge</b></td>
                            <td>{{$res->shipping_charge}}</td>
                        </tr>
                        <?php } ?>

                        <?php if($res->used_wallet_amount > 0){?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Used Wallet Amount</b></td>
                            <td>{{$res->used_wallet_amount}}</td>
                        </tr>

                        <?php } ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td>{{$res->total}}</td>
                        </tr>


                      
                </table>
                <?php } ?>                
            </div>
            <?php
        }
       ?>
        
            
 
        

        <div class="row">
        <div class="col-md-12">
            <h3>Change Order Status </h3>
        </div>
        </div>



        <form method="POST" action="" accept-charset="UTF-8"  role="form">
                {{ csrf_field() }}

        <div class="row">

         <table class="table table-striped">
                    <tr>
                        <th>Payent Status</th>
                        <th>Order Status</th>
                        <th>Comment *</th>
                    </tr>

                     <tr>
                        <td>
                        <select class="form-control" name="payment_status">
                        <option value="">Please Select</option>
                        <option <?php if($res->payment_status==0) { echo 'selected'; } ?> value="0">Pending</option>
                        <option <?php if($res->payment_status==1) { echo 'selected'; } ?> value="1">Recieved</option>


                            
                        </select>
                        </td>
                        <td> <select class="form-control" name="order_status">
                        
                        <?php foreach($order_status_list as $osl) {  ?>
                        <option <?php if($res->order_status==$osl->status_id) { echo 'selected'; } ?> value="{{$osl->status_id}}">{{$osl->status}}</option>
                       
                        <?php } ?>


                            
                        </select></td>
                        <td><textarea class="form-control" name="comment"></textarea></td>
                        @include('snippets.errors_first', ['param' => 'comment'])


                        </th>
                    </tr>
                    <tr>
                        <td>
                        <input class="btn btn-sm btn-success" type="submit" name="change_order_status" value="Save">
                        </td>
                        </tr>

                    </table>

        </div>

        </form>

        <div class="row">
        <div class="col-md-12">
            <h3>Order History </h3>

            <?php if(!empty($order_history) && $order_history->count()  ) 
            { ?>

             <table class="table table-striped">
                    <tr>
                        <th>Order Status</th>
                        <th>Comment</th>
                        <th>Added On</th>
                    </tr>

               <?php foreach($order_history as $oh) 
               { 
                 $added_on = CustomHelper::DateFormat($oh->created_at, 'd F y');




                ?> 
               <tr>
               <td>
               <?php

               foreach($order_status_list as $osl) 
               {

                  if($osl->status_id==$oh->status_id)
                  {

                    echo $osl->status;

                  }

               }




                ?></td>
               <td>{{$oh->comment}}</td>
               <td>{{ $added_on}}</td>

               </tr>
               

               <?php } ?>    
              </table>
              <?php } else echo 'No history found'; ?>
         
        </div>
        </div>





@endcomponent