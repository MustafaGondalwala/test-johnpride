    @component('layouts.admin')

    @slot('title')
        Admin - User Activities - {{ config('app.name') }}
    @endslot

    <div class="row">
        <div class="col-md-12">
            <h1>User Activities</h1>
		</div>        
   </div>
   
    <div class="row">
        <div class="col-md-12">
            
            <div class="topsearch">


            <?php
            $old_name = app('request')->input('name');
            $old_email = app('request')->input('email');
            $old_phone = app('request')->input('phone');
            $old_city = app('request')->input('city');
            $old_state = app('request')->input('state');
            $old_brand = app('request')->input('brand');
            $old_status = app('request')->input('status');

            $old_from = app('request')->input('from');
            $old_to = app('request')->input('to');
            ?>

            <form class="form-inline" method="GET">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" class="form-control" value="{{$old_name}}">
                </div>

                <div class="form-group">
                    <label for="order">From Date:</label>
                    <input type="text" name="from" class="form-control to_date" value="{{ $old_from }}">
                </div>


                <div class="form-group">
                    <label for="business">To Date:</label>
                    <input type="text" name="to" class="form-control from_date" value="{{ $old_to }}">
                </div>

                <button type="submit" class="btn btn-success">Search</button>
                <a href="{{ url('admin/usersactivity') }}" class="btn btn-primary">Reset</a>
                <?php
                /*
                <a href="{{url('admin/'.$segment2)}}" class="btn resetbtn btn-primary">Reset</a>
                */
                ?>
            </form>

            </div>
            </div>
    </div>

    <div class="row">

        <div class="col-md-12">


                    <div class="table-responsive">
                    {{ $UsersActivity->appends(request()->query())->links() }}
            
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <th class="text-center">Module</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">Record ID</th>
                            <th class="text-center">By</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Date</th>
                        </tr>

                        <?php
                        if(!empty($UsersActivity) && count($UsersActivity) > 0){
                            foreach($UsersActivity as $UA){

                                $UA_admin = getUsers($UA->user_id);
                                $UA_admin_first_name = (isset($UA_admin->first_name))?$UA_admin->first_name:'';
                                $UA_admin_last_name = (isset($UA_admin->last_name))?$UA_admin->last_name:'';

                                $UA_admin_name = trim($UA_admin_first_name.' '.$UA_admin_last_name);
                                //pr($oa_admin_first_name);
                                ?>
                                <tr>
                                    <td>{{ $UA->module_name }}</td>
                                    <td>{{ $UA->module_action }}</td>
                                    <td>{{ $UA->tbl_id }}</td>
                                    <td>{{ $UA_admin_name }}</td>
                                    <td>{{ $UA->description }}</td>
                                    <td>
                                    <?php
                                    echo date_format2($UA->created_at, 'd M Y');
                                    ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>

                    {{ $UsersActivity->appends(request()->query())->links() }}

                </div>


			
			</div>
        
   </div>

@endcomponent

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });
    });
</script>


