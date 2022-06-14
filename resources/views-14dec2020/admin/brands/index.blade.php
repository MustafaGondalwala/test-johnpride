@component('admin.layouts.main')

@slot('title')
Admin - Manage Collections - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>


<div class="row">
        <div class="col-md-12">
            <div class="titlehead">

            <h1 class="pull-left">Manage Collections</h1>
            <a href="{{ route('admin.brands.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Collections</a>
           

            <?php
            if(!empty($brands) && $brands->count() > 0){
                ?>
                <form name="exportForm" method="" action="" >
                    <input type="hidden" name="export_xls" value="1">  
                    <button type="submit" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export XLS</button>
                </form>
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
        if(!empty($brands) && count($brands) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>No. of Product</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($brands as $brand){
                        ?>
                        
                        <tr>
                            <td>{{$brand->id}}</td>
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
            <div class="alert alert-warning">No Collections found.</div>
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