@component('admin.layouts.main')

@slot('title')
Admin - Manage Banners - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">

    <div class="col-md-12">

        <h2>Manage Banners
            <a href="{{ route('admin.banners.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> New Banner</a>
        </h2>

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        if(!empty($banners) && count($banners) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>Title</th>
                        <th>Sub-title</th>  
                        <th>Page</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($banners as $banner){
                        ?>
                        
                        <tr>
                            <td><?php echo $banner->title; ?></td>
                            <td>{{ $banner->subtitle }}</td>
                            <td>{{ (isset($page_arr[$banner->page]))?$page_arr[$banner->page]:ucwords($banner->page) }}</td>
                            <td>{{ CustomHelper::getStatusStr($banner->status) }}</td>

                            <td>
                                <a href="{{ route('admin.banners.edit', $banner->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>                                    
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$banner->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                                <form method="POST" action="{{ route('admin.banners.delete', $banner->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Banner?');" id="delete-form-{{$banner->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="banner_id" value="<?php echo $banner->id; ?>">

                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $banners->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No banners found.</div>
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