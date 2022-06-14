<?php
$lable = (isset($label))?$label:'Category';
$selected_id = (isset($selected_id))?$selected_id:'';
?>
<div class="col-sm-4 categoryBox">

    <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
        <label class="control-label required">{{$lable}}:</label>

        <select name="category" class="form-control categoryList">
            <option value="">--Select--</option>
            <?php
            if(!empty($categories) && $categories->count() > 0){
                foreach($categories as $cat){

                    $selected = '';
                    if($cat->id == $selected_id){
                        $selected = 'selected';
                    }

                    ?>
                    <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
                    <?php
                }
            }
            ?>
        </select>

        @include('snippets.errors_first', ['param' => 'category'])
    </div>
</div>