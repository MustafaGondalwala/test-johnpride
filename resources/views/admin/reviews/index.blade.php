	@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Products - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_customer = app('request')->input('customer');
    $old_product = app('request')->input('product');
    $old_rating = app('request')->input('rating');
    $old_status = app('request')->input('status');

    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');

    $ratingArr = [];

    for($r=1; $r<=5; $r++){
        $ratingArr[$r] = $r;
        if($r < 5){
            $rPoint = $r+0.5;
            $ratingArr["$rPoint"] = $rPoint;
        }
    }
    //pr($ratingArr);
    ?>
    
    <div class="row">
        <div class="col-md-12">
			<div class="titlehead">
			<h1 class="pull-left">Reviews ({{ $reviews->count() }})</h1>            
           
			</div>
		</div>
   </div>



   <div class="row">

    <div class="col-md-12">
        <div class="bgcolor">

            <div class="table-responsive">

                <form class="form-inline" method="GET">

                    <div class="col-md-3">
                        <label>Customer Name:</label><br/>
                        <input type="text" name="customer" class="form-control admin_input1" value="{{$old_customer}}">
                    </div>
                    
                    <div class="col-md-3">
                        <label>Product Name:</label><br/>
                        <input type="text" name="product" class="form-control admin_input1" value="{{$old_product}}">
                    </div>

                    <div class="col-md-2">
                        <label>Rating:</label><br/>
                        <select name="rating" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <?php

                            if(!empty($ratingArr) && count($ratingArr) > 0){
                                foreach($ratingArr as $rKey=>$rVal){
                                    $selected = '';
                                    if($rKey == $old_rating){
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="{{$rKey}}" {{$selected}} >{{$rVal}}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Pending</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Approved</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-3">
                        <label>From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}" autocomplete="off">
                    </div>

                    <div class="col-md-3">
                        <label>To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}" autocomplete="off">
                    </div>
                    

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/reviews')}}" class="btn resetbtn btn-primary btn1search" >Reset</a>
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

        if(!empty($reviews) && $reviews->count() > 0){
            ?>
            <div class="table-responsive">

            {{ $reviews->appends(request()->query())->links() }}

                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Heading</th>
                        <th>Comment</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    
                    foreach ($reviews as $review){

                        $reviewUser = $review->reviewUser;
                        $reviewUserName = (isset($reviewUser->name))?$reviewUser->name:'';

                        $reviewProduct = $review->reviewProduct;
                        $reviewProductName = (isset($reviewProduct->name))?$reviewProduct->name:'';

                        $reviewDate = CustomHelper::DateFormat($review->created_at, 'd M Y');

                        $comments = CustomHelper::wordsLimit($review->comment, 30);

                     ?>

                        <tr>
                            <td>
                                <a href="{{url('admin/customers?name='.$reviewUserName)}}" target="_blank">{{$reviewUserName}}</a>
                            </td>

                            <td>
                                <a href="{{url('admin/products?name='.$reviewProductName)}}" target="_blank">{{$reviewProductName}}</a>
                            </td>

                            <td>{{$review->heading}}</td>
                            <td>
                                <a href="javascript:void(0)" data-id="{{$review->id}}" class="viewBtn commentTxt" title="click to full view">{{$comments}}</a>
                                <?php
                                /*
                                <a href="javascript:void(0)" class="fullCommentBox" style="display:none;">{{$review->comment}}</a>
                                */
                                ?>
                            </td>
                            <td>{{$review->rating}}</td>
                            <td>

                                <?php echo CustomHelper::getStatusHTML($review->status, $review->id, 'changeStatus', '', 'status', 'Approved', 'Pending'); ?>
                            </td>
                            <td>{{$reviewDate}}</td>                            

                            <td>
                                <a href="javascript:void(0)" data-id="{{$review->id}}" class="viewBtn" title="View"><i class="fas fa-eye"></i></a>

                                <a href="javascript:void(0)" class="sbmtDelForm"><i class="fas fa-trash-alt"></i></a>

                                <form method="POST" action="{{ route('admin.reviews.delete', $review->id) }}" accept-charset="UTF-8" role="form" class="delForm" onsubmit="return confirm('Are you sure you want to delete this Review?');" id="delete-form-{{$review->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id" value="<?php echo $review->id; ?>">

                                </form>
                            </td>

                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $reviews->appends(request()->query())->links() }}
            <?php
                    }
                    else{
                ?>
                <div class="alert alert-warning">There are no Records at the present.</div>
                <?php
            }
            ?>
            </div>

        </div>




        <!-- View Modal -->
        <div id="viewModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- End - View Modal -->


        @slot('bottomBlock')

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javaScript">

    $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });

    $('.changeStatus').click(function(){
        var id = $(this).data('id');
        var type = $(this).data('type');
        var status = $(this).data('status');

        var conf = confirm("Are you sure you want to change this status?");

        if(conf){
            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ url('admin/reviews/change_status') }}",
                type: "POST",
                data: {id:id, type:type, status:status},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                cache: false,
                beforeSend:function(){
                },
                success: function(resp){
                    if(resp.success){
                        window.location.reload();
                    }
                    else if(resp.errors){
                        alert("something went wrong, please check errors...");
                    }
                }
            });
        }
    });
    

    /*$(".commentTxt").click(function(){
        $(this).siblings(".fullCommentBox").slideToggle();
        $(this).slideToggle();
    });

    $(".fullCommentBox").click(function(){
        $(this).siblings(".commentTxt").slideToggle();
        $(this).slideToggle();
    });*/

    $('.sbmtDelForm').click(function(){
       $(this).siblings(".delForm").submit();
    });


    $('.viewBtn').click(function(){
        var id = $(this).data('id');

        var _token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ url('admin/reviews/ajax_view') }}",
            type: "POST",
            data: {id:id},
            dataType:"JSON",
            headers:{'X-CSRF-TOKEN': _token},
            cache: false,
            beforeSend:function(){
                $("#viewModal .modal-body").html('');
            },
            success: function(resp){
                if(resp.success){
                    if(resp.reviewHtml){
                        $("#viewModal .modal-body").html(resp.reviewHtml);

                        $("#viewModal").modal("show");
                    }

                }
            }
        });

    });
</script>

@endslot

   

@endcomponent