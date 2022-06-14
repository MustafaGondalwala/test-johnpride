	@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Products - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_category = app('request')->input('category');
    $old_status = app('request')->input('status');
    $old_sortBy = app('request')->input('sortBy');

    $old_price = app('request')->input('price');
    $price_scope = app('request')->input('price_scope');
    $collection_id = app('request')->input('collection_id');

    $old_stock = app('request')->input('stock');
    $stock_scope = app('request')->input('stock_scope');

    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');
    $old_not_ordered_days = app('request')->input('not_ordered_days');

    $categoryDropDown = '';
    $categoryDropDown = CustomHelper::categoryDropDown($dropdown_name='category', $classAttr='form-control', $idAtrr='', $selected_value=$old_category);

    $compare_scope = config('custom.compare_scope');
    
    ?>
    <style>
		label{height: auto;}
		
		.categoryselect select.form-control{ width: 100%;}
</style>
    <div class="row">
        <div class="col-md-12">
			<div class="titlehead">
			<h1 class="pull-left">Products List ({{ $products->count() }})</h1>
            
            <a href="{{ route('admin.products.add', ['back_url'=>$BackUrl]) }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Product</a>

            <?php
            if( !empty($products) && $products->count() > 0){
                ?>

                <button type="button" onclick="exportList('export_xls')" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export Products</button>
                <?php
                /*
                
                */
                ?>
                <button type="button" onclick="exportList('export_inventory')" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export Inventory</button>

                <form name="exportForm" method="" action="" >
                    {{ csrf_field() }}
                    <input type="hidden" name="export_xls" value="">
                    <input type="hidden" name="export_inventory" value="">

                    <?php
                    if(count(request()->input())){
                        foreach(request()->input() as $input_name=>$input_val){
                            ?>
                            <input type="hidden" name="{{$input_name}}" value="{{$input_val}}">
                            <?php
                        }
                    }
                    ?>
                </form>
                <?php
            }
            ?>
            

			</div>
		</div>
   </div>



   <div class="row">

    <div class="col-md-12">
        <div class="bgcolor">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-3">
                        <label>Product Name/SKU:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>


                    <div class="col-md-3 categoryselect">
                        <label>Category:</label><br>
                        <?php echo $categoryDropDown; ?>
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


                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <label>Sort By:</label><br/>
                        <select name="sortBy" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="top_selling" {{ ($old_sortBy == 'top_selling')?'selected':'' }}>Top Selling
                        </select>
                    </div>

                    <div class="clearfix"></div>



                    <div class="col-md-3">
                        <label>From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}" autocomplete="off" >
                    </div>

                    <div class="col-md-3">
                        <label>To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}" autocomplete="off" >
                    </div>

                     <div class="col-md-3 categoryselect">
                        <label>Collection:</label><br>
                        
                        <select name="collection_id" class="form-control select_qty1 ">
                            <option value="">--Select Collection--</option>

                            <?php
                            foreach($collection_data as $cln){
                                $selected = '';
                                if($cln->id == $collection_id){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $cln->id }}" {{ $selected }}>{{ $cln->name }}</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/products')}}" class="btn resetbtn btn-primary btn1search" >Reset</a>
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
                        <th class="text-center">ID</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">SKU</th>
                        <th class="text-center">MRP Price</th>
                        <th class="text-center">Sort Order</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Created</th>
                        <th class="text-center">Action</th>
                    </tr>
                    <?php
                    $storage = Storage::disk('public');
                    foreach ($products as $product){

                        $productCategories = $product->productCategories;

                        $category = '';

                        if(isset($productCategories[0]) && count($productCategories[0]) > 0){
                            $category = $productCategories[0];
                        }

                        $CategoryBreadcrumb = CustomHelper::CategoryBreadcrumb($category, 'admin/categories?back_url='.$BackUrl, '');

                        $category_name = (isset($category->name))?$category->name:'';

                        $status = ($product->status == '1')?'Active':'Inactive';

                        $created_at = CustomHelper::DateFormat($product->created_at, 'd M Y');

                         $selected_cat_name = [];

                        ?>

                        <tr class="row_{{$product->id}}">
                            <td>{{$product->id}}</td>
                            <td>{{$product->name}}</td>
                            <td><?php
                            echo $CategoryBreadcrumb; 

                            if(!empty($selected_cat_name)){
                               echo implode(',', $selected_cat_name);
                            }

                            ?></td>
                            <td>{{$product->sku}}</td>
                            <td>{{$product->price}}</td>
                            <td>{{$product->sort_order}}</td>
                            <td>{{$status}}</td>
                            <td>{{$created_at}}</td>

                            <td>
                                <a href="{{route('admin.products.edit', ['id'=>$product->id,'back_url'=>$BackUrl])}}" title="Edit"><i class="fas fa-edit"></i></a>
                                &nbsp;
                                <a href="{{ url('admin/products/'.$product->id.'/inventory?back_url='.$BackUrl) }}" title="Manage Inventory" class=""><i class="fa fa-archive"></i></a>
                              
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


        <!-- Stock Modal -->
        <div id="stockModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal_alert_msg"></div>
                        
                        <div class="form-group">
                            <label class="control-label required">Add Stock:</label>

                            <input type="number" name="stock" class="form-control" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="product_id" value="">
                        <button type="button" class="btn btn-success btn-sm save_stock">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <!-- End - Stock Modal -->

   



@slot('bottomBlock')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(".tooltip_title").tooltip();

    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });
    });

    $(document).on("click", ".manage_stock", function(){

        $("#stockModal").find(".modal_alert_msg").html("");
        $("#stockModal").find("input[name='stock']").val("");

        var title = $(this).data("title");
        var id = $(this).data("id");


        $("#stockModal .modal-title").text("Manage Stock - "+title);
        $("#stockModal").find("input[name='product_id']").val(id);
        $("#stockModal").modal("show");
    });



    $(document).on("click", ".save_stock", function(){

        var curr_sel = $(this);

        var product_id = $(this).siblings("input[name='product_id']").val();

        var stock_inp = $("#stockModal").find("input[name='stock']");
        var stock = stock_inp.val();

        stock = parseInt(stock);

        if(isNaN(stock) || stock == 0 || stock == ""){
            stock_inp.parent(".form-group").addClass("has-error");
            stock_inp.focus();
            return false;
        }
        
        var conf = true;
        
        if(conf){

            _token = '{{csrf_token()}}';
            
            $.ajax({
                url: "{{ url('admin/fabrics/ajax_add_stock') }}",
                method: 'POST',
                data:{product_id: product_id, stock: stock},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){
                    stock_inp.parent(".form-group").removeClass("has-error");
                },
                success: function(resp) {
                    if(resp.success){
                        stock_inp.val("");
                        if(resp.new_stock){
                            $(".row_"+product_id).find(".pro_stock").text(resp.new_stock);
                        }
                    }
                    if(resp.message){
                        $("#stockModal").find(".modal_alert_msg").html(resp.message);
                    }
                },
                error: function(resp) {

                }
            });
        }
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


    function exportList(exportName){

        if(exportName ){
            if( exportName == 'export_xls'){
                var exportForm = $("form[name='exportForm']");

                exportForm.find("input[name='export_xls']").val('1');
                exportForm.find("input[name='export_inventory']").val('');
                document.exportForm.submit();
            }
            else if( exportName == 'export_inventory'){
                var exportForm = $("form[name='exportForm']");

                exportForm.find("input[name='export_xls']").val('');
                exportForm.find("input[name='export_inventory']").val('1');
                document.exportForm.submit();
            }
        }

    }
    
</script>

@endslot

@endcomponent