@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Loyalty Points Master List - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();


    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';


    $page_title = 'Loyalty Points Master';
    $parent_cat_link = 'javascript:void(0)';
    $add_col_btn_name = 'Add Loyalty Points';
    $facilities_arr = config('custom.facilities_arr');

    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">

            <h1 class="pull-left"><?php echo $page_title; ?></h1>
            <?php /* ?>
            <a href="{{ route('admin.loyaltypoints.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> {{$add_col_btn_name}}</a>
            <?php */ ?>



            

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
            if(!empty($loyaltyPointsMaster) && $loyaltyPointsMaster->count() > 0){
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Name</th>
                            <th>Points needed Min</th>
                            <th>Points needed Max</th>
                            <th>Value of Points</th>
                            <!-- <th>Facilities</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($loyaltyPointsMaster as $lpm){

                            $status = ($lpm->status == "1")?'Active':'Inactive';

                            $facilities = (isset($lpm->facilities))? explode(',',$lpm->facilities):[];
                            ?>
                        <tr>
                           <!--  <td>{{$lpm->id}}</td> -->
                            <td>{{$lpm->name}}</td>
                            <td>{{$lpm->points_needed}}</td>
                            <td><?php if($lpm->points_needed_max > 0){echo $lpm->points_needed_max; }else{ echo 'No Limit'; } ?></td>
                            <td>{{$lpm->value_of_points}}</td>
                            <td>
                                <?php 
                                
                                $display_data_ar = [];
                                foreach($facilities_arr as $fc=>$fcv){ 
                                    
                                    if(is_array($facilities) && in_array($fc, $facilities))
                                    {
                                        $display_data_ar[] = $fcv;
                                    }
                                }

                                echo implode(', ', $display_data_ar);
                                ?>

                            </td>
                            <td>{{$status}}</td>
                            <td>
                                
                                
                                <a href="{{ route('admin.loyaltypoints.add',['','id'=>$lpm->id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;

                                <?php /* ?>
                                    <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                    <form method="POST" action="{{ route('admin.loyaltypoints.delete', $lpm->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Loyalty Points?');" class="delForm">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>

                                <?php */ ?>
                                   
                                
                            </td>
                        </tr>
                        <?php
                    }
                        ?>
                    </table>
                </div>
                {{ $loyaltyPointsMaster->appends(request()->query())->links() }}
                <?php
            }
            else{
                ?>
                <div class="alert alert-warning">There are no Loyalty Points  at the present.</div>
                <?php
            }
            ?>

        </div>

    </div>

 <!-- End - Product Modal -->

    @slot('bottomBlock')
        <script>
            function remove(url) {
                var r = confirm("Are you sure you want to remove this item?");
                if (r == true) {
                    window.location.replace(url);
                } else {
                    return false;
                }
            }

            $(document).on("click", ".sbmtDelForm", function(e){
                e.preventDefault();

                $(this).siblings("form.delForm").submit();                
            });            


        </script>
    @endslot

@endcomponent