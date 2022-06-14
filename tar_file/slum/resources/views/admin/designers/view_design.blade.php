@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Designers - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();
    $back_url = (request()->has('back_url'))?request('back_url'):'';

     $storage = Storage::disk('public');
     $path = 'customer_designs/thumb/';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Designs</h1>
 

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


     @include('snippets.errors')
     @include('snippets.flash')

<div class="row">
    <div class="col-md-12">

        <?php
        if(!empty($designData) && $designData->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $designData->appends(request()->query())->links() }}

                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Images</th>
                        <th>Added on</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($designData as $design){
                        
                       $image_name = $design['getDesignImage']['name'];
                       /* $first_name = (isset($designer->first_name))?$designer->first_name:'';
                        $last_name = (isset($designer->last_name))?$designer->last_name:'';
                        $designer_name = trim($first_name.' '.$last_name);*/

                        $status = ($design->status == '1')?'Active':'Inactive';

                        $added_on = CustomHelper::DateFormat($design->created_at, 'd F y');

                        $design_id = $design['id'];
                        ?>

                        <tr>
                            <td>{{$design->name}}</td>
                            <td>{{$status}}</td>
                            <td>
                             <?php 
                             if(!empty($image_name) && $storage->exists($path.$image_name))
                            { 
                             ?>
                             <img src="{{ url('public/storage/'.$path.$image_name) }}" style="width: 50px; height:  50px;">
                             <?php } ?>

                            </td>
                            <td>{{$added_on}}</td>
                            <td><a href="{{ route('admin.designers.edit_design', [$design->id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $designData->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no designe at the present.</div>
            <?php
        }
        ?>

    

<br /><br />

</div>
</div>

@endcomponent



<script style="text/javaScript">
    /*$('#from, #payment_date, #to').datetimepicker({
      dateFormat:'dd/mm/yy',
      showTimepicker: false
    });*/
	
	 $(document).on("click", ".searchbtn", function(){
        $('.searchshow').fadeToggle();
    });
</script>