<?php defined('SYSPATH') or die('No direct script access.');

class Controller_News extends Controller {
	
	private $items;
	
	public function action_pages()
	{		
		$this->items = ORM::factory('page')
			->order_by('modified', 'DESC')
			->limit(5)
			->find_all();	
	}
	
	public function action_proverbs()
	{		
		$this->items = ORM::factory('savedproverb')
			->order_by('date', 'DESC')
			->limit(5)
			->find_all();		
	}
		
	public function after()
	{
		$type = $this->request->action;
		$this->request->response = View::factory('news/'.$type)
			->bind('items', $this->items)
			->bind('title', $this->title)
			->bind('type', $type)
			->render();		
	}
	
} // End News
