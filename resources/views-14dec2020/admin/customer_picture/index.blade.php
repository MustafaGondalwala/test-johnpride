@component('admin.layouts.main')

@slot('title')
Admin - Manage Customer picture - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">
        <div class="col-md-12">
            <div class="titlehead">

            <h1 class="pull-left">Manage Customer Picture</h1>
            <a href="{{ route('admin.customer-picture.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add New Customer Picture</a>
           
            </div>
        </div>
   </div>

<div class="row">

    <div class="col-md-12">        

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        if(!empty($customerPictures) && count($customerPictures) > 0){
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
                    foreach($customerPictures as $customerPicture){
                        ?>
                        
                        <tr>
                            <td>{{$customerPicture->id}}</td>
                            <td><?php echo $customerPicture->title; ?></td>
                            <td><?php echo $customerPicture->product_sku; ?></td>
                            <td><?php echo ($customerPicture->product)?$customerPicture->product->name:''; ?></td>
                            <td>
                                <?php 
                                echo ($customerPicture->featured==1)?'Yes':'No'; 
                                ?>

                            </td>
                            <td>
                                {{ CustomHelper::getStatusStr($customerPicture->status) }}</td>

                            <td>
                                <a href="{{ route('admin.customer-picture.edit', $customerPicture->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>  

                                                               
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$customerPicture->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                                <form method="POST" action="{{ route('admin.customer-picture.delete', $customerPicture->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Customer Picture?');" id="delete-form-{{$customerPicture->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id" value="<?php echo $customerPicture->id; ?>">

                                </form>
                               
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $customerPictures->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No Customer Picture found.</div>
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