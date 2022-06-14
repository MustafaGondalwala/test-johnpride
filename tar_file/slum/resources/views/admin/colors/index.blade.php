@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Colors - {{ config('app.name') }}
    @endslot

    <?php
    $BackUrl = CustomHelper::BackUrl();

    //$parent_id = (request()->has('parent_id'))?request()->parent_id:'';

    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    $parent_col_name = (isset($parentColor->name))?$parentColor->name:'';

    $page_title = 'Colors';

    $parent_cat_link = 'javascript:void(0)';

    if(!empty($parent_col_name)){
        //$parent_cat_url = url('admin/colors?parent_id='.$parentColor->parent_id);
        $parent_cat_url = url('admin/colors');

        //$page_title .= ' - <a href="'.$parent_cat_url.'" class="btn-link">'.$parent_col_name.'</a>';
        $page_title .= ' - '.$parent_col_name;
    }

    

    $add_col_btn_name = 'Add Color';

    /*if(is_numeric($parent_id) && $parent_id > 0){
        $add_col_btn_name = 'Add Sub Color';
    }*/
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="titlehead">

            <h1 class="pull-left"><?php echo $page_title; ?></h2>

            <a href="{{ route('admin.colors.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> {{$add_col_btn_name}}</a>

            

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
            if(!empty($colors) && $colors->count() > 0){
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th>Name</th>
                            <th>No. of Product</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($colors as $color){

                            $status = ($color->status == "1")?'Active':'Inactive';
                            ?>
                        <tr>
                            <td>{{$color->name}}</td>
                            <td>{{$color->countProducts()}}</td>
                            <td>{{$status}}</td>
                            <td>

                                <?php
                               /* if( empty($parent_id) ){
                                    ?>
                                    <a href="{{ route('admin.colors.add', ['', 'parent_id'=>$color->id, 'back_url'=>$BackUrl]) }}" title="Add child color" ><i class="fas fa-plus"></i></a>
                                    &nbsp;
                                    <?php
                                }*/
                                ?>
                                
                                <a href="{{ route('admin.colors.add',['','id'=>$color->id, 'back_url'=>$BackUrl]) }}" title="Edit" ><i class="fas fa-edit"></i></a>
                                &nbsp;

                                <?php

                                if($color->countProducts() == 0){
                                    ?>
                                    <a href="javascript:void(0)" class="sbmtDelForm" title="Delete" ><i class="fas fa-trash-alt"></i></a>

                                    <form method="POST" action="{{ route('admin.colors.delete', $color['id']) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this color?');" class="delForm">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>
                                    <?php
                                }
                                ?>
                                
                            </td>
                        </tr>
                        <?php
                    }
                        ?>
                    </table>
                </div>
                <?php
            }
            else{
                ?>
                <div class="alert alert-warning">There are no Color(s) at the present.</div>
                <?php
            }
            ?>

        </div>

    </div>

    <!-- Product Modal -->
<div id="viewModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        @permission('export_xls')
        <button type="submit" class="btn btn-sm export_btn" data-cid=""><i class="fa fa-table"></i> Export XLS</button>
        <br>
        @endpermission
        <div class="table-list"></div>
      </div>
      <div class="modal-footer">

        @permission('export_xls')
        <button type="submit" class="btn btn-sm export_btn" data-cid=""><i class="fa fa-table"></i> Export XLS</button>
        @endpermission

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
 <!-- End - Product Modal -->

    @slot('bottomBlock')
        <script>
            function remove(url) {
                var r = confirm("Are you sure you want to remove this category?");
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

            $(document).on("click", ".export_btn", function(e){
                e.preventDefault();

                 var category_id = $(this).attr("data-cid");

                 $(".liked_pro_"+category_id).submit();                
            });

            $(document).on("click", ".product_count", function(e){
                e.preventDefault();
                //$(this).parent("form").submit();

                var category_id = $(this).siblings("input[name='category_id']").val();
                var liked_products_ids = $(this).siblings("input[name='liked_products_ids']").val();

                var _token = '{{ csrf_token() }}';

                $.ajax({
                    url: "{{ url('admin/reports/liked_products') }}",
                    type: "POST",
                    data: {category_id:category_id, liked_products_ids:liked_products_ids},
                    dataType:"JSON",
                    headers:{'X-CSRF-TOKEN': _token},
                    cache: false,
                    beforeSend:function(){
                        $("#viewModal .export_btn").attr("data-cid", "");
                        $("#viewModal .modal-title").html("");
                        $("#viewModal .table-list").html("");
                    },
                    success: function(resp){
                        if(resp.success){
                            $("#viewModal .export_btn").attr("data-cid", category_id);
                            $("#viewModal .modal-title").html(resp.title);
                            $("#viewModal .table-list").html(resp.data);
                            $("#viewModal").modal();
                            //window.top.location.href = "{{ url('products/feeds') }}";
                        }
                    }
                });
            });
        </script>
    @endslot

@endcomponent