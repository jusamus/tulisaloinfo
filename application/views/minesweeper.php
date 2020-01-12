<?php defined('SYSPATH') or die('No direct script access.'); ?>
<style type="text/css">
    #minesweeper div {
        display: inline-block;
        border-radius: 5px;
        margin: 5px;
        padding: 5px 10px;
    }
    
    .player {
        border: 2px solid #ccc;
        width: 100px;
    }
    
    .player .name {
        margin: 5px;
    }
    
    .player .lives {
        margin: 5px;
    }
    
    .player .life {
        color: #ff0000;
        font-size: 200%;
        padding: 0 3px;
    }
    
    .player.active {
        border-color: #009900;
    }
    
    #board {
      margin: 50px;
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
        zoom:1;
    }
    
    .card {
      position: relative;
      float: left;
      margin-right: 2px;
      width: 30px;
      height: 50px;
      border-radius: 2px;
      background: white;
      -webkit-box-shadow: 3px 3px 7px rgba(0,0,0,0.3);
      box-shadow: 3px 3px 7px rgba(0,0,0,0.3);
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
      content: "◆";
      color: #ff0000;
    }

    .suithearts:before, .suithearts:after {
      content: "♥";
      color: #ff0000;
    }

    .suitclubs:before, .suitclubs:after {
      content: "♣";
      color: #000;
    }

    .suitspades:before, .suitspades:after {
      content: "♠";
      color: #000;
    }

    div[class*='suit']:before {
      position: absolute;
      font-size: 12px;
      left: 2px;
      top: 2px;
    }

    div[class*='suit']:after {
      position: absolute;
      font-size: 12px;
      right: 2px;
      bottom: 2px;
    }
    
    .card:hover {
      cursor: pointer;
    }
    
</style>
<center>
<div id="minesweeper">
<br/>
    <h2>Miinaharava</h2>
    <div id="players"></div>
    <br />
    <div id="board"></div>
    <br />
    <div id="controls">
        <button id="new">Aloita uusi</button>
    </div>
<script type="text/javascript">
       
    class Player {
        constructor(index, name) {
            this.index = index;
            this.name = name;
            this.lives = 3;
        }        
        
        render() {
            this.element = $('<div class="player"><h4 class="name">'+this.name+'</h4><p class="lives"></p></div>');
            this.element.data('player', this);
            
            for(let i = 1; i <= this.lives; i++) {
                this.element.find('.lives').append($('<span class="life">♥</span>'));
            }
            
            return this.element;
        }
        
        setActive() {
            this.element.addClass('active');
        }        
    }
    
    class Card {
        constructor(suit, rank) {
            this.suit = suit;
            this.rank = rank;
        }
        
        flip() {
            let card = this.element;
                        
            if(card.hasClass('back')) {
                card.removeClass('back').addClass('suit'+this.suit).html('<p>'+this.rank+'</p>');
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
            
            for (let i = 0; i < suits.length; i++) {
                for (let j = 0; j < ranks.length; j++) {
                    this.cards.push(new Card(suits[i], ranks[j]));
                }
            }
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
            
            $('div#board').children().remove();
            $('div.player').hide();
        }
        
        create(players = []) {
            
            if(players.length) {
                for(let i = 1; i <= players.length; i++) {
                    this.newPlayer(i, players[i-1]);
                }
            }
            else {                
                let playerCount = prompt('Montako pelaajaa? (1-4)');

                for(let i = 1; i <= playerCount; i++) {
                    this.newPlayer(i, prompt('Anna pelaajan '+i+' nimi'));
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
            
            this.activePlayer.setActive();
        }
        
        fillBoard() {
            let game = this;
            
            $.each(this.deck.cards, function(i, card){
               card.render().click(function(){
                   game.rotate();
               }).appendTo('#board'); 
            });
        }
    }
    
    $(document).ready(function(){       
        
        var game = new Game();
        game.create(['Jussi', 'Justus', 'Luukas', 'Alisa']);
        
        $('button#new').click(function(){
            
            game = new Game();
            game.create();
            
        });
        
    });
</script>
</div>
</center>
