@component('admin.layouts.main')

    @slot('title')
        Admin - {{$title}} - {{ config('app.name') }}
    @endslot

    <div class="row">

        <div class="col-md-5">

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
            //prd($brand);
            $shippingrate_id = (isset($shippingrates['id']))?$shippingrates['id']:0;
            $dt_title = (isset($shippingrates['title']))?$shippingrates['title']:'';
            $contents = (isset($shippingrates['contents']))?$shippingrates['contents']:'';
            $per_kg = (isset($shippingrates['per_kg']))?$shippingrates['per_kg']:'';
           
            $status = (isset($shippingrates['status']))?$shippingrates['status']:1;           

            if(is_numeric($deliveryterms_id) && $deliveryterms_id > 0){
                $action_url = url('admin/shippingrates/add', $shippingrates['id']);
            }
            else{
                $action_url = url('admin/shippingrates/add');
            }            
            ?>

            <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <?php
            //echo 'user_id='.$user_id; die;
            if(!empty($shippingrate_id) && $shippingrate_id > 0){
               ?>
               {{method_field('PUT')}}
               <?php
            }
            ?>
                

                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="control-label required">Title:</label>

                    <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $dt_title) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'title'])
                </div>


                <div class="form-group{{ $errors->has('contents') ? ' has-error' : '' }}">
                    <label for="contents" class="control-label">Contents:</label>

                    <textarea id="contents" class="form-control" name="contents">{{ old('contents', $contents) }}</textarea>

                    @include('snippets.errors_first', ['param' => 'contents'])
                </div>

                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">

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

                <div class="form-group">

                <input type="hidden" name="shippingrate_id" value="{{$shippingrate_id}}">

                    <button class="btn btn-success"><i class="fa fa-save"></i> Save</button>

                    <a href="{{ url('admin/shippingrate') }}" class="btn resetbtn btn-primary" title="Cancel" style="padding: 10px 15px">Cancel</a>
                </div>

            </form>
			<div class="clearfix"></div>
        </div>
            
        </div>
    </div>

@endcomponent



    