<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php if(count($children) OR $parent->id): ?>
<ul id="submenu">
<?php if($parent->id): ?>
	<li>&laquo; <?=HTML::anchor($parent->path,$parent->title)?></li>
<?php endif?>
<?php foreach($children as $child): ?>
	<li>&raquo; <?=HTML::anchor($child->path,$child->title)?></li>
<?php endforeach?>
</ul>
<?php endif?>
<?=$content?>

<script type="text/javascript">
	
$(document).ready(function(){
		
	$('ul#submenu a').click(function(){
		var link = $(this);
		$('div#content').fadeTo('fast',0.01,function(){
			$.get('/page/'+link.attr('href'),function(data){
				$('div#content').html(data).fadeTo('fast',1);
			});
		});
		return false;	
	});
});
</script>