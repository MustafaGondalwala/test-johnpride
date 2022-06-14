@component('admin.layouts.main')

@slot('title')
Admin - Manage Home Images - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">

    <div class="col-md-12">

        <h2>Manage Home Images
            <a href="{{ route('admin.home_images.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> New Home Image</a>
        </h2>

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        
        if(!empty($homeImages) && count($homeImages) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($homeImages as $homeImage){

                        $storage = Storage::disk('public');
                        $path = 'home_images/thumb/';
                        ?>
                        
                        <tr>
                            <td><?php echo $homeImage->title; ?></td>
                            <td>
                                <?php
                                $image = $homeImage->image;
                                if(!empty($image)){
                                if($storage->exists($path.$image))
                                { ?>
                                    <div class="col-md-2 image_box">
                                        <a target="_blank" href="{{ url('storage/'.$path.'/'.$image) }}">
                                           <img src="{{ url('storage/'.$path.'/'.$image) }}" style="width: 50px;"><br>
                                       </a>
                                   </div>
                                <?php }
                                }
                                ?>
                            </td>
                            <td>{{ CustomHelper::getStatusStr($homeImage->status) }}</td>

                            <td>
                                <a href="{{ route('admin.home_images.edit', $homeImage->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>
                               
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$homeImage->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                                <form method="POST" action="{{ route('admin.home_images.delete', $homeImage->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Home Image?');" id="delete-form-{{$homeImage->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="homeImage_id" value="<?php echo $homeImage->id; ?>">

                                </form>
                                
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $homeImages->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No Home Image found.</div>
            <?php
        }
        ?>

    </div>

</div>

@endcomponent


<script type="text/javaScript">
    $('.sbmtDelForm').click(function(){
        var id = $(this).attr('id');
        $("#delete-form-"+id).submit();
    });
</script>