	@component('admin.layouts.main')

    @slot('title')
        Admin - Inventory List - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_category = app('request')->input('category');
    $old_status = app('request')->input('status');
    $old_sortBy = app('request')->input('sortBy');

    $old_price = app('request')->input('price');
    $price_scope = app('request')->input('price_scope');

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
			<h1 class="pull-left">Inventory List ({{ $inventories->count() }})</h1>

            <?php
            if( !empty($inventories) && $inventories->count() > 0){
                ?>
                <button type="button" onclick="exportList('export_inventory')" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export Inventory</button>

                <form name="exportForm" method="" action="" >
                    
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

            @include('snippets.errors')
            @include('snippets.flash')

        <?php

        if(!empty($inventories) && $inventories->count() > 0){
            ?>
            <div class="table-responsive">

            {{ $inventories->appends(request()->query())->links() }}

                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Inventory SKU</th>
                        <th class="text-center">Size</th>
                        <th class="text-center">Stock</th>
                    </tr>
                    <?php
                    $storage = Storage::disk('public');
                    foreach ($inventories as $inventory){

                        $product = $inventory->inventoryProduct;

                        if(empty($product)){
                            continue;
                        }

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
                            <td>{{$inventory->id}}</td>
                            <td>{{$product->name}}</td>
                            <td>{{$inventory->sku}}</td>
                            <td>{{$inventory->size_name}}</td>
                            <td>{{$inventory->stock}}</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $inventories->appends(request()->query())->links() }}
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

   



@slot('bottomBlock')

<script type="text/javascript">
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