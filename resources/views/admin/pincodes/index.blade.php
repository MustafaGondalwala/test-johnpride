@component('admin.layouts.main')

@slot('title')
Admin - Manage Pincodes - {{ config('app.name') }}
@endslot

<?php

$id = 0;

$id = (isset($pincodeRow->id))?$pincodeRow->id:0;
$state_id = (isset($pincodeRow->state_id))?$pincodeRow->state_id:0;
$city_id = (isset($pincodeRow->city_id))?$pincodeRow->city_id:0;
$pin = (isset($pincodeRow->pin))?$pincodeRow->pin:'';
$cod_amount = (isset($pincodeRow->cod_amount))?$pincodeRow->cod_amount:'';
$zone = (isset($pincodeRow->zone))?$pincodeRow->zone:'';
$status = (isset($pincodeRow->status))?$pincodeRow->status:1;

$field1 = (isset($pincodeRow->field1))?$pincodeRow->field1:'';
$field2 = (isset($pincodeRow->field2))?$pincodeRow->field2:'';
$field3 = (isset($pincodeRow->field3))?$pincodeRow->field3:'';
$cod_available = (isset($pincodeRow->cod_available))?$pincodeRow->cod_available:0;


$action_url = url('admin/pincodes');

$name_readonly = '';

$form_heading = 'Add Pincode';

if(is_numeric($id) && $id > 0){
    $action_url = url('admin/pincodes', $id);
    $form_heading = 'Update Pincode';
}

?>


<div class="row pincodes_pages">

    <div class="col-md-12">
        <div class="titlehead">
            <h1 class="pull-left">Pincodes</h1>

            <?php
            if( !empty($pincodes) && $pincodes->count() > 0){
                ?>

                <form name="exportForm" method="" action="" >
                    {{ csrf_field() }}
                    <input type="hidden" name="export_xls" value="1">

                    <?php
                    if(count(request()->input())){
                        foreach(request()->input() as $input_name=>$input_val){
                            ?>
                            <input type="hidden" name="{{$input_name}}" value="{{$input_val}}">
                            <?php
                        }
                    }
                    ?>

                    <button class="btn btn-info pull-right" ><i class="fa fa-table"></i> Export XLS</button>
                </form>
                <?php
            }
            ?>

            <a href="{{ url('admin/pincodes/import') }}" class="btn btn-info pull-right" ><i class="fa fa-table"></i> Import</a>

        </div>

        @include('snippets.errors')
        @include('snippets.flash')

    </div>

    <div class="col-md-12">
        <div class="topsearch">

            <h4>{{ $form_heading }}</h4>
            <br>
            <form method="POST" action="{{ $action_url }}" accept-charset="UTF-8" role="form" class="form-inline heightform" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group col-sm-3 {{ $errors->has('state_id') ? ' has-error' : '' }} ">
                    <label for="state_id" class="control-label required">State:</label>
                    <select name="state_id" id="state_id" class="form-control">
                        <option value="" >--Select--</option>
                        <?php 
                        if(!empty($state) && count($state) > 0){
                            foreach ($state as $st){
                                $selected = '';
                                if($st->id == $state_id){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="{{$st->id}}" {{$selected}}>{{ $st->name }}</option>
                                <?php 
                            }
                        }
                        ?>

                    </select>

                    @include('snippets.errors_first', ['param' => 'state_id'])
                </div>

                <div class="form-group col-sm-3{{ $errors->has('city_id') ? ' has-error' : '' }} ">
                    <label for="city_id" class="control-label required">City:</label>

                    <select name="city_id" id="city_id" class="form-control">
                        <option value="" >--Select--</option>

                    </select>

                    @include('snippets.errors_first', ['param' => 'city_id'])
                </div>

                <div class="form-group col-sm-3{{ $errors->has('pin') ? ' has-error' : '' }} ">
                    <label for="pin" class="control-label required">Pin:</label>

                    <input type="text" name="pin" id="pin" class="form-control" value="{{ old('pin', $pin) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'pin'])
                </div>

                <div class="form-group col-sm-3{{ $errors->has('cod_amount') ? ' has-error' : '' }} ">
                    <label for="cod_amount" class="control-label">COD Amount:</label>

                    <input type="text" name="cod_amount" id="cod_amount" class="form-control" value="{{ old('cod_amount', $cod_amount) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'cod_amount'])
                </div>


                <div class="form-group col-sm-3{{ $errors->has('zone') ? ' has-error' : '' }} ">
                    <label for="zone" class="control-label">Zone:</label>

                    <input type="text" name="zone" id="zone" class="form-control" value="{{ old('zone', $zone) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'zone'])
                </div>

                <div class="clearfix">&nbsp;</div>

                 <div class="form-group col-sm-3{{ $errors->has('field1') ? ' has-error' : '' }} ">
                    <label for="field1" class="control-label">Field 1:</label>

                    <input type="text" name="field1" id="field1" class="form-control" value="{{ old('field1', $field1) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'field1'])
                </div>

                <div class="form-group col-sm-3{{ $errors->has('field2') ? ' has-error' : '' }} ">
                    <label for="field2" class="control-label">Field 2:</label>

                    <input type="text" name="field2" id="field2" class="form-control" value="{{ old('field2', $field2) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'field2'])
                </div>

                <div class="form-group col-sm-3{{ $errors->has('field3') ? ' has-error' : '' }} ">
                    <label for="field3" class="control-label">Field 3:</label>

                    <input type="text" name="field3" id="field3" class="form-control" value="{{ old('field3', $field3) }}" maxlength="255" />

                    @include('snippets.errors_first', ['param' => 'field3'])
                </div>

                <div class="form-group col-sm-4{{ $errors->has('status') ? ' has-error' : '' }}">
                    <label class="control-label">Status:</label>
                    &nbsp;&nbsp;
                    Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                    &nbsp;
                    Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                    @include('snippets.errors_first', ['param' => 'status'])
                </div>

                <div class="col-sm-12 col-md-3">

                    <div class="form-group{{ $errors->has('cod_available') ? ' has-error' : '' }}">
                        <label class="control-label ">COD Available:</label>

                        <input type="checkbox" name="cod_available" value="1" <?php echo ($cod_available == '1')?'checked':''; ?> />

                        @include('snippets.errors_first', ['param' => 'cod_available'])
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="form-group col-sm-3">
                <input type="hidden" name="id" value="{{ $id }}">

                <button class="btn btn-success btn1search"><i class="fa fa-save"></i> Save</button>
            
                <?php
                if($id > 0){
                    ?>
                    <a href="{{ url('admin/pincodes') }}" class="btn resetbtn btn-success btn1search" title="Cancel">Cancel</a>
                    <?php
                }
                ?>
                </div>
            </form>

        </div>
    </div>


    <div class="col-md-12">


        <?php
        if(!empty($pincodes) && count($pincodes) > 0){
            ?>
            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Pincode</th>
                    <th>Amount</th>
                    <th>Zone</th>
                    <th>COD Available</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                foreach($pincodes as $pincode){

                    $stateName = '';
                    $cityName = '';

                    if(!empty($pincode->pincodeState) && count($pincode->pincodeState) > 0){
                        $stateName = $pincode->pincodeState->name;
                    }

                    if(!empty($pincode->pincodeCity) && count($pincode->pincodeCity) > 0){
                        $cityName = $pincode->pincodeCity->name;
                    }

                    $codAvailable = '';
                    if($pincode->cod_available == 1){
                        $codAvailable = '<span style="color:green">'.'Yes'.'</span>';
                    }
                    elseif($pincode->cod_available == 0){
                        $codAvailable = '<span style="color:red">'.'No'.'</span>';
                    }

                    ?>

                    <tr>
                        <td>{{$pincode->id}}</td>
                        <td>{{$stateName}}</td>
                        <td>{{$cityName}}</td>
                        <td>{{$pincode->pin}}</td>
                        <td>{{$pincode->cod_amount}}</td>
                        <td>{{$pincode->zone}}</td>
                        <td><?php echo $codAvailable; ?></td>
                        <td>{{ CustomHelper::getStatusStr($pincode->status) }}</td>

                        <td>
                            <a href="{{ url('admin/pincodes', $pincode->id) }}" class=""><i class="fas fa-edit"></i></a>

                            <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$pincode->id}}"><i class="fas fa-trash-alt"></i></i></a>

                            <form method="POST" action="{{ route('admin.pincodes.delete', $pincode->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this record?');" id="delete-form-{{$pincode->id}}">
                                {{ csrf_field() }}
                                {{ method_field('POST') }}
                                <input type="hidden" name="id" value="<?php echo $pincode->id; ?>">
                            </form>

                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            {{ $pincodes->appends(request()->query())->links() }}
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no Pincode at the present.</div>
            <?php
        }
        ?>
    </div>

</div>

@slot('bottomBlock')

<script type="text/javaScript">

    var state_id = '{{ $state_id }}';
    var city_id = '{{ $city_id }}';

    load_cities( state_id, city_id );

    $(document).on("change", "select[name='state_id']", function () {
        state_id = $( this ).val();
        load_cities( state_id, city_id );
    } );

    function load_cities( state_id, city_id ) {

        var _token = '{{csrf_token()}}';

        $.ajax( {
            url: "{{url('common/ajax_load_cities')}}",
            type: "POST",
            data: {state_id: state_id, city_id: city_id},
            dataType: "JSON",
            headers: {
                'X-CSRF-TOKEN': _token
            },
            cache: false,
            beforeSend: function () {},
            success: function ( resp ) {
                if ( resp.success ) {
                    $("select[name='city_id']").html( resp.options );
                }
            }
        } );
    }

    $('.sbmtDelForm').click(function(){
        var id = $(this).attr('id');
        $("#delete-form-"+id).submit();
    });


</script>

@endslot

@endcomponent