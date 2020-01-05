<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="news" class="<?=$type?>">
<ol>
	<?php $i=0; foreach($items as $item): ?>
	<li class="<?=++$i & 1 ? 'odd' : 'even'?>">
		<p><?=HTML::anchor($item->path, $item->title)?></p>
		<span>Muokattu <?=date('j.n.Y H:i', $item->modified)?></span>
	</li>
	<?php endforeach?>
</ol>
</div>