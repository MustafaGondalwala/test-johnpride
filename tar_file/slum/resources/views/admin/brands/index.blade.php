@component('admin.layouts.main')

@slot('title')
Admin - Manage Brands - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">

    <div class="col-md-12">

        <h2>Manage Brands
            <a href="{{ route('admin.brands.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Brand</a>
        </h2>

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        if(!empty($brands) && count($brands) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>No. of Product</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($brands as $brand){
                        ?>
                        
                        <tr>
                            <td><?php echo $brand->name; ?></td>
                            <td><?php echo $brand->countProducts(); ?></td>
                            <td>{{ CustomHelper::getStatusStr($brand->status) }}</td>

                            <td>
                                <a href="{{ route('admin.brands.edit', $brand->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>  

                                 <?php if($brand->countProducts() == 0) { ?>                                 
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$brand->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                            <form method="POST" action="{{ route('admin.brands.delete', $brand->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this brand?');" id="delete-form-{{$brand->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="brand_id" value="<?php echo $brand->id; ?>">

                                </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $brands->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No Brands found.</div>
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