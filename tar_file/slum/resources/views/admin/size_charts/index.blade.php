@component('admin.layouts.main')

@slot('title')
Admin - Manage Size Charts - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">

    <div class="col-md-12">

        <h2>Manage Size Charts
            <a href="{{ route('admin.size_charts.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Size Chart</a>
        </h2>

        @include('snippets.errors')
        @include('snippets.flash')

        <?php
        if(!empty($sizeCharts) && count($sizeCharts) > 0){
            ?>

            <div class="table-responsive">

                <table class="table table-striped">
                    <tr>
                        <th>Title</th>
                        <th>No. of Product</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach($sizeCharts as $sizeChart){
                        
                        ?>
                        
                        <tr>
                            <td><?php echo $sizeChart->title; ?></td>
                            <td><?php echo $sizeChart->countProducts(); ?></td>
                            <td>{{ CustomHelper::getStatusStr($sizeChart->status) }}</td>

                            <td>
                                <a href="{{ route('admin.size_charts.edit', $sizeChart->id.'?back_url='.$BackUrl) }}"><i class="fas fa-edit"></i></a>

                                <?php if($sizeChart->countProducts() == 0) { ?>                                 
                                <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$sizeChart->id}}"><i class="fas fa-trash-alt"></i></i></a>
                                
                                <form method="POST" action="{{ route('admin.size_charts.delete', $sizeChart->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this sizeChart?');" id="delete-form-{{$sizeChart->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="sizeChart_id" value="<?php echo $sizeChart->id; ?>">

                                </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $sizeCharts->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No Size Chart found.</div>
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