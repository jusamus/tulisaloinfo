<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Website extends Controller
{
	public $view, $header, $menu, $current_page, $content, $footer;
	public $auto_render = TRUE;
	
	public function before()
	{
		$this->view = View::factory('website')
			->bind('header', $this->header)
			->bind('menu', $this->menu)
			->bind('content', $this->content)
			->bind('news', $this->news)
			->bind('footer', $this->footer)
                        ->bind('current_page', $this->current_page);		
		
		$proverb = Request::factory('/proverb')->execute();
                
		$this->header = View::factory('header')
			->bind('proverb', $proverb);
					
		$menuitems = ORM::factory('page')
			->select('path','title')
			->where('parent_id','=',0)
			->order_by('id')		
			->find_all();
                
		$this->menu = View::factory('menu')
			->bind('menuitems', $menuitems)
			->bind('current_page', $this->current_page);
					
		$this->news = Request::factory('/news')->execute();
                
		$segments = explode('/', $this->request->uri);
                
		$this->current_page = $segments[0] ? $segments[0] : 'welcome';
                
                if($this->current_page == 'welcome')
                {
                    $this->content = Request::factory('/hangman')->execute();
                }
                else
                {
                    $this->content = Request::factory('/page/'.$this->request->uri)->execute();
                }

		$this->footer = View::factory('footer');
                
	}
	
	public function action_index()
	{
		
	}
	
	public function after()
	{
		if($this->auto_render === TRUE)
		{
			$this->request->response = $this->view->render();
		}		
	}
} // End Website