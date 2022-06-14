@component('admin.layouts.main')

@slot('title')
Admin - Manage Look Book - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">
    <div class="col-md-12">
        <div class="titlehead">

        <h1 class="pull-left">Manage Look Book</h1>
        <a href="{{ route('admin.look-book.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add New Look Book</a>
       
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-12">        

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        if(!empty($lookBooks) && count($lookBooks) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Product SKU</th>
                        <th>Product Name</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($lookBooks as $lookBook){
                        ?>
                        
                        <tr>
                            <td>{{$lookBook->id}}</td>
                            <td><?php echo $lookBook->title; ?></td>
                            <td><?php echo $lookBook->product_sku; ?></td>
                            <td><?php echo ($lookBook->product)?$lookBook->product->name:''; ?></td>
                            <td>
                                <?php 
                                echo ($lookBook->featured==1)?'Yes':'No'; 
                                ?>

                            </td>
                            <td>
                                {{ CustomHelper::getStatusStr($lookBook->status) }}</td>

                            <td>
                                <a href="{{ route('admin.look-book.edit', $lookBook->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>  

                                                               
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$lookBook->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                                <form method="POST" action="{{ route('admin.look-book.delete', $lookBook->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Look Book?');" id="delete-form-{{$lookBook->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id" value="<?php echo $lookBook->id; ?>">

                                </form>
                               
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $lookBooks->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No Look Book found.</div>
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