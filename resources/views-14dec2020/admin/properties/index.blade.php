@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Categories - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();


    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    $page_title = 'Properties';

    $path = 'properties/';
    $thumb_path = 'properties/thumb/';

    $storage = Storage::disk('public');
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">

            <h1 class="pull-left"><?php echo $page_title; ?></h2>

            <a href="{{ route('admin.properties.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Property</a>            

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
            if(!empty($properties) && $properties->count() > 0){
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($properties as $property){
                            $status = ($property->status == "1")?'Active':'Inactive';

                            $property_image = $property->image;
                            ?>
                        <tr>
                            <td>{{$property->name}}</td>

                            <td>
                                <?php
                                if(!empty($property_image) && $storage->exists($thumb_path.$property_image)){
                                    ?>
                                    <a href="{{url('public/storage/properties/'.$property_image)}}" target="_blank"><img src="{{url('public/storage/properties/thumb/'.$property_image)}}" style="width:50px;"></a>
                                    <?php
                                }
                                ?>
                            </td>

                            <td>{{$property->sort_order}}</td>
                            <td>{{$status}}</td>
                            <td>
                                
                                <a href="{{ route('admin.properties.add', [$property['id'], 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;

                                <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                    <form method="POST" action="{{ route('admin.properties.delete', $property['id']) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Usage?');" class="delForm">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>
                                
                            </td>
                        </tr>
                        <?php
                    }
                        ?>
                    </table>
                </div>
                <?php
            }
            else{
                ?>
                <div class="alert alert-warning">There are no property(s) at the present.</div>
                <?php
            }
            ?>

        </div>

    </div>

    @slot('bottomBlock')
        <script>

            $(document).on("click", ".sbmtDelForm", function(e){
                e.preventDefault();

                $(this).siblings("form.delForm").submit();                
            });
        </script>
    @endslot

@endcomponent