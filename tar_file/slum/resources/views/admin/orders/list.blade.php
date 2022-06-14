@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Customers - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_email = app('request')->input('email');
    $old_phone = app('request')->input('phone');
    $old_wallet = app('request')->input('old_wallet');
    $old_status = app('request')->input('status');

    $order_status = app('request')->input('order_status');

    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');

    $compare_scope = config('custom.compare_scope');

    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Orders </h1>

            
            

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



   <div class="row">

    <div class="col-md-12">
        <div class="bgcolor topsearch1">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-2">
                        <label class="control-label">Name:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>

                    <div class="col-md-2">
                        <label>Email:</label><br/>
                        <input type="email" name="email" class="form-control admin_input1" value="{{$old_email}}">
                    </div>

                    <div class="col-md-2">
                        <label>Phone:</label><br/>
                        <input type="text" name="phone" class="form-control admin_input1" value="{{$old_phone}}">
                    </div>

                    <div class="col-md-2">
                        <label>Order Status:</label><br/>

                        


                        <select name="order_status" class="form-control admin_input1">
                            <option value="">Please Select</option>
                            <?php if($order_status_arr->count())
                            {
                                 foreach($order_status_arr as $os)
                                 {
                                    ?>
                                     <option value="{{$os->status_id}}"  <?php if($order_status!='' && $order_status == $os->status_id ) { echo 'selected'; } ?>> {{$os->status}} </option>

                                    <?php 
                                 }

                            }
                            ?>
                           
                            
                        </select>
                    </div>



                    <div class="col-md-2">
                        <label>From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}">
                    </div>

                    <div class="col-md-2">
                        <label>To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}">
                    </div>

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/orders')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>




<div class="row">
    <div class="col-md-12">

        @include('snippets.errors')
        @include('snippets.flash')        

        <?php
        if(!empty($res) && $res->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $res->appends(request()->query())->links() }}

                <table class="table table-striped">
                    <tr>
                        <th>Order Id</th>
                        <th>Billing Address</th>
                        <th>Shipping Address</th>
                       
                        <th>Total (Rs)</th>
                        <th>Order Status</th>
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($res as $rec)
                    {

                          $billing_country_name=$shipping_country_name='';
                          $billing_state_name=$shipping_state_name='';
                          $billing_city_name=$shipping_city_name='';

                          if($rec->billing_country)
                          {

                              $billing_country_res= $country_model->where(['id'=>$rec->billing_country])->first();
                              $billing_country_name=$billing_country_res->name; 
                          }


                          if($rec->billing_state)
                          {

                              $billing_state_res= $state_model->where(['id'=>$rec->billing_state])->first();
                              $billing_state_name=$billing_state_res->name; 

                          }
                          if($rec->billing_city)
                          {

                              $billing_city_res= $city_model->where(['id'=>$rec->billing_city])->first();
                              $billing_city_name=$billing_city_res->name; 
                          }

                          //shipping c,s, city
                          if($rec->shipping_country)
                          {

                              $shipping_country_res= $country_model->where(['id'=>$rec->shipping_country])->first();
                              $shipping_country_name=$shipping_country_res->name; 
                          }

                          if($rec->shipping_state)
                          {

                              $shipping_state_res= $state_model->where(['id'=>$rec->shipping_state])->first();
                              $shipping_state_name=$shipping_state_res->name; 

                          }
                          if($rec->shipping_city)
                          {

                              $shipping_city_res= $city_model->where(['id'=>$rec->shipping_city])->first();
                              $shipping_city_name=$shipping_city_res->name; 
                          }



                         
                      
                        







                        ?>
                        <tr>

                            <td>{{$rec->order_id}}</td>
                            <td>

                            
                        Name : {{$rec->billing_first_name.' '.$rec->billing_last_name}}<br>
                        Email : {{$rec->billing_email}} <br>
                        Phone : {{$rec->billing_phone}} <br>
                        Address1 : {{$rec->billing_address1}} <br>
                        Address2 : {{$rec->billing_address2}}<br>
                        City : {{$billing_city_name}}  <br>
                        Pin Code :  <br>
                        
                        State : {{$billing_state_name}}  <br>
                        Country :  {{$billing_country_name}}
                        
                       
                        
                              

                       </td>

                       <td> 
                        Name : {{$rec->shipping_first_name.' '.$rec->shipping_last_name}}<br>
                        Email : {{$rec->shipping_email}} <br>
                        Phone : {{$rec->shipping_phone}} <br>
                        Address1 : {{$rec->shipping_address1}} <br>
                        Address2 : {{$rec->shipping_address2}} <br>
                        City : {{$shipping_city_name}}  <br>
                        Pin Code : {{$rec->shipping_pincode}} <br>
                        State : {{$shipping_state_name}}  <br>
                        Country :  {{$shipping_country_name}} </td>
                            
                            <td>{{$rec->total}}</td>

                            <td><?php echo $order_model->orderStatus($rec->order_status);  ?></td>


                            
                        


                            <td> <?php $added_on = CustomHelper::DateFormat($rec->created_at, 'd F y'); ?>{{$added_on}}</td>
                            
                           
                          
                            <td>

                            <a href="{{url('admin/orders/view_order/'.$rec->order_id)}}">View Order</a>
                               
                               

                                 

                                
                                <?php
                                /*
                                &nbsp;
                                <a href="{{ route('admin.designers.designs', [$designer_id, 'back_url'=>$BackUrl]) }}" title="Manage Designs" ><i class="far fa-object-group"></i></a>
                                
                                &nbsp;
                                <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                <form method="POST" action="{{ route('admin.designers.delete', $designer_id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this designer?');" class="delForm">
                                    {{ csrf_field() }}
                                </form>
                                */
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $res->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no orders at the present.</div>
            <?php
        }
        ?>

    

<br /><br />

</div>
</div>

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
      $( function() {
        $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });
    });
    
     $(document).on("click", ".searchbtn", function(){
        $('.searchshow').fadeToggle();
    });
</script>




























