	@component('admin.layouts.main')

    @slot('title')
        Admin - Manage States - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = (request()->has('name'))?request()->name:'';
    $old_state = (request()->has('state'))?request()->state:'';
    
    ?>
    
    <div class="row">
        <div class="col-md-12">
			<div class="titlehead">
			<h1 class="pull-left">City List ({{ $cities->count() }})</h1>

            <a href="{{ url('admin/cities/save') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add City</a>
            

			</div>
		</div>
   </div>

      <div class="row">

    <div class="col-md-12">
        <div class="bgcolor">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-2">
                        <label>City Name:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>


                    <div class="col-md-3">
                        <label>States:</label><br/>
                        <select name="state" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <?php
                            if(!empty($states) && count($states) > 0){

                                foreach($states as $state){

                                    $selected = '';
                                    if($state->id == $old_state){
                                        $selected = 'selected';
                                    }

                                    ?>
                                    <option value="{{$state->id}}" {{$selected}} >{{$state->name}}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                   <!--  <div class="clearfix"></div> -->
                    

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/cities')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>


    <div class="row">

        <div class="col-md-12">

            @include('snippets.errors')
            @include('snippets.flash')

        <?php

        if(!empty($cities) && $cities->count() > 0){
            ?>
            <div class="table-responsive">

            {{ $cities->appends(request()->query())->links() }}

                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th class="">Name</th>
                        <th class="">State</th>
                        <th class="">Country</th>
                        <th class="">Status</th>
                        <th class="">Action</th>
                    </tr>
                    <?php
                    $storage = Storage::disk('public');
                    foreach ($cities as $city){

                        //prd($city->cityState->toArray());

                        $cityState = (isset($city->cityState))?$city->cityState:'';

                        $stateName = (isset($cityState->name))?$cityState->name:'';

                        $country = (isset($cityState->country))?$cityState->country:'';
                           
                        $countryName = (isset($country->name))?$country->name:'';

                        ?>

                        <tr>
                            <td>{{$city->name}}</td>
                            <td>{{$stateName}}</td>
                            <td>{{$countryName}}</td>
                            <td><?php  echo ($city->status==1)?'Active':'Inactive';  ?></td>
                            
                            
                            

                            <td>
                                <a href="{{route('admin.cities.save', ['id'=>$city->id,'back_url'=>$BackUrl])}}" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            {{ $cities->appends(request()->query())->links() }}
            <?php
                    }
                    else{
                ?>
                <div class="alert alert-warning">There are no Records at the present.</div>
                <?php
            }
            ?>
            </div>

        </div>

   

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(".tooltip_title").tooltip();

    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            dateFormat:'dd/mm/yy',
            changeMonth:true,
            changeYear: true,
            yearRange:"1950:0+"
        });
    });

    $(document).on("click", ".product_status", function(){

        var curr_sel = $(this);

        var product_id = $(this).attr('data-id');
        var curr_status = $(this).attr('data-status');
        
        var conf = confirm("Are you sure to change status of this Product?");
        
        if(conf){

            _token = '{{csrf_token()}}';
            
            $.ajax({
                url: "{{ url('admin/products/ajax_change_status') }}",
                method: 'POST',
                data:{product_id, curr_status},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp.success == true){
                        curr_sel.parent().html(resp.status_html);
                        //curr_sel.remove();
                    } else {

                    }
                },
                error: function(resp) {

                }
            });
        }
    });
    
</script>