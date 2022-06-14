<?php

$segment = request()->segment(1);

$cmspages = CustomHelper::getData($tbl='cms_pages', $id=0, $where='', $selectArr=['title','slug']);

if(!empty($cmspages) && count($cmspages) > 0){
	foreach ($cmspages as $cms_page){
	$activeClass = '';
		if($segment == $cms_page->slug){
			$activeClass = 'active';
		}
?>
<ul>
	<li><a class="{{$activeClass}}" href="{{url($cms_page->slug)}}">{{$cms_page->title}}</a></li>
</ul>
<?php
}
}
?>