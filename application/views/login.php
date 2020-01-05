<?php defined('SYSPATH') or die('No direct script access.'); ?>
<h2>Kirjaudu sisään</h2>
<?=Form::open('login/check')?>
<p>
	<?=Form::label('username','Tunnus:')?>
	<?=Form::input('username')?>
</p>
<p>
	<?=Form::label('password','Salasana:')?>
	<?=Form::password('password')?>
</p>
<p>
	<?=Form::submit('submit', 'Kirjaudu')?>
</p>
<?=Form::close()?>