<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Minesweeper extends Controller_Website {      
    
        public function action_index()
        {            
            $this->content = View::factory('minesweeper')
                ->render();
        }
} // End Minesweeper
