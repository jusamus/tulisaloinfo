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
                ->set('stats', $this->stats())
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
        
        private function stats()
        {
            $stats = array();
            
            $query = DB::select(DB::expr('COUNT(id) correct_count'))
                ->from('guesses')
                ->where('correct', '=', 1)
                ->execute();

            $result = $query->as_array();

            $stats['correct'] = isset($result[0]['correct_count']) ? $result[0]['correct_count'] : 0;
            
            $query = DB::select(DB::expr('COUNT(id) incorrect_count'))
                ->from('guesses')
                ->where('correct', '=', 0)
                ->execute();

            $result = $query->as_array();

            $stats['incorrect'] = isset($result[0]['incorrect_count']) ? $result[0]['incorrect_count'] : 0;
            
            return $stats;
        }
        
        public function action_check() {
            $return = array(
                'indexes' => array(),
                'stats' => array(),
            );
            
            $guess = $this->request->param('guess');
            $word = Session::instance()->get('word');
            
            foreach (UTF8::str_split($word) as $index => $letter)
            {
                if($letter === $guess)
                {
                    $return['indexes'][] = $index;
                }
            }
                       
            $guess_stats = ORM::factory('guess');
            $guess_stats->letter = $guess;
            $guess_stats->time = time();
            
            if(count($return['indexes']))
            {    
                $guess_stats->correct = 1;
            }
            else {
                $guess_stats->correct = 0;
            }
            
            $guess_stats->save();
            
            $return['stats'] = $this->stats();
            
            die(json_encode($return));
        }
} // End Hangman
