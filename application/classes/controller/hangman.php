<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Hangman extends Controller {

        public function action_index()
        {
            if(!Session::instance()->get('word'))
            {
                $word = $this->fetchword();
            }
            else
            {
                $word = Session::instance()->get('word');
            }
            
            $this->request->response = View::factory('hangman')
                ->set('word', $word)
                ->render();
        }
        
        private function fetchword($level = NULL) {
            $word = ORM::factory('word')->limit(1)->order_by(Db::expr('RAND()'));
            switch ($level)
            {
                case 'easy':
                    $word->where('CHAR_LENGTH("word")', '<=', 5);
                break;
                case 'medium':
                    $word->where('CHAR_LENGTH("word")', '>', 5)
                    ->and_where('CHAR_LENGTH("word")', '<=', 10);   
                break;             
                case 'hard':
                    $word->where('CHAR_LENGTH("word")', '>', 10)
                    ->and_where('CHAR_LENGTH("word")', '<=', 20);
                break;
                case 'insane':
                    $word->where('CHAR_LENGTH("word")', '>', 20);
                break;
            }
            //die($word->find()->last_query());
            $word = $word->find()->word;            
            Session::instance()->set('word', $word);
            return $word;
        }
        
        public function action_new()
        {
            $level = !empty($_REQUEST['level']) ? $_REQUEST['level'] : '';
            $word = $this->fetchword($level);
            
            die(View::factory('hangman')
                ->set('word', $word)
                ->render());
        }
        
        public function action_check() {
            $indexes = array();
            $guess = $this->request->param('guess');
            $word = Session::instance()->get('word');
            foreach (UTF8::str_split($word) as $index => $letter)
            {
                if($letter === $guess)
                {
                    $indexes[] = $index;
                }
            }
            die(json_encode($indexes));
        }
} // End Hangman
