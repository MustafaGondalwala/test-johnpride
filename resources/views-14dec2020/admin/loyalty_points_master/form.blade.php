@component('admin.layouts.main')

    @slot('title')
        Admin - {{$page_heading}} - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

   $facilities_arr = config('custom.facilities_arr');


    

    if(empty($back_url)){
        $back_url = 'admin/colors?type='.$type;
    }

    $name = (isset($loyaltyPointsMaster->name))?$loyaltyPointsMaster->name:'';
    $points_needed = (isset($loyaltyPointsMaster->points_needed))?$loyaltyPointsMaster->points_needed:'';
    $points_needed_max = (isset($loyaltyPointsMaster->points_needed_max))?$loyaltyPointsMaster->points_needed_max:'';
    $min_order_amount = (isset($loyaltyPointsMaster->min_order_amount))?$loyaltyPointsMaster->min_order_amount:'';
    $facilities = (isset($loyaltyPointsMaster->facilities))? explode(',',$loyaltyPointsMaster->facilities):array();
    

    $discount_type = (isset($loyaltyPointsMaster->discount_type))?$loyaltyPointsMaster->discount_type:'';
    $discount = (isset($loyaltyPointsMaster->discount))?$loyaltyPointsMaster->discount:'';
    $status = (isset($loyaltyPointsMaster->status))?$loyaltyPointsMaster->status:'1';


    ?>

            <div class="col-md-12">

            <h2>{{$page_heading}}</h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="alert_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <input type="hidden" name="id" value="{{$id}}">

				<div class="bgcolor">
                <div class="row">
                    <div class="col-sm-12 col-md-6">

                        

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="100" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>


                        <div class="form-group{{ $errors->has('points_needed') ? ' has-error' : '' }}">
                            <label class="control-label required">Points Needed Minimum:</label>

                            <input type="text" name="points_needed" class="form-control" value="{{ old('points_needed', $points_needed) }}" maxlength="20" />

                            @include('snippets.errors_first', ['param' => 'points_needed'])
                        </div>

                        <div class="form-group{{ $errors->has('points_needed_max') ? ' has-error' : '' }}">
                            <label class="control-label required">Points Needed max (No limit if points is set zero): </label>

                            <input type="text" name="points_needed_max" class="form-control" value="{{ old('points_needed_max', $points_needed_max) }}" maxlength="20" />

                            @include('snippets.errors_first', ['param' => 'points_needed_max'])
                        </div>

                        <div class="form-group{{ $errors->has('min_order_amount') ? ' has-error' : '' }}">
                            <label class="control-label required">Min order amount:</label>
    
                            <input type="text" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $min_order_amount) }}" maxlength="20" />

                            @include('snippets.errors_first', ['param' => 'min_order_amount'])
                        </div>

                        <div class="form-group{{ $errors->has('facilities') ? ' has-error' : '' }}">
                            <label class="control-label">Facilities:</label>

                            <?php 
                            
                            foreach($facilities_arr as $fc=>$fcv){ 
                                $checked = "";
                                if(is_array($facilities) && in_array($fc, old('facilities', $facilities)))
                                {
                                    $checked = "checked";
                                }


                                ?>

                                <div class="checkbox-group">
                                    
                                    {{$fcv}} <input type="checkbox" name="facilities[]" class="checkbox" value="{{ $fc }}" {{$checked}} />
                                </div>

                          


                            <?php } ?>    




                            @include('snippets.errors_first', ['param' => 'facilities'])
                        </div>

                        <div class="form-group{{ $errors->has('discount_type') ? ' has-error' : '' }}">
                            <label class="control-label">Discount type:</label>



                            <select name="discount_type" class="form-control" >
                                <option value="value" <?php echo ($discount_type=="value") ? 'selected':''; ?>>By Value</option>
                                <option value="percentage" <?php echo ($discount_type=="percentage") ? 'selected':''; ?>>By Percentage</option>
                            </select>


                            @include('snippets.errors_first', ['param' => 'discount_type'])
                        </div>

                        <div class="form-group{{ $errors->has('discount') ? ' has-error' : '' }}">
                            <label class="control-label">Discount:</label>

                            <input type="text" name="discount" class="form-control" value="{{ old('discount', $discount) }}" maxlength="20" />

                            @include('snippets.errors_first', ['param' => 'discount'])
                        </div>

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'featured'])
                        </div>

                        <div class="form-group">

                            <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                            <a href="{{ url($back_url) }}" class="btn btn-primary" >Cancel</a>
                        </div>
                    </div>

                </div>
				</div>

            </form>

        </div>

    </div>

@endcomponent