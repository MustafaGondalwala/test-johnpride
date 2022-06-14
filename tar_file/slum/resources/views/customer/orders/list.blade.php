<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">My Orders</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container">
 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

		<?php
         $storage = Storage::disk('public');
         $path = 'customer_designs/thumb/';

          ?>

		<div class="sccountsec">
		

		<?php
        if(!empty($res) && $res->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $res->appends(request()->query())->links() }}

                <table class="table headth bordermain table-hover">
                    <tr>
                        <th>Order Id</th>
                        
                        <th>Total (Rs)</th>
                        <th>Order Status</th>
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($res as $rec)
                    {
                     
                      
                        ?>
                        <tr>

                            <td>{{$rec->order_id}}</td>
                            <td>{{$rec->total}}</td>

                            <td><?php echo $order_model->orderStatus($rec->order_status);  ?></td>


                            
                        


                            <td> <?php $added_on = CustomHelper::DateFormat($rec->created_at, 'd F y'); ?>{{$added_on}}</td>
                            
                           
                          
                            <td>

                            <a href="{{url('user/orders/view_order/'.$rec->order_id)}}">View Order</a>
                               
                               

                                 

                                
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
		
			

		</div> 
	</div>
</section>

@include('common.footer')


 
</body>
</html>