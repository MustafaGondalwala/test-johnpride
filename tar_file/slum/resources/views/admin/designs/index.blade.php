	@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Products - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = (request()->has('name'))?request()->name:'';
    $old_category = (request()->has('category'))?request()->category:'';
    $old_designer = (request()->has('designer'))?request()->designer:'';
    $old_status = (request()->has('status'))?request()->status:'';

    $old_price = (request()->has('price'))?request()->price:'';
    $price_scope = (request()->has('price_scope'))?request()->price_scope:'';

    $old_stock = (request()->has('stock'))?request()->stock:'';
    $stock_scope = (request()->has('stock_scope'))?request()->stock_scope:'';

    $old_from = (request()->has('from'))?request()->from:'';
    $old_to = (request()->has('to'))?request()->to:'';
    $old_not_ordered_days = (request()->has('not_ordered_days'))?request()->not_ordered_days:'';

    $CategoryDropDown = CustomHelper::CategoryDropDown($dropdown_name='category', $type='design', $classAttr='form-control', $idAtrr='', $selected_value=$old_category);

    $compare_scope = config('custom.compare_scope');

    


    ?>
    
    <div class="row">
        <div class="col-md-12">
			<div class="titlehead">
			<h1 class="pull-left">Designs List ({{ $products->count() }})</h1>

            <a href="{{url('admin/designs/add?cid=0')}}"  
   


           class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Design</a>
            
            <?php
            /*
            <a href="{{ route('admin.designs.add', ['back_url'=>$BackUrl]) }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Product</a>
            */
            ?>

            <?php
            /*
            @permission('export_xls')
            <form name="exportForm" method="post" action="{{url('admin/fabric/export')}}" >
                {{ csrf_field() }}
                <input type="hidden" name="export_xls" value="1">

                <?php
                if(count(request()->input())){
                    foreach(request()->input() as $input_name=>$input_val){
                        ?>
                        <input type="hidden" name="{{$input_name}}" value="{{$input_val}}">
                        <?php
                    }
                }
                ?>

                <button type="submit" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export XLS</button>
            </form>
            @endpermission
            */
            ?>

			</div>
		</div>
   </div>

      <div class="row">

    <div class="col-md-12">
        <div class="bgcolor">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-2">
                        <label>Product Name/SKU:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>


                    <div class="col-md-2">
                        <label>Category:</label><br>
                        <?php echo $CategoryDropDown; ?>
                    </div>


                    <div class="col-md-2">
                        <label>Designer:</label><br>
                        <select name="designer" class="form-control" >

                                <option value="">--Select--</option>
                                <option value="0" <?php echo ($old_designer == '0')?'selected':''; ?> >Tex India</option>

                                <?php
                                if(!empty($DesignersList) && count($DesignersList) > 0){
                                    foreach($DesignersList as $dl){
                                        $d_first_name = $dl->first_name;
                                        $d_last_name = $dl->last_name;
                                        $designer_name = trim($d_first_name.' '.$d_last_name);
                                        $selected = '';
                                        if($dl->id == $old_designer){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$dl->id}}" {{$selected}} >{{$designer_name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                    </div>


                    <div class="col-md-2 checklabel1">
                        <label>Price:</label><br/>

                        <select name="price_scope" class="form-control select_qty1 ">

                            <?php
                            foreach($compare_scope as $scpKey=>$scpVal){
                                $selected = '';
                                if($scpKey == $price_scope){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $scpKey }}" {{ $selected }}>{{ $scpVal }}</option>
                                <?php
                            }
                            ?>
                        </select>

                        <input type="number" name="price" class="form-control select_qty2 " value="{{$old_price}}" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>

                    </div>


                    <div class="col-md-2 checklabel1">
                        <label>Stock:</label><br/>

                        <select name="stock_scope" class="form-control select_qty1 ">

                            <?php
                            foreach($compare_scope as $scpKey=>$scpVal){
                                $selected = '';
                                if($scpKey == $stock_scope){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $scpKey }}" {{ $selected }}>{{ $scpVal }}</option>
                                <?php
                            }
                            ?>
                        </select>

                        <input type="number" name="stock" class="form-control select_qty2 " value="{{$old_stock}}" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    </div>

                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>



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
                        <a href="{{url('admin/designs')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
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

        if(!empty($products) && $products->count() > 0){
            ?>
            <div class="table-responsive">

            {{ $products->appends(request()->query())->links() }}

                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Designer</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">SKU</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Sort Order</th>
                        <th class="text-center">Approved Status</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Created</th>
                        <th class="text-center">Action</th>
                    </tr>
                    <?php
                    $storage = Storage::disk('public');
                    foreach ($products as $product)
                    {
                        //pr($product); die;
                        $category = $product->Category;
                        $User = $product->User;

                        $category_name = (isset($category->name))?$category->name:'';

                        $CategoryBreadcrumb = CustomHelper::CategoryBreadcrumb($category, 'admin/categories?type='.$type, '', true);

                        $status = ($product->status == '1')?'Active':'Inactive';


                        $approved_status = 'Pending'; 
                        if($product->is_approved==1)
                        {

                            $approved_status = 'Approved'; 

                        }
                         if($product->is_approved==2)
                        {

                            $approved_status = 'Disapproved'; 

                        }


                        $created_at = CustomHelper::DateFormat($product->created_at, 'd M Y');

                        $designer_name = '';

                        if($product->user_id == '0'){
                            $designer_name = 'Tex India';
                        }
                        elseif(!empty($User) && count($User) > 0){
                            $user_first_name = $User->first_name;
                            $user_last_name = $User->last_name;

                            $designer_name = trim($user_first_name.' '.$user_last_name);
                        }

                           $selected_cat_name = [];
                           $exist_cat_result= DB::table('categories')->whereRaw("id in (select category_id from product_to_category where product_id= $product->id)")->get();

                           if(!empty($exist_cat_result))
                           {
                             foreach($exist_cat_result as $ex )
                             {
                                $selected_cat_name[]=$ex->name;
                             }
                           }
                           //pr($exist_cat_result); die;
                        



                        ?>

                        <tr>
                            <td>{{$product->name}}</td>
                            <td>{{$designer_name}}</td>
                            <td><?php //echo $CategoryBreadcrumb;
                            if(!empty($selected_cat_name)){
                               echo implode(',', $selected_cat_name);
                            } ?>
                                
                            </td>
                            <td>{{$product->sku}}</td>
                            <td>{{$product->price}}</td>
                            <td>{{$product->sort_order}}</td>
                            <td>{{$approved_status}}</td>
                            <td>{{$status}}</td>
                            <td>{{$created_at}}</td>

                            <td>
                                <a href="{{route('admin.designs.edit', ['id'=>$product->id, 'cid'=>$product->category_id, 'back_url'=>$BackUrl])}}" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $products->appends(request()->query())->links() }}
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

   

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(".tooltip_title").tooltip();

    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth:true,
            changeYear: true,
            yearRange:"1950:0+"
        });
    });

    $(document).on("click", ".product_status", function(){

        var curr_sel = $(this);

        var product_id = $(this).attr('data-id');
        var curr_status = $(this).attr('data-status');
        
        var conf = confirm("Are you sure to change status of this Product?");
        
        if(conf){

            _token = '{{csrf_token()}}';
            
            $.ajax({
                url: "{{ url('admin/products/ajax_change_status') }}",
                method: 'POST',
                data:{product_id, curr_status},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp.success == true){
                        curr_sel.parent().html(resp.status_html);
                        //curr_sel.remove();
                    } else {

                    }
                },
                error: function(resp) {

                }
            });
        }
    });
    
</script>