<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="news" class="<?=$type?>">
<ol>
	<?php $i=0; foreach($items as $item): ?>
	<li class="<?=++$i & 1 ? 'odd' : 'even'?>">
		<p><?=$item->content?></p>
		<span>
			Bongasi <?=$item->author?>
			<br />
			<?=date('j.n.Y', $item->date)?>
		</span>
	</li>
	<?php endforeach?>
</ol>
</div>