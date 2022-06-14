@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Designer's Design - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Designs ({{count($designs)}})</h1>

            <a href="{{ route('admin.designers.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Designer</a>
            

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
        if(!empty($designers) && $designers->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $designers->appends(request()->query())->links() }}

                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Printing Comm. (%)</th>
                        <th>Referral Code</th>
                        <th>Referral Comm. (%)</th>
                        <th>Status</th>
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($designers as $designer){
                        $first_name = (isset($designer->first_name))?$designer->first_name:'';
                        $last_name = (isset($designer->last_name))?$designer->last_name:'';
                        $designer_name = trim($first_name.' '.$last_name);

                        $status = ($designer->status == '1')?'Active':'Inactive';

                        $added_on = CustomHelper::DateFormat($designer->created_at, 'd F y');

                        $designer_id = $designer['id'];
                        ?>

                        <tr>
                            <td>{{$designer_name}}</td>
                            <td>{{$designer->email}}</td>
                            <td>{{$designer->phone}}</td>
                            <td>{{$designer->printing_commission}}</td>
                            <td>{{$designer->referral_code}}</td>
                            <td>{{$designer->referral_commission}}</td>
                            <td>{{$status}}</td>
                            <td>{{$added_on}}</td>

                            <td>
                                <a href="{{ route('admin.designers.edit', [$designer_id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;
                                <a href="{{ route('admin.designers.designs', [$designer_id, 'back_url'=>$BackUrl]) }}" title="Manage Designs" ><i class="far fa-object-group"></i></a>
                                <?php
                                /*
                                &nbsp;
                                <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                <form method="POST" action="{{ route('admin.designers.delete', $designer_id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this designer?');" class="delForm">
                                    {{ csrf_field() }}
                                </form>
                                */
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $designers->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no designers at the present.</div>
            <?php
        }
        ?>

    

<br /><br />

</div>
</div>

@endcomponent



<script style="text/javaScript">
    $('#from, #payment_date, #to').datetimepicker({
      dateFormat:'dd/mm/yy',
      showTimepicker: false
    });
	
	 $(document).on("click", ".searchbtn", function(){
        $('.searchshow').fadeToggle();
    });
</script>