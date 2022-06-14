@component('admin.layouts.main')

    @slot('title')
        Admin - Shipping Rates - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $shippingrate_id = (isset($shippingrates['id']))?$shippingrates['id']:0;
    $shipping_zone_id = (isset($shippingrates['shipping_zone_id']))?$shippingrates['shipping_zone_id']:0;
    $min_weight = (isset($shippingrates['min_weight']))?$shippingrates['min_weight']:0;
    $rate = (isset($shippingrates['rate']))?$shippingrates['rate']:0;
    $max_weight = (isset($shippingrates['max_weight']))?$shippingrates['max_weight']:'';
    $per_kg = (isset($shippingrates['per_kg']))?$shippingrates['per_kg']:'';

    $status = (isset($shippingrates['status']))?$shippingrates['status']:1;

    $action_url = url('admin/shippingrates/'.$shippingrate_id);

    $back_url = (request()->has('back_url'))?request()->back_url:'';
    ?> 
    
	<div class="row">
		<div class="col-md-12">
			<div class="titlehead">
				<h1 class="pull-left">Shipping Rates</h1>

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

			</div>
	</div>         
            
    <div class="row">

        <div class="col-md-12">
            <div class="bgcolor">
                <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" enctype="multipart/form-data" role="form" class=" ">
                    {{ csrf_field() }}
                    
                    <input type="hidden" name="back_url" value="{{$back_url}}">
					 <div class="col-md-2">
                    <div class="form-group{{ $errors->has('shipping_zone_id') ? ' has-error' : '' }} ">
                        <label for="shipping_zone_id" class="control-label required">Zone:</label><br>

                        <select name="shipping_zone_id" id="shipping_zone_id" class="form-control">
                            <option value="">Select</option>
                            <?php
                            $shipping_zone_id = old('shipping_zone_id', $shipping_zone_id);
                            foreach($ShippingZones as $SZ){
                                $selected = '';
                                if($SZ->id == $shipping_zone_id){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{$SZ->id}}" {{$selected}}>{{$SZ->name}}</option>
                                <?php
                            }
                            ?>
                        </select>                    

                        @include('snippets.errors_first', ['param' => 'shipping_zone_id'])
                    </div>
					</div>

					<div class="col-md-2">
					<div class="form-group{{ $errors->has('min_weight') ? ' has-error' : '' }} ">
					<label for="min_weight" class="control-label">Min Weight:</label><br>

					<input type="text" id="min_weight" class="form-control" name="min_weight" value="{{ old('min_weight', $min_weight) }}" maxlength="255" />

					@include('snippets.errors_first', ['param' => 'min_weight'])
					</div>
					</div>
					<div class="col-md-2">
					<div class="form-group{{ $errors->has('max_weight') ? ' has-error' : '' }} ">
					<label for="max_weight" class="control-label">Max Weight:</label><br>

					<input type="text" id="max_weight" class="form-control" name="max_weight" value="{{ old('max_weight', $max_weight) }}" maxlength="255" />

					@include('snippets.errors_first', ['param' => 'max_weight'])
					</div>
					</div>
					<div class="col-md-2">
                    <div class="form-group{{ $errors->has('rate') ? ' has-error' : '' }} ">
                        <label for="rate" class="control-label">Rate:</label><br>

                        <input type="text" id="rate" class="form-control" name="rate" value="{{ old('rate', $rate) }}" maxlength="255" />

                        @include('snippets.errors_first', ['param' => 'rate'])
                    </div>
					</div>	



                    <?php
                    $sel_status = old('status', $status);
                    ?>

						<div class="col-md-2">
                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }} ">

                        <label for="type" class="control-label required">Status:</label><br>
                        <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>&nbsp;Active
                        &nbsp;
                        <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>&nbsp;Inactive

                        @include('snippets.errors_first', ['param' => 'status'])
                    </div>
							</div>



                    <input type="hidden" name="shippingrate_id" value="{{$shippingrate_id}}">


                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <?php
                    if($shippingrate_id > 0){
                        ?>
                        <a href="{{ url('admin/shippingrates') }}" class="btn resetbtn btn-primary" title="Cancel">Cancel</a>
                        <?php
                    }
                    ?>


                </form>
				<div class="clearfix"></div>
            </div>
        </div>
    </div>


            
    <div class="row">

        <div class="col-md-12">
            <?php
            if(!empty($shippingrates_list) && count($shippingrates_list) > 0){
                ?>

                {{ $shippingrates_list->appends(request()->query())->links() }}

            <div class="table-responsive">

            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th>Zone</th>
                    <th>Weight</th>
                    <th>Rate</th>

                    <th>Action</th>
                </tr>
                <?php
                
                    foreach($shippingrates_list as $sr){
                        //pr($dt);
                        //$no_of_product = $brand->products()->count();
                        //pr($brand->countProducts());
                        $sr_min_weight = (!empty($sr->min_weight))?$sr->min_weight:'0';

                        $shipping_zone = '';


            if($sr->shipping_zone_id > 0){
                $ShippingZone = $ShippingRatesModel->ShippingZone($sr->shipping_zone_id);
                //pr($ShippingZone);

                $shipping_zone = (isset($ShippingZone->name))?$ShippingZone->name:'';
            }
                        ?>
                        <tr>
                            <td>{{ $shipping_zone }}</td>
                            <?php
                            /*
                            <td>{{$dt->contents}}</td>
                            */
                            ?>
                            <td>
                            <?php
                            echo number_format($sr_min_weight, 0).'-'. number_format($sr->max_weight, 0);
                            ?>
                            </td>
                            <td>{{ $sr->rate }}</td>
                            
                            <td>
                            <a href="{{route('admin.shippingrates.index', [$sr->id])}}"><i class="fas fa-edit"></i></a>
                            <a href="javascript:void(0)" name="delete" value="Delete" class="delete" data-id="{{$sr->id}}"><i class="fas fa-trash-alt" ></i> </a>

                           
                                <form name="delete_shippingrate" method="post" action="{{url('admin/shippingrates/delete/'.$sr->id)}}" onsubmit="return confirm('Are you sure to delete this record?')" style="display: inline-block;" id="delete-form-{{$sr->id}}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="POST">
                                </form>

                            </td>
                        </tr>
                        
                        <?php
                    }
                
                ?>
                
            </table>
            </div>
             {{ $shippingrates_list->appends(request()->query())->links() }}
           
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No records found.</div>
            <?php
        }
            ?>


        </div>

    </div>

@endcomponent
<script>
    $('.delete').click(function(){
        var id = $(this).attr('data-id');
        $('#delete-form-'+id).submit();
    });
</script>