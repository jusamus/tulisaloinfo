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
    
</style>
<div id="hangman">
<center>
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
</div>
</center>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).keypress(function(e) {
            var key = String.fromCharCode(e.which)
            $('button.letter[data-letter='+key+']').trigger('click');
        });
        
        $('button.letter').click(function(){
            var clickedLetter = $(this).attr('data-letter');
            var self = $(this);
            
            $.getJSON('<?=URL::site('hangman/check')?>/'+clickedLetter, function($data){
                $.each($data, function(i, item){
                    $('div#word').find('button[data-index='+item+']').text(clickedLetter);
                });
                self.attr('disabled', 'disabled').css('background', '#faa');
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

