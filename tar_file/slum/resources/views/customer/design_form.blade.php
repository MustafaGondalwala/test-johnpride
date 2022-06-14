<!DOCTYPE html>
<html>
<head>

@include('common.head')


<link href="{{url('public')}}/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />

</head>

<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">Save/Update Design</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container">
 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')


<?php  

$name=(isset($res->name))?$res->name:'';
$price=(isset($res->price))?$res->price:0;
$status=(isset($res->status))?$res->status:1;
$featured=(isset($res->featured))?$res->featured:0;
$design_image=(isset($res->design_image))?$res->design_image:0;
$color=(isset($res->color))?$res->color:0;

?>
		
		<div class="sccountsec designform">

		<form method="post" enctype="multipart/form-data">
		 {{ csrf_field() }}
		<div class="row">
		
		<div class="col-md-6 form-group {{ $errors->has('name') ? ' has-error' : '' }}">
			<label>Design Name * : </label>

			<input type="text" name="name" class="form-control" value="{{ old('name', $name) }}"  >
			
		</div>

		<?php if(!empty($auth_user) && $auth_user->type == 'designer1') { ?>
		<div class="col-md-6 form-group {{ $errors->has('price') ? ' has-error' : '' }}">
			<label>Price : </label>

			<input type="text" name="price" class="form-control" value="{{ old('price', $price) }}"  >
			
		</div>
		<?php } ?>

		<div class="col-md-6 form-group {{ $errors->has('image') ? ' has-error' : '' }}">
			<label>Upload File * : </label>

		    <input class="form-control" type="file" id="image" name="image" >
                                 
		</div> 

		<?php /*   $CategoryDropDown = CustomHelper::CategoryDropDown($dropdown_name='category_id', $type='design', $classAttr='form-control', $idAtrr='category_id', $selected_cat_ids, true); ?>

		<div class="col-md-6 {{ $errors->has('image') ? ' has-error' : '' }}">
			<label>Category * : </label><br>

			<?php echo  $CategoryDropDown; ?>

		   
                                 
		</div>



		<?php */ ?>




		<div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('p1_cat') ? ' has-error' : '' }}">
                            <label class="control-label">Category:</label>

                            <select name="p1_cat" id="p1_cat" class="form-control" >

                                <option value="">Please Select</option>
                                <?php foreach($parent_design_category as $pc) 
                                { ?>

                                <option <?php if(!empty($p1_cat_ids_arr)  && in_array($pc->id,$p1_cat_ids_arr)  ) { echo 'selected'; } ?> value="{{$pc->id}}">{{$pc->name}}</option>



                                 <?php    } ?>

                               
                            </select>

                            @include('snippets.errors_first', ['param' => 'p1_cat'])
                        </div>
                      </div>


                     <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('p2_cat') ? ' has-error' : '' }}">
                            <label class="control-label">Sub Category:</label>

                            <select name="p2_cat" id="p2_cat" class="form-control" >

                                <option value="">Please Select</option>

                                <?php if(!empty($sub_category) && $sub_category->count() > 0 ) { 

                                             foreach($sub_category as $scat)
                                             { ?>

                                              <option <?php if(!empty($p2_cat_ids_arr) && in_array($scat->id,$p2_cat_ids_arr)) { echo 'selected'; } ?> value="{{$scat->id}}">{{$scat->name}}</option>

                                             <?php 

                                             }

                                         }

                                        ?>



                                   




                                 

                               
                            </select>

                            @include('snippets.errors_first', ['param' => 'p2_cat'])
                        </div>
                    </div>



                     <div class="col-sm-12 col-md-6">
                        <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label class="control-label"> Sub-Subcategory:</label>

                            <select  name="category_id[]" id="category_id" class="form-control" multiple="" >

                                

                                 <?php if(!empty($sub_sub_category) && $sub_sub_category->count() > 0 ) { 

                                             foreach($sub_sub_category as $scat)
                                             { ?>

                                              <option <?php if(!empty($selected_cat_ids) && in_array($scat->id,$selected_cat_ids)) { echo 'selected'; } ?> value="{{$scat->id}}">{{$scat->name}}</option>

                                             <?php 

                                             }

                                         }

                                        ?>


                                
                            </select>
                             @include('snippets.errors_first', ['param' => 'category_id'])
                        </div>
                    </div>


                    








		<div class="col-sm-12 col-md-3">

                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label class="control-label required">Color:</label>

                            <?php
                            if(!empty($ColorsMaster) && count($ColorsMaster) > 0){
                                ?>

                                <select name="color" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($ColorsMaster as $cm){
                                        $selected = '';

                                        if($cm->id == $color){
                                            $selected = 'selected';
                                        }
                                        if($cm->children()->count() > 0){
                                            ?>
                                            <optgroup label="{{$cm->name}}">
                                                <?php
                                                foreach($cm->children as $ccm){
                                                    if($ccm->id == $color){
                                                        $selected = 'selected';
                                                    }
                                                    ?>
                                                    <option value="{{$ccm->id}}" {{$selected}} >{{$ccm->name}}</option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                            <?php
                                        }
                                        else{
                                            ?>
                                            <option value="{{$cm->id}}" {{$selected}} >{{$cm->name}}</option>
                                            <?php
                                        }
                                        ?>
                                        
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>

                            @include('snippets.errors_first', ['param' => 'color'])
                        </div>
                    </div>
 

		

        </div>
		<div class="row">
        
        <?php /*
		<div class="col-md-6 form-group {{ $errors->has('status') ? ' has-error' : '' }}">
			<label>Status * : </label>
			<br>

			<input type="checkbox" name="status" <?php if($status==1) { echo 'checked'; } ?> value="1"> Active/Inactive
			
		</div>

		<?php */ ?>



		<div style="display: none;" class="col-md-6 form-group {{ $errors->has('featured') ? ' has-error' : '' }}">
			<label>Featured * : </label>
			<br>

			<input type="checkbox" name="featured" <?php if($featured==1) { echo 'checked'; } ?> value="1"> Active/Inactive
			
		</div>

		</div>
		<div class="row">
		<div class="col-md-12">

		<input class="btn" type="submit" value="Save" />
		</div>


		</div>
		


		</form>
			

		</div> 
	</div>
</section>

@include('common.footer')

<script type="text/javascript" src="{{ url('public/bootstrap-multiselect/bootstrap-multiselect.js') }}"></script>
<script type="text/javascript">

$(document).ready(function() {
   
   $('#category_id1').multiselect({
        numberDisplayed: 2



    });


   $("#p1_cat").change(function()
   {

       
       var token= '{{ csrf_token() }}';
       var p_cat_id= $("#p1_cat").val(); 
        if(p_cat_id)
        {
                  $.ajax({
                  url: "{{url('ajaxhit/getCatgory')}}", 
                  type: 'post',
                  cache: false,
                  dataType: 'html',
                  //data: $('#'+form_id).serialize(),
                  data: {_token:token,cat_id:p_cat_id},
                  crossDomain: true,
                  beforeSend: function()
                  {
                    
                  },
                  success: function(response)
                  {
                      $('#p2_cat').html(response);
                      //$('#category_id').html();
                      //alert(response);
                  },
                 });


        }

   });

   $("#p2_cat").change(function()
   {

       var token= '{{ csrf_token() }}';
       var p_cat_id= $("#p2_cat").val(); 
        if(p_cat_id)
        {
                  $.ajax({
                  url: "{{url('ajaxhit/getCatgory')}}", 
                  type: 'post',
                  cache: false,
                  dataType: 'html',
                  //data: $('#'+form_id).serialize(),
                  data: {_token:token,cat_id:p_cat_id},
                  crossDomain: true,
                  beforeSend: function()
                  {
                    
                  },
                  success: function(response)
                  {
                      $('#category_id').html(response);
                      //alert(response);
                  },
                 });


        }

   });






});  // end of ready function 





</script>


 
</body>
</html>