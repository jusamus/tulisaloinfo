<?php defined('SYSPATH') or die('No direct script access.'); ?>
<style type="text/css">
    #minesweeper div {
        display: inline-block;
        border-radius: 5px;
        margin: 5px;
        padding: 5px 10px;
    }
    
    hr {
        margin: 5px;
    }
    
    h2 {
        font-size: 200%;
        padding-top: 20px;
        display: inline-block;
    }
    
    button {
        margin: 20px 0 0 40px;
        padding: 10px 20px;
        font-size: 140%;
    }
    
    .player {
        border: 2px solid #ccc;
        width: 110px;
        height: 80px;
        text-align: center;
        position: relative;
    }
    
    .player .name {
        float: left;
        margin: 5px;
        display: inline-block;
        font-size: 120%;
    }
    
    .player .points {
        float: right;
        margin: 5px;
        color: #4A78B3;
        font-size: 120%;
    }
    
    .player .lives {
        margin: 5px;
        position: absolute;
        left: 5px;
    }
    
    .player .life {
        color: #ff0000;
        font-size: 200%;
        padding: 0 3px;
    }
    
    .player.active {
        border-color: #009900;
    }
    
    .info {
        width: 160px;
        height: 60px;
        position: relative;
    }
    
    .info h4 {
        margin: 5px;
        position: absolute;
        top: 15px;
    }
    
    #mine h4 {
        right: 0px;
    }
    
    #mine .card {
        left: 0px;
        top: 0px;
    }
    
    #life h4 {
        left: 0px;
    }
    
    #life .card {
        right: 0px;
        top: 0px;
    }
    
    #mine {
        float: left;
    }
    
    #life {
        float: right;
    }

    /* For modern browsers */
    #board:before,
    #board:after {
        content:"";
        display:table;
    }

    #board:after {
        clear:both;
    }

    /* For IE 6/7 (trigger hasLayout) */
    #board {
        zoom: 1;
        margin: 50px;
        position: relative;
        width: 550px;
        height: 450px;
    }
    
    .card {
      display: inline-block;
      position: absolute;
      margin-right: 2px;
      width: 29px;
      height: 50px;
      border-radius: 2px;
      background: white;
      -webkit-box-shadow: 3px 3px 7px rgba(0,0,0,0.3);
      box-shadow: 3px 3px 7px rgba(0,0,0,0.3);
      border: 1px solid #999;
    }
    
    .back {
        background-color:white;
        background-image: linear-gradient(90deg, rgba(200,0,0,.5) 50%, transparent 50%),
        linear-gradient(rgba(200,0,0,.5) 50%, transparent 50%);
        background-size:11px 11px;
    }
    
    .card p {
      text-align: center;
      font: 20px/44px Georgia, serif;
    }

    .suitdiamonds:before, .suitdiamonds:after {
      content: "♦";
      color: #f00;
    }

    .suithearts:before, .suithearts:after {
      content: "♥";
      color: #f00;
    }

    .suitclubs:before, .suitclubs:after {
      content: "♣";
      color: #000;
    }

    .suitspades:before, .suitspades:after {
      content: "♠";
      color: #000;
    }

    .suitjoker:before, .suitjoker:after {
      content: "JOKER";
      color: #f00;
    }

    div[class*='suit']:before {
      position: absolute;
      font-size: 12px;
      left: 5px;
      top: 2px;
    }

    div[class*='suit']:after {
      position: absolute;
      font-size: 12px;
      right: 5px;
      bottom: 2px;
    }
    
    .card:hover {
      cursor: pointer;
    }
    
</style>
<center>
<div id="minesweeper">
    <div class="info" id="mine">
        <h4>Miinakortti</h4>
        <div id="minecard" class="card">
            <p>A</p>
        </div>
    </div>
    <div class="info" id="life">
        <h4>Elämäkortti</h4>
        <div id="lifecard" class="card suitjoker">
            <p>J</p>
        </div>
    </div>
    <button id="new">Aloita uusi peli</button>
    <div id="players"></div>
    <br />
    <div id="board"></div>
    <br />
<script type="text/javascript">
    jQuery.fn.shake = function(color,interval,distance,times){
        let jTarget = $(this);
        let left = parseInt(jTarget.css('left'));
        
        setTimeout(function(){
            interval = typeof interval == "undefined" ? 100 : interval;
            distance = typeof distance == "undefined" ? 5 : distance;
            times = typeof times == "undefined" ? 5 : times;
            
            if(color == 'red') {                
                jTarget.css({
                    'background-color': '#fdd',
                    'border': '1px solid #d66'
                });
            }
            else if(color == 'green') {               
                jTarget.css({
                    'background-color': '#efe',
                    'border': '1px solid #6d6'
                });
            }

            for(let iter=0;iter<(times+1);iter++){
                jTarget.animate({
                    left: ((iter%2==0 ? (left + distance) : left + (distance*-1)))
                }, interval);
            }

            return jTarget.animate({
                left: left
            },interval);
        }, 500);
    }
    
    class Player {
        constructor(index, name) {
            this.index = index;
            this.name = name;
            this.lives = 2;
            this.points = 0;
            this.rewardGained = false;
        }        
        
        render() {
            this.element = $('<div class="player" data-index="'+this.index+'"><span class="name">'+this.name+'</span><span class="points">'+this.points+'</span><hr/><span class="lives"></span></div>');
            this.element.data('player', this);
            
            for(let i = 1; i <= this.lives; i++) {
                this.element.find('.lives').append($('<span class="life">♥</span>'));
            }
            
            return this.element;
        }
        
        setActive() {
            this.element.addClass('active');
        }
        
        gainLife(noPoints = false) {
            if(!noPoints) {
                this.points += 10;
            }
            
            if(this.lives < 3) {
                this.lives++;
                $('div.player[data-index='+this.index+']').find('.lives').append($('<span class="life">♥</span>'));
            }            
        }
        
        loseLife() {
            
            this.points -= 10;
            
            if(this.lives > 0) {
                this.lives--;
                $('div.player[data-index='+this.index+']').find('.life:last').remove();
            }
            
            if(this.points < 0) {
                this.points = 0;
            }           
        }
    }
    
    class Card {
        constructor(suit, rank, value) {
            this.suit = suit;
            this.rank = rank;
            this.value = value;
            this.flipped = false;
        }
        
        flip() {
            let self = this;
            let card = self.element;
            let width = parseInt(card.css('width'));
            let padding = parseInt(card.css('paddingLeft'));
            let margin = parseInt(card.css('marginLeft'));
            
            if(card.hasClass('back')) {
                card.animate({
                    width: '0px',
                    paddingLeft: '0px',
                    paddingRight: '0px',
                    marginRight: (width - margin) + 'px',
                    marginLeft: (width + margin) + 'px',
                    backGroundSize: '11px 1px'
                }, {
                   duration: 200,
                   complete: function(){
                        card.removeClass('back').addClass('suit'+self.suit).html('<p>'+(self.rank == 'joker' ? 'J' : self.rank)+'</p>');
                        
                        self.flipped = true;
                        
                        card.animate({
                            width: width + 'px',
                            paddingLeft: padding + 'px',
                            paddingRight: padding + 'px',
                            marginRight: margin + 'px',
                            marginLeft: margin + 'px',
                            backGroundSize: '11px 11px'
                        }, 200);
                   }
                });
                
            }
            /*else {
                card.removeClass('suit'+this.suit).addClass('back').children().remove();
            }*/
        }
        
        render() {
            let self = this;
            this.element = $('<div class="card back"></div>');
            this.element.data('card', this);
            
            this.element.click(function(){
               self.flip(); 
            });
            
            return this.element;
        }
    }
    
    class Deck {
        
        constructor() {
            this.cards = [];
            this.create();
            this.shuffle();
        }
        
        create() {
            let suits = ['spades', 'hearts', 'diamonds', 'clubs'];
            let ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
            let values = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
            
            for (let i = 0; i < suits.length; i++) {
                for (let j = 0; j < ranks.length; j++) {
                    this.cards.push(new Card(suits[i], ranks[j], values[j]));
                }
            }
            
            this.cards.push(new Card('joker', 'joker', 14));
            this.cards.push(new Card('joker', 'joker', 14));
        }
        
        shuffle() {            
            let loc1, loc2, tmp;
            for (let i = 0; i < 1000; i++) {
                loc1 = Math.floor((Math.random() * this.cards.length));
                loc2 = Math.floor((Math.random() * this.cards.length));
                tmp = this.cards[loc1];
                this.cards[loc1] = this.cards[loc2];
                this.cards[loc2] = tmp;
             }
        }
        
        find(suit = null, rank = null) {
            let cards = [];
            
            $.each(this.cards, function(i, card){
                if(suit !== null && rank !== null) {
                    if(card.suit == suit && card.rank == rank) {
                        cards.push(card);
                    }
                }
                else if(suit !== null) {
                    if(card.suit == suit) {
                        cards.push(card);
                    }
                }
                else if(rank !== null) {
                    if(card.rank == rank) {
                        cards.push(card);
                    }
                }
            });
            
            return cards;
        }
        
        findRandom(type = 'mine') {
            let indexes = [];
            
            $.each(this.cards, function(i, card){
                if(!card.flipped) {
                    if(type == 'mine' && card.value <= 10) {
                        indexes.push(i);
                    }
                    else if(type == 'life' && card.value > 10) {
                        indexes.push(i);
                    }             
                }
            });
            
            return this.cards[indexes[Math.floor(Math.random() * indexes.length)]];
        }
    }
    
    class Game {        
        constructor() {
            this.reset();
        }
        
        reset() {
            this.players = [];
            this.turns = 0;
            this.deck = new Deck();
            this.activePlayer = 0;
            this.minecard = 'A';
            this.lifecard = 'joker';

            $('div#board').children().remove();
            $('div.player').hide();
        }
        
        create(players = []) {
            
            if(players.length) {
                for(let i = 0; i < players.length; i++) {
                    this.newPlayer(i, players[i]);
                }
            }
            else {                
                let playerCount = prompt('Montako pelaajaa?');

                for(let i = 0; i < playerCount; i++) {
                    this.newPlayer(i, prompt('Anna pelaajan '+(i + 1)+' nimi'));
                }
            }
            
            this.fillBoard();
            this.rotate();
        }
        
        newPlayer(index, name) {
            let player = new Player(index, name);
            this.players.push(player);
            player.render().appendTo('#players');
        }
        
        rotate() {
            if(!this.activePlayer) {
                this.activePlayer = this.players[0];
            }
            else {
                let activePlayerIndex;
                
                for(let i = 0; i < this.players.length; i++) {
                    if(this.players[i] == this.activePlayer) {
                        activePlayerIndex = i;
                    }
                    
                    this.players[i].element.removeClass('active');
                }
                
                if(activePlayerIndex == this.players.length - 1) {
                    this.activePlayer = this.players[0];
                }
                else {
                    this.activePlayer = this.players[activePlayerIndex + 1];
                }                
            }
            
            if(this.activePlayer.lives == 0) {
                this.rotate();
            }
            
            this.activePlayer.setActive();
        }
        
        fillBoard() {
            let game = this;
            let i = 0;
            let x = 0;
            let y = 0;
            
            $.each(this.deck.cards, function(i, card){
                setTimeout(function(){
                    let element = card.render();
                    element.click(function(){
                        if(!card.flipped) {
                            game.activePlayer.points += card.value;
                            
                            if(game.activePlayer.points >= 100 && !game.activePlayer.rewardGained) {
                                game.activePlayer.rewardGained = true;
                                game.activePlayer.gainLife(true);                                
                            }
                            
                            if(game.minecard == card.rank) {
                                game.activePlayer.loseLife();
                                card.element.shake('red');
                                game.minecard = game.deck.findRandom('mine').rank;
                                
                                $('#minecard').removeClass('suitjoker');
                                $('#minecard').find('p').text(game.minecard == 'joker' ? 'J' : game.minecard);
                                $('#minecard').shake();
                                
                                if(game.minecard == 'joker') {
                                    $('#minecard').addClass('suitjoker');
                                }
                            }
                            
                            if(game.lifecard == card.rank) {
                                game.activePlayer.gainLife();
                                card.element.shake('green');
                                game.lifecard = game.deck.findRandom('life').rank;
                                
                                $('#lifecard').removeClass('suitjoker');
                                $('#lifecard').find('p').text(game.lifecard == 'joker' ? 'J' : game.lifecard);
                                $('#lifecard').shake();
                                
                                if(game.lifecard == 'joker') {
                                    $('#lifecard').addClass('suitjoker');
                                }
                            }
                            
                            $('.player[data-index='+game.activePlayer.index+']').find('.points').text(game.activePlayer.points);
                            
                            if(game.activePlayer.lives == 0) {
                                $('.player[data-index='+game.activePlayer.index+']').shake('red');
                            }
                            
                            game.rotate();
                        }
                    }).appendTo('#board').animate({
                        left: x + 'px',
                        top: y + 'px'
                    }, 1000);

                    i++;

                    if(i % 9 == 0 ) {
                        x = 0;
                        y += 70;
                    }
                    else {
                        x += 65;    
                    }
                }, i * 50);
            });
        }
    }
    
    $(document).ready(function(){       
        
        $('#content-info').html('<div style="padding: 20px 20px 0 0"><h4>Säännöt</h4>\n\
<p>Tämä on alunperin Justus -poikani ideoima korttipeli joka sopii 2-6 pelaajalle\n\
(voi olla enemmänkin jos haluaa :)</p>\n\
<p>Säännöt on helpot: kukin pelaaja kääntää vuorollaan yhden kortin. Kaikista korteista\n\
saa pisteitä kortin arvon mukaan: A = 1p, 2 = 2p jne. Jokerista saa 14 pistettä.</p>\n\
<p>Jos käännetty kortti on "miinakortti", lähtee pelaajalta yksi elämä ja 10 pistettä.\n\
Miinakortti vaihtuu tällöin toiseksi satunnaiseksi 1p - 10p arvoiseksi kortiksi. Mikäli\n\
pelaajalla ei ole elämiä, hänen pelinsä loppuu.</p>\n\
<p>Jos käännetty kortti on "elämäkortti", saa pelaaja yhden lisäelämän ja 10 lisäpistettä.\n\
Elämiä voi olla maksimissaan 3. Elämäkortti vaihtuu myös tällöin joksikin toiseksi 11p - 14p\n\
arvoiseksi kortiksi</p>\n\
<p>Jos pelaaja onnistuu saamaan 100 pistettä, hän saa yhden lisäelämän, mutta ei lisäpisteitä.</p></div>');
        
        $('.life').live('click', function(){
            $(this).remove();
        })
        
        var game;
        
        $('button#new').click(function(){            
            game = new Game();
            game.create();
            
        });
        
    });
</script>
</div>
</center>
