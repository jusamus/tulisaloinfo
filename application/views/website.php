<?php defined('SYSPATH') or die('No direct script access.'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>tulisalo.info</title>
<?=HTML::style('assets/css/reset.css')?>
<?=HTML::style('assets/css/960.css')?>
<?=HTML::style('assets/css/text.css')?>
<?=HTML::style('assets/css/style.css')?>
<?=HTML::style('assets/css/print.css',array('media'=>'print'))?>
<?=HTML::style('assets/css/custom-theme/jquery-ui.custom.css')?>
<?=HTML::script('assets/js/jquery-1.3.2.min.js')?>
<?=HTML::script('assets/js/jquery-ui-1.7.2.custom.min.js')?>
<?=HTML::script('assets/js/jquery.timers-1.1.2.js')?>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<?=$header?>
	</div>
	<div id="separator">&nbsp;</div>
	<div id="menu">
		<?=$menu?>
	</div>
	<div class="container_16 clearfix" id="container">	
		<div class="grid_11" id="content">
			<?=$content?>
		</div>
		<div class="grid_5" id="right-col">
			<div id="right-col-frame">
				<div id="right-col-content">
					<?=$news?>
				</div>
			</div>
		</div>
		</div>
		<div id="push" class="clear"></div>
	</div>
</div>
<div id="footer">
	<?=$footer?>
</div>
<script type="text/javascript">	

	var loop = false;
	
	function loop_bg(obj)
	{
		if(loop)
		{
			obj.css('backgroundPosition', '0 0');
			obj.animate({backgroundPosition: '-15px 0'}, 500, 'linear', function(){
				loop_bg(obj);
			});			
		}
		else
		{
			obj.css('backgroundPosition', '0 0');
		}	
	}
	
	$(document).ready(function(){
		$('div#separator').ajaxStart(function(){
			loop = true;
			var div = $(this);
			div.animate({backgroundPosition: '-15px 0'}, 500, 'linear', function(){
				loop_bg(div);
			});			
		}).ajaxStop(function(){
			loop = false;
		});
	});
	
</script>
</body>
</html>