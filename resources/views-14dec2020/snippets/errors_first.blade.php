<?php
if(!empty($errors) && count($errors) > 0){
?>

@if ($errors->has($param))
    <p class="help-block">
        {{ $errors->first($param) }}
    </p>
@endif

<?php } ?>