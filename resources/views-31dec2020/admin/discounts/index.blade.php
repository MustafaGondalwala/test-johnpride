@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Discounts - {{ config('app.name') }}
    @endslot

   <?php
   $BackUrl = CustomHelper::BackUrl();
   $category_types = config('custom.category_types');

   $parent_id = (request()->has('parent_id'))?request()->parent_id:'';

   $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

   $type = (request()->has('type'))?request()->type:'';
   ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h2 class="pull-left">Discounts - {{ucwords($type)}}</h2>

            <a href="{{ route('admin.discounts.add', ['','type'=>$type]) }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Discount</a>
            

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
            

            @include('snippets.errors')
            @include('snippets.flash')

             
            <?php
            if(!empty($discounts) && $discounts->count() > 0){
                ?>
                <div class="table-responsive">
                    {{$discounts->appends(request()->query())->links()}}
                    <table class="table table-striped">
                        <tr>
                            <th>Min. Length (meter)</th>
                            <th>Max. Length (meter)</th>
                            
                            <?php if($type == 'printing' || $type == 'fabric') { ?>
                            <th>Value (%)</th>
                            <?php } ?>

                            <?php if($type == 'designer_commission') { ?>
                            <th>Value (Rs)</th>
                            <?php } ?>

                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($discounts as $discount){
                            $status = ($discount->status == "1")?'Active':'Inactive';
                            ?>
                        <tr>

                            <td>{{$discount->min_len}}</td>
                            <td>{{$discount->max_len}}</td>
                            <td>{{$discount->value}}</td>
                            <td>{{$status}}</td>
                            <td>
                                <a href="{{ route('admin.discounts.add', [$discount['id'], 'type'=>$type, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;
                                <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                <form method="POST" action="{{ route('admin.discounts.delete', $discount['id']) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this discount?');" class="delForm">
                                    {{ csrf_field() }}
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                        ?>
                    </table>
                    {{$discounts->appends(request()->query())->links()}}
                </div>
                <?php
            }
            else{
                ?>
                <div class="alert alert-warning">There are no records at the present.</div>
                <?php
            }
            ?>

        </div>

    </div>



    @slot('bottomBlock')
    <script type="text/javascript">

            $(document).on("click", ".sbmtDelForm", function(e){
                e.preventDefault();

                $(this).siblings("form.delForm").submit();                
            });
        </script>
    @endslot

@endcomponent