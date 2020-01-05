<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="footer-text">
	<div style="float: right;">
		Copyright &copy; <?=date('Y')?>
	</div>
	<div style="float: left;">
		Powered by
		<?=HTML::anchor(
			'http://www.kohanaphp.com',
			'Kohana 3',
			array('target'=>'_blank')
		)?>
		and
		<?=HTML::anchor(
			'http://www.jquery.com',
			'jQuery',
			array('target'=>'_blank')
		)?>
	</div>
</div>