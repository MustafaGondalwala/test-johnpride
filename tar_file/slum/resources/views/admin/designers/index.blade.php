@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Designers - {{ config('app.name') }}
    @endslot


    <?php
    $BackUrl = CustomHelper::BackUrl();

    $old_name = app('request')->input('name');
    $old_email = app('request')->input('email');
    $old_phone = app('request')->input('phone');
    $old_reff_code = app('request')->input('reff_code');
    $old_status = app('request')->input('status');

    $print_comm_scope = app('request')->input('print_comm_scope');
    $old_print_comm = app('request')->input('print_comm');

    $reff_comm_scope = app('request')->input('reff_comm_scope');
    $old_reff_comm = app('request')->input('reff_comm');

    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');

    $compare_scope = config('custom.compare_scope');

    $back_url = (request()->has('back_url'))?request('back_url'):'';
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">
            <h1 class="pull-left">Designers ({{count($designers)}})</h1>

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
        <div class="bgcolor">

            <div class="table-responsive">

                <form class="form-inline" method="GET">
                    <div class="col-md-2">
                        <label>Name:</label><br/>
                        <input type="text" name="name" class="form-control admin_input1" value="{{$old_name}}">
                    </div>

                    <div class="col-md-2">
                        <label>Email:</label><br/>
                        <input type="email" name="email" class="form-control admin_input1" value="{{$old_email}}">
                    </div>

                    <div class="col-md-2">
                        <label>Phone:</label><br/>
                        <input type="text" name="phone" class="form-control admin_input1" value="{{$old_phone}}">
                    </div>

                    <div class="col-md-2">
                        <label>Referral Code:</label><br/>
                        <input type="text" name="reff_code" class="form-control admin_input1" value="{{$old_reff_code}}">
                    </div>


                    <div class="col-md-2 checklabel1">
                        <label>Printing Comm. (%):</label><br/>

                        <select name="print_comm_scope" class="form-control select_qty1 ">

                            <?php
                            foreach($compare_scope as $scpKey=>$scpVal){
                                $selected = '';
                                if($scpKey == $print_comm_scope){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $scpKey }}" {{ $selected }}>{{ $scpVal }}</option>
                                <?php
                            }
                            ?>
                        </select>

                        <input type="number" name="print_comm" class="form-control select_qty2 " value="{{$old_print_comm}}" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>

                    </div>


                    <div class="col-md-2 checklabel1">
                        <label>Referral Comm. (%):</label><br/>

                        <select name="reff_comm_scope" class="form-control select_qty1 ">

                            <?php
                            foreach($compare_scope as $scpKey=>$scpVal){
                                $selected = '';
                                if($scpKey == $reff_comm_scope){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{ $scpKey }}" {{ $selected }}>{{ $scpVal }}</option>
                                <?php
                            }
                            ?>
                        </select>

                        <input type="number" name="reff_comm" class="form-control select_qty2 " value="{{$old_reff_comm}}" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>

                    </div>

                    <div class="clearfix"></div>

                    <div class="col-md-2">
                        <label>Status:</label><br/>
                        <select name="status" class="form-control admin_input1">
                            <option value="">--Select--</option>
                            <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                            <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                        </select>
                    </div>


                    <div class="col-md-2">
                        <label>From Date:</label><br/>
                        <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}">
                    </div>

                    <div class="col-md-2">
                        <label>To Date:</label><br/>
                        <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}">
                    </div>

                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success btn1search">Search</button>
                        <a href="{{url('admin/designers')}}" class="btn resetbtn btn-primary btn1search">Reset</a>
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
                                
                                <?php
                                /*
                                &nbsp;
                                <a href="{{ route('admin.designers.designs', [$designer_id, 'back_url'=>$BackUrl]) }}" title="Manage Designs" ><i class="far fa-object-group"></i></a>
                                
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
    /*$('#from, #payment_date, #to').datetimepicker({
      dateFormat:'dd/mm/yy',
      showTimepicker: false
    });*/
	
	 $(document).on("click", ".searchbtn", function(){
        $('.searchshow').fadeToggle();
    });
</script>