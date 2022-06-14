@component('layouts.admin')

    @slot('title')
        Admin Panel - {{ config('app.name') }}
    @endslot

    <div class="row">
        <div class="col-md-12">
           <div class="titlehead">
				<h1 class="pull-left">Permissions</h1>
            <?php
            /*
            <a href="{{url('admin/permissions/add')}}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Permission</a>
            */
            ?>
        </div>
         </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(session('msg_success'))
            <div class="alert dark alert-icon alert-success alert-dismissible alertDismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <i class="icon wb-check" aria-hidden="true"></i> 
            {{session('msg_success')}}
        </div>
        @endif
        @if(session('msg_update'))
        <div class="alert dark alert-icon alert-info alert-dismissible alertDismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <i class="icon wb-check" aria-hidden="true"></i> 
        {{session('msg_update')}}
    </div>
    @endif
    @if(session('msg_delete'))
    <div class="alert dark alert-icon alert-danger alert-dismissible alertDismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
    <i class="icon wb-check" aria-hidden="true"></i> 
    {{session('msg_delete')}}
</div>
@endif
</div>
</div>


    <div class="row">


        <div class="col-md-12">

        <form action="{{url('admin/permissions/save')}}" method="post">

{{ csrf_field() }}

        <table class="table table-striped table-bordered table-hover" data-tablesaw-mode="columntoggle" data-tablesaw-minimap="" id="table-3973">
<thead>
    <tr>
    <th data-tablesaw-priority="4" class="tablesaw-priority-4 tablesaw-cell-visible">Name</th>
    <?php $i = 5; ?>
    @foreach ($roles as $role)
        <th class="text-center" data-tablesaw-priority="{{$i++}}"> <input type="checkbox" name='check_all' id="check_all_{{ $role->id }}" onclick="checkall(this.id, {{ $role->id }})" style='float:left;' >{{ $role->display_name }}</th>
    @endforeach
    <th class="text-center" data-tablesaw-priority="1">Action</th>
    </tr></thead>
<tbody id="permList">
<?php $j = 5; ?>
@foreach($permission as $view)
<?php
if($view->children->count()){
//pr($view->children->toArray());
}
?>
  <tr class="row_{{$view->id}} permRow">
    <td class="tablesaw-priority-4 tablesaw-cell-visible">{{ $view->display_name }}</td>
     @foreach ($roles as $role)
        <td class="text-center" class="tablesaw-priority-{{$j++}}">
        <div class="checkbox-custom checkbox-inline checkbox-primary pull-left">
            <?php
            /*
             {!! Form::checkbox("roles[{$role->id}][]", $view->id, $role->hasPermission($view->name)) !!}
            */
            ?>

            <input type="hidden" name="role_ids[]" value="{{ $role->id }}">

             <input name="roles[{{ $role->id }}][]" type="checkbox" class="check_{{ $role->id }}" value="{{ $view->id }}" {{ ($role->hasPermission($view->name))?'checked':'' }}>
              <label for="inputCheckbox"></label>
            </div>
            
        </td>
    @endforeach
    
    <td class="text-center" class="tablesaw-priority-1">

    

        <!-- <a href="/admin/permissions/{{$view->id}}" class="btn btn-icon btn-info btn-outline btn-round" title="{{ trans('app.edit')}}" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('app.edit')}}">
            <i class="icon wb-pencil" aria-hidden="true"></i>
        </a> -->
         <!-- <button data-placement="top" data-original-title="{{ trans('app.delete')}}" rel="tooltip" title="{{ trans('app.delete')}}"  class="btn btn-sm btn-danger" data-target=".exampleNiftyFlipVertical" data-toggle="modal" type="button" data-href="{{URL::to('PermissionController/destroy')}}/{{$view->id}}"><i class="icon fa-remove" aria-hidden="true"></i></button> -->

         <?php
         /*
         <a href="{{url('admin/permissions/edit/'.$view->id.'?back_url='.$back_url)}}" class="btn btn-sm btn-primary tooltip_title" title="Edit"><i class="far fa-edit"></i></a>

         <a href="javascript:void(0)" class="btn btn-sm btn-danger tooltip_title" onclick="delete_permission({{$view->id}})" title="Delete"><i class="fa fa-times"></i> </a>
         */
         ?>
        
      </td>
   </tr>

   <?php
   if($view->children->count()){

    foreach($view->children as $view2){
        ?>
        <tr class="row_{{$view2->id}} permRow">
            <td class="tablesaw-priority-4 tablesaw-cell-visible">&nbsp;&gt;&nbsp;{{ $view2->display_name }}</td>
            @foreach ($roles as $role)
            <td class="text-center" class="tablesaw-priority-{{$j++}}">
                <div class="checkbox-custom checkbox-inline checkbox-primary pull-left">

                    <input type="hidden" name="role_ids[]" value="{{ $role->id }}">

                    <input name="roles[{{ $role->id }}][]" type="checkbox" class="check_{{ $role->id }}" value="{{ $view2->id }}" {{ ($role->hasPermission($view2->name))?'checked':'' }}>
                    <label for="inputCheckbox"></label>
                </div>

            </td>
            @endforeach

            <?php
            /*
            <td class="text-center" class="tablesaw-priority-1">

               <a href="{{url('admin/permissions/edit/'.$view2->id.'?back_url='.$back_url)}}" class="btn btn-sm btn-primary tooltip_title" title="Edit"><i class="far fa-edit"></i></a>

               <a href="javascript:void(0)" class="btn btn-sm btn-danger tooltip_title" onclick="delete_permission({{$view2->id}})" title="Delete"><i class="fa fa-times"></i> </a>

           </td>
            */
            ?>

            
       </tr>
       <?php
   }
}
?>
    @endforeach      
</tbody>
</table>
<button type="submit" class="btn btn-success perm_save_btn"><i class="fa fa-save"></i> Save</button>
<br>
<br>
</form>
            

        </div>


    </div>

    <script type="text/javascript">

        /*function delete_permission(id){
            conf = confirm("Are you sure to delete this Permission?");

            if(conf){
                //window.location = '/admin/permissions/destroy/'+id;

                
            }
            else{
                return false;
            }
        }*/

        function checkall(check_id, role_id=0){
            //alert(check_id);
            if(!isNaN(role_id) && role_id > 0){
                var chk = document.getElementById(check_id);
                if (chk.checked) 
                {
                    $(".permRow:visible").find(".check_"+role_id).prop('checked', true);
                }
                else {
                    $(".permRow:visible").find(".check_"+role_id).prop('checked', false);
                }
            }
        }

        function delete_permission(id){
            conf = confirm("Are you sure to delete this Permission?");
            if(conf){
                window.location = "{{url('admin/permissions/delete')}}/"+id;
                /*_token = '{{csrf_token()}}';

                $.ajax({
                    url:'permissions/ajax_delete_permission',
                    type:"POST",
                    data:{id:id},
                    headers:{'X-CSRF-TOKEN': _token},
                    dataType:"JSON",
                    beforeSend:function(){},
                    success:function(resp){
                        console.log(resp);

                        if(resp.success){
                            $(".row_"+id).remove();
                        }
                    }
                });*/
            }
        }
    </script>


@endcomponent

<script type="text/javascript">
  $(".tooltip_title").tooltip();
</script>