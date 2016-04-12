<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Element\Captcha;
use Zend\Captcha\Figlet as CaptchaFiglet;
use Application\Model\Post;
use Zend\Db\Adapter\Adapter as Adap;

class IndexController extends AbstractActionController
{
	protected $postTable;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function leftPostAction()
    {
    	$captcha = new CaptchaFiglet(
    		array(
		    	'name' => 'Verify Post',
		   		'wordLen' => 5,
		    	'timeout' => 300,
			)
    	);

		$id = $captcha->generate();
		

		return array(
			'capcha' => $captcha->getFiglet()->render($captcha->getWord()),
			'errors' => $this->params()->fromQuery('errors')
		);
    }

    public function savePostAction()
    {
    	$userName = $this->getRequest()->getPost('user_name');
    	$userEmail = $this->getRequest()->getPost('user_email');
    	$userWebsite = $this->getRequest()->getPost('user_website');
    	$message = $this->getRequest()->getPost('message');
    	$userIP = $this->getRequest()->getServer('REMOTE_ADDR');
    	$userAgent = $this->getRequest()->getServer('HTTP_USER_AGENT');

    	$postAssoc =  
    		array(
    			'userName' => $userName, 
    			'userEmail' => $userEmail, 
    			'userWebsite' => $userWebsite, 
    			'message' => $message, 
    			'userAgent' => $userAgent, 
    			'userIP' => $userIP, 

    		);

    	$postObject = new Post($userEmail, $userName, $message, $userIP, $userAgent, $userWebsite);
    	$postTable = $this->getPostTable();

		if(!empty($postObject->getErrors()))
		{
			return $this->redirect()->toRoute('leftPost', array(), array(
				'query' => array(
					'errors' => $postObject->getErrors()
				)
			));
		}

		$postId = $postTable->savePost($postObject);

		$this->redirect()->toUrl("/thank-you/$postId");

    }

    public function postListAction() 
    {
		$page = $this->params('page');

		$prevPage = false;
		$nextPage = false;

		if (0 == (int)$page) {
			$page = 1;
		}

		$postTable = $this->getPostTable();
		$posts = $postTable->fetchAll(25, ($page-1)*25);

		if ($page > 1) {
			$prevPage = $page - 1;
		}

		if (count($posts) == 25) {
			$nextPage = $page + 1;
		}

		return array(
			'posts' => $posts,
			'prevPage' => $prevPage,
			'nextPage' => $nextPage,
		);
    }

	public function thankYouAction()
	{
		$postId = $this->params('post');
		if(0 != (int)$postId) {
			return array(
				'post' => $this->getPostTable()->getPost($postId)
			);
		}
	}

    public function getPostTable()
	{
	    if (!$this->postTable) {
            $sm = $this->getServiceLocator();
            $this->postTable = $sm->get('Application\Model\PostTable');
        }
        
        return $this->postTable;
	}
}
