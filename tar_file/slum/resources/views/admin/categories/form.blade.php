@component('admin.layouts.main')

    @slot('title')
        Admin - {{$page_heading}} - {{ config('app.name') }}
    @endslot

    <div class="row">

    <?php
    $back_url = (request()->has('back_url'))?request()->input('back_url'):'';

    if(empty($back_url)){
        $back_url = 'admin/categories';
    }

    $parent_id = (isset($category->parent_id))?$category->parent_id:$parent_id;
    $name = (isset($category->name))?$category->name:'';
    $description = (isset($category->description))?$category->description:'';
    $sort_order = (isset($category->sort_order))?$category->sort_order:'';
    $meta_title = (isset($category->meta_title))?$category->meta_title:'';
    $meta_keyword = (isset($category->meta_keyword))?$category->meta_keyword:'';
    $meta_description = (isset($category->meta_description))?$category->meta_description:'';
    $featured = (isset($category->featured))?$category->featured:'';
    $status = (isset($category->status))?$category->status:'1';

    $categoryImages = (isset($category->categoryImages))?$category->categoryImages:'';

    $categoryAttributes = (isset($category->categoryAttributes))?$category->categoryAttributes:'';

    //pr($categoryAttributes->toArray());

    $parent_category_name = '';
    $parentCategoryAttributes = '';

    if(!empty($parent_category) && count($parent_category) > 0){
        
        $parent_category_name = (isset($parent_category->name))?$parent_category->name:'';

        $getParentCategoryAttributes = CustomHelper::getParentCategoryAttributes($parent_category);

        if(!empty($getParentCategoryAttributes) && count($getParentCategoryAttributes) > 0){
            $parentCategoryAttributes = array_flatten($getParentCategoryAttributes);
        }
    }

    

    $attr_labels_arr = [];
    $attr_sort_arr = [];

    if(!empty($categoryAttributes) && count($categoryAttributes) > 0){
        foreach($categoryAttributes as $c_a_key=>$cat_attr){
            $attr_labels_arr[$c_a_key] = $cat_attr->label;
            $attr_sort_arr[$c_a_key] = $cat_attr->sort_order;
        }
    }

    $attr_labels_arr = old('attr_labels', $attr_labels_arr);
    $attr_sort_arr = old('attr_sort_order', $attr_sort_arr);

    


    //pr($attr_labels_arr);

    $path = 'categories/';
    $thumb_path = 'categories/thumb/';

    $storage = Storage::disk('public');

    /*if(count($errors)){
        pr($errors);
    }*/
    ?>

        <div class="col-md-12">

            <h2>{{$page_heading}}</h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="alert_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <input type="hidden" name="category_id" value="{{$category_id}}">
                <input type="hidden" name="parent_id" value="{{$parent_id}}">

				<div class="bgcolor">
                <div class="row">
                    <div class="col-sm-12 col-md-12">

                        <?php
                        if(!empty($categories) && count($categories) > 0){
                            ?>
                            <div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="control-label">Parent Category:</label>

                                <select name="parent_id" class="form-control" >
                                    <option value="">--Select--</option>

                                    <?php
                                    foreach($categories as $cat){
                                        if($cat->id != $category_id && $cat->parent_id != $category_id){
                                            $selected = '';

                                            if($cat->id == $parent_id){
                                                $selected = 'selected';
                                            }
                                            ?>
                                            <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>

                                @include('snippets.errors_first', ['param' => 'parent_id'])
                            </div>
                            <?php
                        }
                        ?>

                        <?php
                        if(!empty($parent_category_name)){
                            ?>
                            <div class="form-group{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="control-label">Parent Category:</label>

                                <span class="form-control">
                                    {{$parent_category_name}}
                                </span>
                            </div>
                            <?php
                        }
                        ?>


                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="control-label required">Category Name:</label>

                            <input type="text" name="name" class="form-control" value="{{ old('name', $name) }}" maxlength="100" />

                            @include('snippets.errors_first', ['param' => 'name'])
                        </div>



                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="control-label">Description:</label>

                            <textarea name="description" class="form-control" maxlength="255">{{old('description', $description)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'description'])
                        </div>

                        

                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label class="control-label">Sort Order:</label>

                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $sort_order) }}" />

                            <p class="help-block">Set the order of this category (relative to other categories in the same level).</p>

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Title:</label>

                            <textarea name="meta_title" class="form-control" maxlength="255">{{old('meta_title', $meta_title)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_title'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Keywords:</label>

                            <textarea name="meta_keyword" class="form-control" maxlength="255">{{old('meta_keyword', $meta_keyword)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_keyword'])
                        </div>

                        <div class="form-group{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                            <label class="control-label">Meta Description:</label>

                            <textarea name="meta_description" class="form-control" maxlength="255">{{old('meta_description', $meta_description)}}</textarea>

                            @include('snippets.errors_first', ['param' => 'meta_description'])
                        </div>


                        <div class="form-group{{ $errors->has('images.*') ? ' has-error' : '' }} col-md-12">
                            <label for="images" class="control-label required">Images: </label>
                            (Preferred dimensions: width:768px, height:768px)

                            <?php
                            $count_images = count($categoryImages);

                            if(!empty($categoryImages) && count($categoryImages) > 0){
                                
                                ?>
                                <table class="table">
                                    <tr>
                                        <th>Image</th>
                                        <th>Default</th>
                                        <th>Remove</th>
                                    </tr>
                                    <?php
                                    foreach($categoryImages as $ci){

                                        $image = $ci->image;

                                        if(!empty($image) && $storage->exists($thumb_path.$image)){

                                            $target_path = url('public/storage/'.$thumb_path.$image);

                                            if($storage->exists($path.$image)){
                                                $target_path = url('public/storage/'.$path.$image);
                                            }
                                            ?>
                                            <tr>
                                                <td>

                                                    <a href="{{$target_path}}" target="_blank"><img src="{{url('public/storage/'.$thumb_path.$image)}}" width="100" /></a>
                                                </td>

                                                <td><input type="radio" name="is_default" value="{{ $ci->id}}" {{ $ci->is_default ? 'checked' : '' }} /></td>

                                                <td><input type="checkbox" name="images_remove[]" value="{{ $ci->id }}" /></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                            }
                            ?>


                            <input type="file" name="images[]" multiple>
                            <input type="hidden" name="count_images" value="{{$count_images}}" />

                             @include('snippets.errors_first', ['param' => 'images.*'])
                        </div>








                        

                            <?php
                            if(!empty($parentCategoryAttributes) && count($parentCategoryAttributes) > 0){
                                ?>

                                <div class="col-md-12">
                                    <label for="heading_1" class="control-label">Parent Attributes:</label>
                                </div>

                                <div class="col-md-12">

                                    <?php
                                    foreach($parentCategoryAttributes as $pca){
                                        ?>

                                        <div class="row attribute">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <span class="form-control">{{$pca->label}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <span class="form-control">{{$pca->sort_order}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        

                        <div class="col-md-12">
                            <label for="heading_1" class="control-label">Attributes:</label>
                        </div>


                        <div class="col-md-12 attributes_box">

                        <?php
                            $countheading = 1;

                        if(!empty($attr_labels_arr) && count($attr_labels_arr) > 0){
                            //pr($category->toArray());
                            foreach($attr_labels_arr as $attr_key=>$label){
                                /*if(strpos($cat_k, 'heading') === 0 && !empty($cat_v)){
                                    $sel_heading = (isset($product->{$cat_k}))?$product->{$cat_k}:'';*/

                                    $attr_sort_order = (isset($attr_sort_arr[$attr_key]))?$attr_sort_arr[$attr_key]:'';

                                    $attr_params = [];
                                    $attr_params['countheading'] = $countheading;
                                    $attr_params['label'] = $label;
                                    $attr_params['attr_sort_order'] = $attr_sort_order;
                                    ?>

                                    @include('admin.categories._attribute_row', $attr_params)
                                    
                                    <?php

                                    $countheading++;
                                //}
                            }
                        }
                        if($countheading <= 1){

                            $attr_params = [];
                            $attr_params['countheading'] = $countheading;
                            ?>

                            @include('admin.categories._attribute_row', $attr_params)
                            
                            <?php
                        }
                        ?>
                        
                                                        
                        </div>











                        <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                            <label class="control-label">Featured:</label>

                            <input type="checkbox" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'featured'])
                        </div>

                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                        <div class="form-group">

                            <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                            <a href="{{ url($back_url) }}" class="btn btn-primary" >Cancel</a>
                        </div>
                    </div>

                </div>
				</div>

            </form>

        </div>

    </div>

@endcomponent

<?php

$pattern = '/\n*/m';
$replace = '';

$attr_params = [];
$attr_params['countheading'] = $countheading;
$attr_params['showRemoveBtn'] = true;

$attr_row_html = view('admin.categories._attribute_row', $attr_params);

$attr_row = preg_replace( $pattern, $replace, $attr_row_html);
?>

<script type="text/javascript">

    
    $(".add_head_row").click(function(){
        var attribute_len = $(".attribute").length;

        if(attribute_len+1 > 10){
            alert('Maximum 10 Attributes are allowed.');
        }
        else{

            var attr_row = '<?php echo $attr_row; ?>';

            $(".attributes_box").append(attr_row);
        }
    });



    $(document).on("click",".remove_head_row", function(){
        $(this).parent(".attribute").remove();
    });

</script>