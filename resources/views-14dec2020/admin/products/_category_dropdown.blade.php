<?php
$lable = (isset($label))?$label:'Category';
$selected_id = (isset($selected_id))?$selected_id:'';
?>

<option value="">--Select--</option>
<?php
if(!empty($categories) && $categories->count() > 0){
    foreach($categories as $cat){

        $selected = '';

        if(is_array($selected_id)){
            if(in_array($cat->id, $selected_id)){
                $selected = 'selected';
            }
        }
        elseif($cat->id == $selected_id){
            $selected = 'selected';
        }

        ?>
        <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
        <?php
    }
}
?>