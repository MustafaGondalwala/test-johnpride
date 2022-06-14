@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Size - {{ config('app.name') }}
    @endslot

    <?php

    $id = 0;

    $id = (isset($sizeRow->id))?$sizeRow->id:0;
    $name = (isset($sizeRow->name))?$sizeRow->name:'';
    $status = (isset($sizeRow->status))?$sizeRow->status:1;
  
   
    $action_url = url('admin/sizes');

    $name_readonly = '';

    $form_heading = 'Add Size';

    if(is_numeric($id) && $id > 0){
        $action_url = url('admin/sizes', $id);
        $form_heading = 'Update Size';
    }

    ?>


    <div class="row">

    <div class="col-md-12">
            <h1>Sizes</h1>

            @include('snippets.errors')
            @include('snippets.flash')
            </div>

                <div class="col-md-12">
                <div class="topsearch">

                <h4>{{ $form_heading }}</h4>
                <br>
                    <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" role="form" class="form-inline heightform" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group col-md-2 {{ $errors->has('name') ? ' has-error' : '' }} ">
                            <label for="name" class="control-label required">Name:</label>

                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $name) }}" maxlength="255" />

                           @include('snippets.errors_first', ['param' => 'name'])
                        </div>

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>
 
                 <input type="hidden" name="id" value="{{ $id }}">

                        <button class="btn btn-success btn1search"><i class="fa fa-save"></i> Save</button>

                        <?php
                        if($id > 0){
                            ?>
                            <a href="{{ url('admin/sizes') }}" class="btn resetbtn btn-primary btn1search" title="Cancel">Cancel</a>
                            <?php
                        }
                        ?>

                    </form>

                </div>
            </div>


        <div class="col-md-12">

            
            <?php
            if(!empty($sizes) && count($sizes) > 0){
            ?>
                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>No. of Products</th>
                        <th>Status</th>                        
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($sizes as $size){
                        $countProducts = $size->countProducts();
                        ?>

                        <tr>
                            <td>{{$size->name}}</td>                            
                            <td>{{$countProducts}}</td>                            
                            <td>{{ CustomHelper::getStatusStr($size->status) }}</td>

                            <td>
                                <a href="{{ url('admin/sizes', $size->id) }}" class=""><i class="fas fa-edit"></i></a>

                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$size->id}}"><i class="fas fa-trash-alt"></i></i></a>

                                <form method="POST" action="{{ route('admin.sizes.delete', $size->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Size?');" id="delete-form-{{$size->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id" value="<?php echo $size->id; ?>">
                                </form>

                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                 {{ $sizes->appends(request()->query())->links() }}
                 <?php
             }
             else{
                ?>
                <div class="alert alert-warning">There are no Size at the present.</div>
                <?php
             }
                 ?>
        </div>

    </div>

    @slot('bottomBlock')

    <script type="text/javaScript">
        $('.sbmtDelForm').click(function(){
            var id = $(this).attr('id');
            $("#delete-form-"+id).submit();
        });
    </script>

    @endslot

@endcomponent