<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="proverb">
	<p><?=$proverb?></p>
	<div>
		Höpinäkones &trade;
		<a href="#" id="dice">&nbsp;</a><a href="#" id="save">&nbsp;</a>
	</div>
</div>
<div id="bubble">
	<div id="slider"></div>
</div>
<div id="interval">
	Puhallusväli <span id="start"></span> - <span id="end"></span> s.
</div>
<script type="text/javascript">
var images = [
	'assets/img/bubble1.png',
	'assets/img/bubble2.png',
	'assets/img/bubble3.png',
	'assets/img/bubble4.png',
	'assets/img/bubble5.png',
	'assets/img/save_hover.png',
	'assets/img/dice_hover.png']; 
jQuery.each(images, function(i) {
  images[i] = new Image();
  images[i].src = this;
});

function change_news(type)
{
	if($('div#news_select select').val() != type || !$('div#news').hasClass(type))
	{
		$('div#news_select select').val(type).change();
	}
}

function rand(base, variant)
{
	return base+Math.floor(Math.random()*variant);
}

var pos;
function wobble(bubble)
{	
	pos = $(bubble).position();
	if(pos.left > rand(600,300) || pos.top < 0 || bubble.css('opacity') == 0)
	{
		bubble.unbind().remove();
		delete bubble;
		return;
	}
	bubble.animate({
			width: '+=4px',
			height: '-=2px',
			top: '-='+rand(0,20)+'px',
			left: '+='+rand(20,30)+'px'
		}, rand(800,100), 'linear', function(){
			bubble.animate({
			width: '-=2px',
			height: '+=4px',
			top: '-='+rand(0,20)+'px',
			left: '+='+rand(20,30)+'px'
			}, rand(800,100), 'linear', function(){
				wobble(bubble);
			});
		});
}
var start = 0;
var end = 8000;
var random_time = rand(start,end-start);
function interval(element)
{
	element.oneTime(random_time+1, 'bubbles', function(){
		random_time = rand(start,end-start);
		element.trigger('click');
		interval(element);
	});
}

$(document).ready(function(){
	var random, size;
	$('div#bubble').click(function(){
		if($('img.bubble').length > 20) return;
		random = rand(1,4);
		size = rand(10,30);
		var bubble = $('<img src="/assets/img/bubble'+random+'.png" class="bubble" />');
		
		bubble.mouseover(function(){
			$(this).css({opacity: 0});
		}).css({
			position: 'absolute',
			left: '225px',
			top: '120px',
			width: size+'px',
			height: size+'px',
			opacity: 0
		}).appendTo('div#header').animate({
			top: '+='+rand(30,20)+'px',
			left: '+='+rand(30,20)+'px',
			opacity: <?=
			(strpos(Request::instance()->user_agent('browser'),'Explorer') === FALSE)
				? '1'
				: '0.2'?>0
		}, rand(700,100), 'linear', function(){
			wobble(bubble);
		});		
	}).oneTime(random_time, 'bubbles', function(){
		interval($(this));
	}).mouseenter(function(){
		$('div#slider, div#interval').fadeIn('fast');
	}).mouseleave(function(){
		$('div#slider, div#interval').fadeOut('fast');
	});
	
	$('div#slider').slider({
		range: true,
		min: 0,
		max: 50,
		values: [start/1000, end/1000],
		slide: function(event, ui) {
			start = ui.values[0]*1000;
			end = ui.values[1]*1000;
			$('span#start').text(ui.values[0]);
			$('span#end').text(ui.values[1]);
			random_time = rand(start,end-start);
			$('div#bubble').stopTime('bubbles');
			interval($('div#bubble'));
		}
	});
	
	$('span#start').text(start/1000);
	$('span#end').text(end/1000);
		
	$('div#proverb a#dice').click(function(){
		change_news('proverbs');
		$('div#proverb p').hide('normal',function(){
			$.get('/proverb?'+Math.random(),function(data){
				$('div#proverb p').text(data).show('normal');			
			});
		});		
	});
	
	$('div#proverb a#save').click(function(){
		var name;
		if(name = prompt('Mikäs sun nimi mahtaa olla?'))
		{
			$.post('/proverb/save',{
				content: $('div#proverb p').text(),
				author: name
			}, function(data){				
				$('div#news_select select').val('proverbs').change();
			});
		}
		else
		{
			alert('Voi nääs. Pakko antaa joku nimi.')
		}
	});
});
</script>