@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Testimonials - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $back_url = (request()->has('back_url'))?request('back_url'):'';

    //prd(url()->current());
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Testimonials ({{count($testimonials)}})</h1>

            <a href="{{ route('admin.testimonials.add').'?back_url='.$BackUrl }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Testimonial</a>
            

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
        if(!empty($testimonials) && $testimonials->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $testimonials->appends(request()->query())->links() }}

                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Date on</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $payment_id = 0;
                    foreach($testimonials as $testimonial){
                        $name = (isset($testimonial->name))?$testimonial->name:'';

                        $description = (isset($testimonial->description))?$testimonial->description:'';

                        $description = strip_tags($description);

                        if(strlen($description) > 33){
                            $description = substr($description, 0, 30).'...';
                        }

                        $featured = ($testimonial->featured == '1')?'Active':'Inactive';
                        $status = ($testimonial->status == '1')?'Active':'Inactive';

                        $testimonial_id = $testimonial->id;

                        $date_on = CustomHelper::DateFormat($testimonial->date_on, 'd F y');
                        ?>

                        <tr>
                            <td>{{$testimonial->name}}</td>
                            <td>{{$testimonial->subject}}</td>
                            <td>{{$description}}</td>
                            <td>{{$date_on}}</td>
                            <td>{{$featured}}</td>
                            <td>{{$status}}</td>

                            <td>
                                <a href="{{ route('admin.testimonials.edit', [$testimonial_id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>

                                
                                
                                &nbsp;
                                <a href="javascript:void(0)" class="sbmtDelForm" data-id="{{$testimonial_id}}" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                <form name="deleteForm{{$testimonial_id}}" method="POST" action="{{ route('admin.testimonials.delete', $testimonial_id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to delete this testimonial?');" class="delForm">
                                    {{ csrf_field() }}
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $testimonials->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no testimonials at the present.</div>
            <?php
        }
        ?>

    

<br /><br />

</div>
</div>

@endcomponent



<script style="text/javaScript">
	
	 $(document).on("click", ".sbmtDelForm", function(){

        var testimonial_id = $(this).data("id");
        $("form[name='deleteForm"+testimonial_id+"']").submit();
    });
</script>