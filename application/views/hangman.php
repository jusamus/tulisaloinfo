<?php defined('SYSPATH') or die('No direct script access.'); ?>
<style type="text/css">
    #hangman div, 
    #hangman button, 
    #hangman select {
        font-size: 110%;
    }
    
    #hangman button {
        margin: 2px;
        padding: 2px 5px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    #hangman select {
        margin: 2px;
        padding: 2px 5px;
        font-weight: bold;
    }
    
    #hangman hr {
        padding: 0;
        margin: 20px 0;
    }
    
    #hangman div.lives {
        font-size: 140%;
        font-weight: bold;
    }
    
    #hangman ul {
        list-style-type: none;
        font-size: 110%;
    }
    
</style>
<center>
<div id="hangman">
<br/>
    <h4>Hirsipuu</h4>
    <div id="word">
    <?php
        $i = 0;
        foreach(UTF8::str_split($word) as $letter)
        {
            $letter = UTF8::strtolower($letter);
            echo Form::button('word', '?', array('data-index' => $i, 'class' => 'word'));
            $i++;
        }
    ?>
    </div>
    <hr style=""/>
    <div id="letters">
    <?php
        $letters = 'qwertyuiopåasdfghjklöäzxcvbnm';

        foreach(UTF8::str_split($letters) as $letter)
        {
            if($letter == 'a' OR $letter == 'z') echo '<br/>';
            echo Form::button('letter', $letter, array('data-letter' => $letter, 'class' => 'letter'));
        }
    ?>
    </div>
    <br/>
    <?=Form::select('level', array('' => 'Valitteppa vaikeusaste',
        'easy' => 'Heleppo (max 5 kirjainta)',
        'medium' => 'Kohtalaanen (6-10 kirjainta)',
        'hard' => 'Kimurantti (11-20 kirjainta)',
        'insane' => 'Järjetöön (yli 20 kirjainta)'), !empty($_REQUEST['level']) ? $_REQUEST['level'] : '', array('id' => 'level'))?>
    &nbsp;&nbsp;
    <?=Form::button('new', 'Hae uusi sana', array('id' => 'new'))?>
    <br/>
    <hr/>
    <div id="stats">
        <h4>Statistiikkaa (since 6.1.2020)</h4>
        <ul>
            <li>Kaikkiaan oikein arvattuja kirjaimia: <span id="correct"><?=$stats['correct']?></span></li>
            <li>Kaikkiaan väärin arvattuja kirjaimia: <span id="incorrect"><?=$stats['incorrect']?></span></li>
        </ul>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).unbind('keypress');
        $('div#content-info img').attr('src', '<?=URL::site('assets/img/hangman')?>/10.png');
        
        var keyPressed = false;
        
        $(document).keypress(function(e) {
            var key = String.fromCharCode(e.which);
            if($('button.letter[data-letter='+key+']').is(':disabled') || keyPressed) {
                return false;
            }
            else {
                if(!keyPressed) {                    
                    keyPressed = TRUE;
                    $('button.letter[data-letter='+key+']').trigger('click');
                }
            }            
        });
        
        var lives = 10;
        $('span#lives').text(lives);
        
        $('button.letter').click(function(){
            var clickedLetter = $(this).attr('data-letter');
            var self = $(this);
            
            $.getJSON('<?=URL::site('hangman/check')?>/'+clickedLetter, function($data){
                var guessedCount = $('button.guessed').length;
                
                $.each($data.indexes, function(i, item){
                    $('div#word').find('button[data-index='+item+']').text(clickedLetter).addClass('guessed');
                });
                
                if(guessedCount < $('button.guessed').length) {                    
                    self.attr('disabled', 'disabled').css('background', '#afa');
                }
                else {
                    lives--;
                    self.attr('disabled', 'disabled').css('background', '#faa');
                }
                
                $('span#correct').text($data.stats.correct);
                $('span#incorrect').text($data.stats.incorrect);
                $('div#content-info img').attr('src', '<?=URL::site('assets/img/hangman')?>/'+lives+'.png');
                
                if($('button.guessed').length === $('button.word').length) {
                    setTimeout(function(){
                        alert('Hyvä hyvä, tehtävä suoritettu!');
                        $('button#new').trigger('click');
                    }, 200);
                }
                else if(lives == 0) {
                    setTimeout(function(){
                        alert('Moon pahoillani, hävisit pelin! Yritäppä uusiksi!');
                        $('button#new').trigger('click');
                    }, 200);                    
                }
                
                keyPressed = false;
            });
            
        });
        
        $('button#new').click(function(){
            var level = $('select#level').val();
            $.get('<?=URL::site('hangman/new')?>?level='+level, function(data){
                $('div#hangman').html($(data));
            });
        })
    });
</script>
</div>
</center>
