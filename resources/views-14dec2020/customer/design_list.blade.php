<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">My Designs</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container">
 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

		<?php
         $storage = Storage::disk('public');
         $path = 'designs/thumb/';

          ?>

		<div class="sccountsec">

        <?php $logged_user= auth()->user();   ?>


         <?php if($logged_user->type=='designer') { ?>
		


        <div class="row">

		<div class="col-md-12 form-group">
		<span class="pull-right"><a class="btn" href="{{url('user/upload_design')}}">Upload New Design</a></span>
		</div>
		</div>

        <?php } ?>

		<?php
        if(!empty($res) && $res->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $res->appends(request()->query())->links() }}

                <table class="table headth bordermain">
                    <tr>
                        <th>Design name</th>
                        <!--<th>Status</th>-->
                        <th>Approved Status</th>
                        <th>Image</th>
                        
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($res as $rec)
                    {
                     $image_name = $rec['getDesignImage']['name'];
                     //pr($image_name); 
                      
                        ?>
                        <tr>

                            <td>{{$rec->name}}</td>

                            <?php /* ?><td><?php echo ($rec->status==1)?'Active':'Incative'; ?></td> <?php */ ?>


                            <td>
                             <?php 
                              $is_approved=$rec->is_approved;
                              $approved_status = ($is_approved == 0) ? "Pending" : (($is_approved == 1)  ? "Approved" : "Disapproved"); 

                              echo $approved_status;

                              ?>
                            </td>
                        <td>
                            <?php 
                             if(!empty($image_name) && $storage->exists($path.$image_name))
                                { 
                                 ?>
                             <img src="{{ url('public/storage/'.$path.$image_name) }}" class="imgsize">
                             <?php } ?>
                        </td>


                            <td> <?php $added_on = CustomHelper::DateFormat($rec->created_at, 'd F y'); ?>{{$added_on}}</td>
                            
                           
                          
                            <td>
                               
                                <form method="POST" action="{{route('user.delete_designs') }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this design?');" class="delForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$rec->id}}"> 
									<button name="delete_design" class="deletebtn">Delete</button>
                                    <!--<input type="submit" name="delete_design" class="deletebtn" value="Delete">-->
                                </form>

                                <a href="{{ route('user.upload_design', [$rec->id]) }}" title="Edit" ><i class="fas fa-edit"></i>Edit</a> 

                                
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
            <div class="alert alert-warning">There are no designs at the present.</div>
            <?php
        }
        ?>
		
			

		</div> 
	</div>
</section>

@include('common.footer')


 
</body>
</html>