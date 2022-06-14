@component('admin.layouts.main')

    @slot('title')
        Admin - {{$title}} - {{ config('app.name') }}
    @endslot

    <div class="row">

        <div class="col-md-12">

            <h2>{{$heading}}</h2>
            <div class="bgcolor">

            <!-- @include('snippets.errors') -->
            @include('snippets.flash')

            @if (session('errMsg'))
            <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('errMsg') }}
            </div>
            @endif

            <?php
            //pr($coupons->toArray());
            $coupon_id = (isset($coupons->id))?$coupons->id:0;
            $name = (isset($coupons->name))?$coupons->name:'';
            $code = (isset($coupons->code))?$coupons->code:'';
            $type = (isset($coupons->type))?$coupons->type:'';
            $discount = (isset($coupons->discount))?$coupons->discount:'';
            $description = (isset($coupons->description))?$coupons->description:'';
            $use_limit = (isset($coupons->use_limit))?$coupons->use_limit:'';
            $min_amount = (isset($coupons->min_amount))?$coupons->min_amount:'';
            $max_amount = (isset($coupons->max_amount))?$coupons->max_amount:'';
            $max_discount_amt = (isset($coupons->max_discount_amt))?$coupons->max_discount_amt:'';
            $start_date = (isset($coupons->start_date))?$coupons->start_date:'';
            $expiry_date = (isset($coupons->expiry_date))?$coupons->expiry_date:'';
           
            $status = (isset($coupons->status))?$coupons->status:1;           

            if(is_numeric($coupon_id) && $coupon_id > 0){
                $action_url = url('admin/coupons/edit', $coupon_id);
            }
            else{
                $action_url = url('admin/coupons/add');
            }            
            ?>

            <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                

                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                    <label for="name" class="control-label required">Name:</label>

                    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $name) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'name'])
                </div>


                <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }} col-md-6">
                    <label for="code" class="control-label required">Code:</label>

                    <input type="text" id="code" class="form-control" name="code" value="{{ old('code', $code) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'code'])
                </div>


                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }} col-md-6">
                    <label for="type" class="control-label required">Discount Type:</label>

                    <select name="type" class="form-control" >
                        <option value="value" <?php echo ($type=="value") ? 'selected':''; ?>>By Value</option>
                        <option value="percentage" <?php echo ($type=="percentage") ? 'selected':''; ?>>By Percentage</option>
                    </select>

                    @include('snippets.errors_first', ['param' => 'type'])
                </div>


                <div class="form-group{{ $errors->has('discount') ? ' has-error' : '' }} col-md-6">
                    <label for="discount" class="control-label required">Discount<span class="percentSign"></span> : </label>

                    <input type="text" id="discount" class="form-control" name="discount" value="{{ old('discount', $discount) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'discount'])
                </div>

                <div class="form-group{{ $errors->has('max_discount_amt') ? ' has-error' : '' }} col-md-6">
                    <label for="max_discount_amt" class="control-label required">Maximum Discount Amount : </label>

                    <input type="text" name="max_discount_amt" value="{{ old('max_discount_amt', $max_discount_amt) }}" class="form-control"  maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'max_discount_amt'])
                </div>




                 <div class="form-group{{ $errors->has('min_amount') ? ' has-error' : '' }} col-md-6">
                    <label for="min_amount" class="control-label required">Min. Order Amount : </label>

                    <input type="text" name="min_amount" value="{{ old('min_amount', $min_amount) }}" class="form-control"  maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'min_amount'])
                </div>


                 <?php
                 /*
                 <div class="form-group{{ $errors->has('max_amount') ? ' has-error' : '' }} col-md-6">
                    <label for="min_amount" class="control-label ">Max. Amount : </label>

                    <input type="text" name="max_amount" value="{{ old('max_amount', $max_amount) }}" class="form-control"  maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'max_amount'])
                </div>
                 */
                 ?>


                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }} col-sm-12">
                    <label for="description" class="control-label">Description   : </label>

                    <textarea name="description" class="form-control" maxlength="255" >{{ old('description', $description) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'description'])
                </div>

                
               


                





                 <div class="form-group{{ $errors->has('use_limit') ? ' has-error' : '' }} col-md-6">
                    <label for="use_limit" class="control-label ">Use Limit : </label>

                    <input type="text" id="use_limit" class="form-control" name="use_limit" value="{{ old('use_limit', $use_limit) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'use_limit'])
                </div>


                <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }} col-md-6">
                    <label for="start_date" class="control-label required">Start Date : </label>

                    <input type="text" id="start_date" class="form-control expire_at" name="start_date" value="{{ old('start_date', ($start_date) ? date('d/m/Y',strtotime($start_date)): date('d/m/Y'))  }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'start_date'])
                </div>


                <div class="form-group{{ $errors->has('expiry_date') ? ' has-error' : '' }} col-md-6">
                    <label for="expiry_date" class="control-label required">Expire Date : </label>

                    <input type="text" id="expiry_date" class="form-control expire_at" name="expiry_date" value="{{ old('expiry_date', ($expiry_date) ? date('d/m/Y',strtotime($expiry_date)): date('d/m/Y'))  }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'expiry_date'])
                </div>
                

                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }} col-md-12">

                <?php
                $sel_status = old('status', $status);
                //echo 'sel_status='.$sel_status;
                ?>

                    <label for="type" class="control-label required">Status:</label>
                    <br>
                    <input type="radio" name="status" value="1" {{($sel_status == 1)?'checked':''}}>Active
                    &nbsp;
                    <input type="radio" name="status" value="0" {{($sel_status == 0)?'checked':''}}>Inactive
                        
                    </select>



                    @include('snippets.errors_first', ['param' => 'status'])
                </div>



                <div class="form-group col-md-12">

                <input type="hidden" name="coupon_id" value="{{$coupon_id}}">

                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <a href="{{ url('admin/coupons') }}" class="btn resetbtn btn-primary" title="Cancel">Cancel</a>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
            
        </div>
    </div>

    @slot('bottomBlock')

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( ".expire_at" ).datepicker({
                'dateFormat':'dd/mm/yy'
            });
        });

        $(document).on("change", "select[name=type]", function(){
            checkDiscountType();
        });

        $(document).on("change keyup", "input[name=discount]", function(){
            checkDiscountType();
        });

        function checkDiscountType(){
            var type = $("select[name=type]").val();
            var max_discount_amt = $("input[name=max_discount_amt]");

            var max_discount_value = "{{$max_discount_amt}}";

            var sign = '';

            if(type == 'percentage'){
                sign = '(%)';
                max_discount_amt.val(max_discount_value);
                max_discount_amt.attr("readonly", false);
            }
            else{
                var discount = $("input[name=discount]").val();
                max_discount_amt.val(discount);
                max_discount_amt.attr("readonly", true);
            }

            $(".percentSign").text(sign);
        }

        checkDiscountType();

    </script>
    @endslot

@endcomponent
