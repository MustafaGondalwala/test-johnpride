@component('admin.layouts.main')

    @slot('title')
        Admin - Shipping Zones - {{ config('app.name') }}
    @endslot

   
	<div class="row">
		<div class="col-md-12">
			<div class="titlehead">
				<h1 class="pull-left">Shipping Zones</h1>
				<a href="{{url('admin/shippingzones/add')}}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Shipping Zone</a>
			</div>
		</div>
	</div>         
            
    <div class="row">

        <div class="col-md-12">

            @if (session('sccMsg'))
            <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('sccMsg') }}
            </div>
            @endif

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif


            @include('snippets.errors')
            @include('snippets.flash')

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif
            
          
            <div class="table-responsive">

            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                if(!empty($ShippingZone_list) && count($ShippingZone_list) > 0){
                    foreach($ShippingZone_list as $sz){
                    ?>
                    <tr>
                        <td>{{ $sz->name }}</td>

                        <td>{{ CustomHelper::getStatusStr($sz->status) }}</td>
                        <td>
                            <a href="{{url('admin/shippingzones/edit/'.$sz->id)}}" class=""><i class="fas fa-edit"></i></a>
                            <a href="javascript:void(0)" class="delete" data-id="{{$sz->id}}"><i class="fas fa-trash-alt"></i></a>

                            
                            <form name="delete_shippingzone" method="post" action="{{url('admin/shippingzones/delete/'.$sz->id)}}" onsubmit="return confirm('Are you sure to delete this record?')" style="display: inline-block;" id="delete-form-{{$sz->id}}">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="POST">
                            </form>

                            <?php
                            ?>
                        </td>
                    </tr>

                    <?php
                }
                ?>

            <?php
            }
            ?>
                
            </table>
            {{ $ShippingZone_list->appends(request()->query())->links() }}
            </div>
            <hr />


        </div>

    </div>

@endcomponent
<script>
    $('.delete').click(function()
    {
        var id = $(this).attr('data-id');
        $("#delete-form-"+id).submit();
    });
</script>