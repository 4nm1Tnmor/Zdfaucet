<?php defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->data = array();

		$this->data['settings'] = $this->m_core->getSettings();
		$this->data['csrf_name'] = $this->security->get_csrf_token_name();
		$this->data['csrf_hash'] = $this->security->get_csrf_hash();
	}
}
class Guess_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->data['settings']['status'] == 'off' && current_url() != site_url('maintenance')) {
			return redirect(site_url('maintenance'));
		}
	}

	protected function render($template, $data)
	{
		$contents = $this->load->view('user_template/' . $template, $data, TRUE);
		$data['contents'] = $contents;
		$this->load->view('user_template/template', $data);
	}
}
class Admin_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('m_admin', 'm_links', 'm_account', 'm_deposit'));
		$this->load->helper('date');
		

		if (current_url() != site_url('admin') && current_url() != site_url('admin/auth/login')) {
			if($this->session->admin == NULL){
			 return redirect(site_url('admin'));
			}
		} else if($this->session->admin != NULL) {
			return redirect(site_url('admin/overview'));
		}
	}

	protected function render($template, $data) 
	{
		$contents = $this->load->view('admin_template/' . $template, $data, TRUE);
		$data['contents'] = $contents;
		$this->load->view('admin_template/template', $data);
	}
}

class Member_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->data['settings']['status'] == 'off') {
			return redirect(site_url('maintenance'));
		}
		if ($this->session->vUID== NULL || !is_numeric($this->session->vUID)) {
			session_destroy();
			return redirect(site_url());
		}
		$this->data['pages'] = $this->m_core->getPages();

		$userId = $this->db->escape_str($this->session->vUID);
		$user = $this->m_core->get_user_from_id($userId);
		// if (!$user || $user['ip_address'] != $this->input->ip_address()) {
		// 	session_destroy();
		// 	return redirect(site_url());
		// }

		$this->data['user'] = $user;
		$this->data['notifications'] = $this->m_core->getNotifications($userId);
		$this->data['countAvailableTasks']=$this->m_core->countAvailableTasks($userId);
		$this->data['countUnreadNotification']=$this->m_core->countUnreadNotifications($userId);
		$this->data['countAvailableLinks'] = $this->m_core->countAllLinksView() - $this->m_core->countLinkHistory($user['id']);
		$this->data['countAvailableAds'] = $this->m_core->countAvailableAds($user['id']);
	}

	protected function render($template, $data)
	{
		$contents = $this->load->view('user_template/' . $template, $data, TRUE);
		$data['contents'] = $contents;
		$this->load->view('user_template/template', $data);
	}
}
