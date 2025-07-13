<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	// ✅ Pre-declare all commonly loaded classes to avoid PHP 8.2+ dynamic property warnings
	public $benchmark;
	public $config;
	public $db;
	public $email;
	public $form_validation;
	public $hooks;
	public $input;
	public $lang;
	public $load;
	public $output;
	public $pagination;
	public $router;
	public $security;
	public $session;
	public $uri;
	public $user_agent;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();

		log_message('info', 'Controller Class Initialized');
	}

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
}
