@component('admin.layouts.main')

    @slot('title')
        Admin - {{$title}} - {{ config('app.name') }}
    @endslot
 
    <div class="row">

        <div class="col-md-12">

            <h2>{{$heading}}</h2>
            <div class="bgcolor">

            @include('snippets.errors')
            @include('snippets.flash')

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif

            <?php
            //prd($shippingzones);
            $shippingzone_id = (isset($shippingzones['id']))?$shippingzones['id']:0;
            $name = (isset($shippingzones['name']))?$shippingzones['name']:'';
            $status = (isset($shippingzones['status']))?$shippingzones['status']:1;

            if(is_numeric($shippingzone_id) && $shippingzone_id > 0){
                $action_url = url('admin/shippingzones/edit', $shippingzone_id);
            }
            else{
                $action_url = url('admin/shippingzones/add');
            }

            
            ?>

            <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <?php
            //echo 'user_id='.$user_id; die;

            ?>

           

                <div class="form-group col-md-4{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="control-label">Name:</label>

                    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $name) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'name'])
                </div>
                <?php
                $sel_status = old('status', $status);
                ?>
                <div class="form-group col-md-4{{ $errors->has('status') ? ' has-error' : '' }}">

                    <label for="type" class="control-label required">Status:</label>
                    <br>
                    <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>&nbsp;Active
                    &nbsp;
                    <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>&nbsp;Inactive

                    @include('snippets.errors_first', ['param' => 'status'])
                </div>
				
				<div class="clearfix"></div>
                <div class="form-group col-md-4">

                <input type="hidden" name="shippingzone_id" value="{{$shippingzone_id}}">
                <br>

                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <?php
                    if($shippingzone_id > 0){
                        ?>
                        <a href="{{ url('admin/shippingzones') }}" class="btn btn-lg btn-primary" title="Cancel">Cancel</a>
                        <?php
                    }
                    ?>
                    
                </div>
				<div class="clearfix"></div>
                <div class="form-group col-md-12">
                    <div class="row">
                        <?php
                        $zonecityId = array();

                        if(isset($ShippingZonesCities) && count($ShippingZonesCities) > 0){
                            foreach($ShippingZonesCities as $zonecity){
                                $zonecityId[] = $zonecity->city_id;
                            }
                        }

                        if(count($cities) > 0){
                            $count_city = 0;
                            ?>

                            <div class="col-md-4">
                                <ul class="Shipping-ul">
                                    <?php
                                    foreach($cities as $city){
                                        $checked = '';
                                        if(in_array($city->id, $zonecityId)){
                                            $checked = 'checked';
                                        }
                                        ?>
                                        <li>{{$city->name}} : <input type="checkbox" name="city_id[]" value="{{$city->id}}" {{$checked}}></li>
                                        <?php
                                        $count_city++;

                                        if($count_city > 30){
                                            $count_city = 0;
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="col-md-4">
                                        <ul class="Shipping-ul">
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
				<div class="clearfix"></div>	
            </form>
        </div>
            
        </div>
    </div>


@endcomponent