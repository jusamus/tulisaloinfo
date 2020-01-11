<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Minesweeper extends Controller {

        public function action_index()
        {            
            $this->request->response = View::factory('minesweeper')
                ->render();
        }
} // End Minesweeper
