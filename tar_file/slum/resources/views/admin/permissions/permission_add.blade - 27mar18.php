@component('layouts.admin')

    @slot('title')
        Admin Panel - {{ config('app.name') }}
    @endslot

    <div class="row">
        <div class="col-md-12">
            <h1>Permissions</h1>
        </div>
    </div>

    <div class="row">


    	<div class="col-md-12">

    	<?php
    	if(isset($permissiondata->id) && $permissiondata->id > 0){
    		$form_action = url("admin/permissions/edit/$permissiondata->id");
    		$save_btn = 'Update Permission';
    	}
    	else{
    		$form_action = url('admin/permissions/add');
    		$save_btn = 'Add Permission';

    	}
    	?>
			<div class="row">
    		<form name="userForm" action="{{$form_action}}" method="post" novalidate>
    			{{ csrf_field() }}
    			<div class="col-md-6">
    			<div class="bgcolor">
    				<div class="form-group">
    					<label class="control-label">{{ trans('app.permission_name')}}</label>
    					<input type="text" required class="form-control" id="name" name="name" value="{{(isset($permissiondata->name))?$permissiondata->name:''}}">
    				</div>
    				<div class="form-group">
    					<label class="control-label">{{ trans('app.display_name')}}</label>
    					<input type="text" required  class="form-control" id="display_name" name="display_name" placeholder="display_name Name" value="{{(isset($permissiondata->display_name))?$permissiondata->display_name:''}}">
    				</div>
    				<div class="form-group">
    					<label class="control-label">{{ trans('app.description')}}</label>
    					<textarea name="description" id="description" class="form-control"> {{(isset($permissiondata->description))?$permissiondata->description:''}}</textarea>
    				</div>

    				<div class="bs-example" data-example-id="single-button-dropdown">
    					<button type="submit" ng-disabled="userForm.$invalid" class="btn btn-primary ladda-button" data-plugin="ladda" data-style="expand-left">
    						<span aria-hidden="true" class="glyphicon glyphicon-refresh"></span> {{$save_btn}} 
    						<span class="ladda-spinner"></span><div class="ladda-progress" style="width: 0px;"></div>
    					</button>
    					<br>
    					<br>
    				</div>
    			</div>	
				</div>
    		</form>

		</div>

    	</div>


    </div>

@endcomponent