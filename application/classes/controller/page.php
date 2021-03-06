<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller {

	public function action_load($page)
	{		
                if($page == 'welcome')
                {
                    $this->request->response = Request::factory('hangman')->execute();
                }
                else
                {
                    $page = ORM::factory('page')->where('path', '=', $page)->find();
                    $content = $page->content;
                    $parent = $page->parent;
                    $children = $page->children;
                    $this->request->response = View::factory('content')
                            ->bind('parent', $parent)
                            ->bind('children', $children)
                            ->bind('content', $content)
                            ->render();
                }
	}
	
} // End Page
