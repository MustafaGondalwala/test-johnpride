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
			<h1 class="pull-left">Newslettersubscriber ({{ $res->count() }})</h1>

             
            
           
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

                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th class="">Email</th>
                        
                        <th class="">Action</th>
                    </tr>
                    <?php
                    
                    foreach ($res as $rec){

                     ?>

                        <tr>
                            <td>{{$rec->email}}</td>
                            

                            <td>
                               
                               
                                <a href="{{url('admin/newslettersubscriber/delete/'.$rec->id)}}" title="Delete"><i class="fas fa-trash"></i></a>
                                


                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $res->appends(request()->query())->links() }}
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

