	@component('layouts.admin')

    @slot('title')
        Admin - Download Reports - {{ config('app.name') }}
    @endslot
    
    <div class="row">
        <div class="col-md-12">
			<div class="titlehead">
			<h1 class="pull-left">Reports</h1>

            @permission('export_xls')
            <form name="exportForm" method="post" action="{{url('admin/products/export')}}" >
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

			</div>
		</div>
   </div>

   
    <div class="row">

        <div class="col-md-12">
			<div class="topsearch">           

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="table-responsive">

            <?php

            $back_url = CustomHelper::BackUrl();

            //echo "back_url=".$back_url;

            

            $old_name = app('request')->input('name');
            $old_category = app('request')->input('category');
            $old_brand = app('request')->input('brand');
            $old_status = app('request')->input('status');
            $old_new = app('request')->input('new');
            $old_sort_by = app('request')->input('sort_by');
            $old_inventory = app('request')->input('inventory');
            $inventory_scope = app('request')->input('inventory_scope');

            $old_from = app('request')->input('from');
            $old_to = app('request')->input('to');

            $compare_scope = config('custom.compare_scope');
            ?>

            <form class="form-inline" method="GET">

                <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="from">From Date:</label><br/>
                    <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}">
                </div>

                <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="to">To Date:</label><br/>
                    <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}">
                </div>

                <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="name">Product Name/Code/Description:</label><br/>
                    <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                </div>
               
               <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="brand">Brand:</label><br/>
                    <select name="brand" class="form-control brand admin_input1">
                    <option value="">--Select Brand--</option>
                        <?php
                        if(!empty($brands) && count($brands)){
                            foreach($brands as $brand){
                                $sel_brand = '';
                                if($brand->id == $old_brand){
                                    $sel_brand = 'selected';
                                }
                                ?>
                                <option value="{{ $brand->id }}" {{ $sel_brand }}>{{ $brand->name }}</option>
                                <?php
                            }
                        }
                        ?>
                     </select>
                </div>
               
                <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="category">Category:</label><br>
                    <select name="category" class="form-control category admin_input1">
                    <option value="">--Select Category--</option>
                        <?php
                       echo CategoriesDropdown($parent_id=0, $old_category);
                        ?>
                        </select>
                </div>

                <div class="col-md-2 col-sm-6 col-xs-12 checklabel1">
                    <label for="inventory">Inventory Qty:</label><br/>

                    <select name="inventory_scope" class="form-control select_qty1 ">

                    <?php
                    foreach($compare_scope as $scpKey=>$scpVal){
                            $selected = '';
                        if($scpKey == $inventory_scope){
                            $selected = 'selected';
                        }
                        ?>
                        <option value="{{ $scpKey }}" {{ $selected }}>{{ $scpVal }}</option>
                        <?php
                    }
                    ?>
                    </select>

                    <input type="number" name="inventory" class="form-control  select_qty2 " value="{{$old_inventory}}" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                </div>

                 <div class="col-md-2 col-sm-6 col-xs-12">
                    <label for="phone">Status:</label><br/>
                    <select name="status" class="form-control admin_input1">
                        <option value="">--Select--</option>
                        <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                        <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                    </select>
                </div>

                 <div class="col-md-2 col-sm-6 col-xs-12 checklabel1">
                    <label>New:</label>
                    <input type="checkbox" name="new"  value="1" {{ ($old_new == '1')?'checked':'' }}>
                </div>

                <div class="clearfix"></div>

                <?php
                $products_sort_by = config('custom.products_sort_by');
                ?>

                 <div class="col-md-2 col-sm-6 col-xs-12">
                    <label>Sort by:</label><br/>

                    <select name="sort_by" class="form-control">
                        <option value="">--Select--</option>
                        <?php
                        if(!empty($products_sort_by) && count($products_sort_by) > 0){
                            foreach($products_sort_by as $ps_key=>$ps_val){
                                ?>
                                <option value="{{$ps_key}}" {{($ps_key == $old_sort_by)?'selected':''}} >{{$ps_val}}</option>
                                <?php
                            }
                        }
                        
                        ?>                                           
                    </select>
                </div>
			 
                  
				 <div class="col-md-6 col-sm-6 col-xs-12">
                <button type="submit" class="btn btn-success btn1search">Search</button>
                <a href="{{url('admin/products')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
				</div>
            </form>
			</div>
				</div>
   		</div>

   </div>

   <div class="row">
    <div class="col-md-12">

        <h4><input type="radio" name="type">Sales Report</h4>

    </div>
</div>

   

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>