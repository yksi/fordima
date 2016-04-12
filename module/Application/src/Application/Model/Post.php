<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class Post
{
	public $id;
	public $email;
	public $name;
	public $website;
	public $message;
	public $ip;
	public $agent;


	public function exchangeArray($data)
     {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
     	$this->name = (!empty($data['name'])) ? $data['name'] : null;
		$this->email = (!empty($data['email'])) ? $data['email'] : null;
		$this->message = (!empty($data['message'])) ? $data['message'] : null;
		$this->ip = (!empty($data['ip'])) ? $data['ip'] : null;
		$this->agent = (!empty($data['agent'])) ? $data['agent'] : null;
		$this->website = (!empty($data['website'])) ? $data['website'] : null;
     }

	public function __construct($email = null, $name = null, $message = null, $ip = null, $agent = null, $website = null) 
	{
		$this->name = $name;
		$this->email = $email;
		$this->message = $message;
		$this->ip = $ip;
		$this->agent = $agent;
		$this->website = $website;
	}

	public function getErrors()
	{
        $errors = array();

        if (!$this->verifyName()) {
            $errors['name-error'] = "Name is invalid.";
        }

        if (!$this->verifyEmail()) {
            $errors['email-error'] = "Email is invalid.";
        }

        if (!$this->verifyMessage()) {
            $errors['message-error'] = "Message cannot be empty and contains html tags.";
        }

        return $errors;
	}

	private function verifyName()
	{
		$patternMatches = preg_match('/^[\w\.\-\s@+]+$/u', $this->name);
		return $patternMatches && !!$this->name;
	}

    private function verifyMessage()
    {
        $patternMatches = preg_match('/^[\w\.\-\s@+]+$/u', $this->message);
        return strlen($this->message) == strlen(strip_tags($this->message)) && !!$this->message;
    }

    private function verifyEmail()
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) && !!$this->email;
    }
} 