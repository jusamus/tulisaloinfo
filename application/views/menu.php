<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="news_select">
	Uutisboksi
	<?=Form::select('news',array(
		'pages' => 'Tuoreimmat sivut',
		'proverbs' => 'Tuoreimmat höpinät'),
		'pages')?>
</div>
<ul id="navlist">
	<li id="login">
		<?php if(A1::instance()->logged_in()): ?>
		<?=HTML::anchor('logout', '<span>Kirjaudu ulos</span>', array('id'=>'logout'))?>
		<?php else: ?>
		<?=HTML::anchor('login', '<span>Kirjaudu</span>', array('id'=>'login'))?>
		<?php endif?>
	</li>
<?php foreach($menuitems as $item):?>
	<li>
		<?=HTML::anchor(
			$item->path,
			'<span>'.$item->title.'</span>',
			array(
				'id' => $item->path,
				'class'	=> strpos($item->path, $current_page) === 0 ? 'active' : ''))?>
	</li>
<?php endforeach?>
</ul>
<script type="text/javascript">
	
$(document).ready(function(){
	
	var inactive_bgcolor = $('ul#navlist a:not(.active)').css('backgroundColor');
	var active_bgcolor = $('ul#navlist a.active').css('backgroundColor');
	var inactive_color = $('ul#navlist a:not(.active)').css('color');
	var active_color = $('ul#navlist a.active').css('color');
	var hover_color = $('ul#navlist a.active').css('borderColor');
	
	$('div#menu a#news').click(function(){
		$(this).animate({height: '-=2px'},100,'linear',function(){
			$(this).animate({height: '+=2px'},100);
		});
	}).toggle(function(){
		$('div#right-col-content').slideUp('fast');
	},function(){
		$('div#right-col-content').slideDown('fast');
	});
	
	$('ul#navlist a').click(function(){
		if($(this).attr('id') == 'logout') return true;
		var link = $(this);
		$('ul#navlist a').not(link).animate({
			backgroundColor: inactive_bgcolor,
			color: inactive_color
		}).removeClass('active').mouseover(function(){
			$(this).css('backgroundColor',hover_color);
		}).mouseout(function(){
			$(this).css('backgroundColor',inactive_bgcolor);
		});		
		$(link).animate({
			backgroundColor: active_bgcolor,
			color: active_color
		}).mouseover(function(){
			$(this).css('backgroundColor',active_bgcolor);
		}).mouseout(function(){
			$(this).css('backgroundColor',active_bgcolor);
		});			
		$('div#content').fadeTo('fast',0.01,function(){
			$.get('/page/'+link.attr('id'),function(data){
				$('div#content').html(data).fadeTo('fast',1);
				$('ul#navlist a').not(link).removeClass('active');			
				$(link).addClass('active').css('color',active_color);
			});
		});
		return false;	
	});
	
	$('div#news_select select').change(function(){
		var type = $(this).val();
		$('div#right-col-content').slideUp('normal',function(){
			$.get('/news/'+type,function(data){					
				$('div#right-col-content').html($(data)).slideDown('normal');
			});
		});
	});
});
</script>