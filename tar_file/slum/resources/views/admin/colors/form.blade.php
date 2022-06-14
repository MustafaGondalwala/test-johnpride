@component('admin.layouts.main')

    @slot('title')
        Admin - {{$page_heading}} - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    if(empty($back_url)){
        $back_url = 'admin/colors?type='.$type;
    }

    //$parent_id = (isset($color->parent_id))?$color->parent_id:'';
    $name = (isset($color->name))?$color->name:'';
    $code = (isset($color->code))?$color->code:'';
    $status = (isset($color->status))?$color->status:'1';

    /*if(!is_numeric($parent_id) || $parent_id == 0){
        $parent_id = (isset(request()->parent_id))?request()->parent_id:'';
    }*/
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

                        <?php
                        /*if(!empty($colors) && count($colors) > 0){
                            ?>
                            <div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="control-label">Parent Colors:</label>

                                <select name="parent_id" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($colors as $col){
                                        $selected = '';

                                        if($col->id == $parent_id){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="{{$col->id}}" {{$selected}} >{{$col->name}}</option>
                                        <?php
                                    }
                                    ?>
                                </select>

                                @include('snippets.errors_first', ['param' => 'parent_id'])
                            </div>
                            <?php
                        }*/
                        ?>


                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Color Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="100" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>


                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                            <label class="control-label required">Color Code:</label>

                            <input type="text" name="code" class="form-control" value="{{ old('code', $code) }}" maxlength="100" />

                            @include('snippets.errors_first', ['param' => 'code'])
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