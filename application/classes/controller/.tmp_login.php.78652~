<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller {

	public $a1;
	
	public function before()
	{
		$this->a1 = A1::instance();
	}
	
	public function action_show()
	{
		$this->request->response = View::factory('login')->render();
	}
	
	public function action_check()
	{
		$this->a1->login($_POST['username'], $_POST['password']);
		$this->request->redirect('');
	}
	
	public function action_logout()
	{
		$this->a1->logout();
		$this->request->redirect('');
	}
	
	public function after()
	{
		
	}
	
} // End Login
