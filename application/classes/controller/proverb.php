<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Proverb extends Controller {

	public function action_generate()
	{
		$proverb = ORM::factory('proverb')->get_random();
		$this->request->response = View::factory('proverb')
			->bind('proverb', $proverb)
			->render();
	}
	
	public function action_save()
	{		
		$proverb = ORM::factory('savedproverb');
		$proverb->content = $_POST['content'];
		$proverb->author = $_POST['author'];
		$proverb->date = time();
		$proverb->save();
	}
	
	public function action_load()
	{
		
	}
	
} // End Proverb
