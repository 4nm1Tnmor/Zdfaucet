<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {
        $skip = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 0;

        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination_config');

        $this->data['page'] = 'Users';

        $this->data['users'] = $this->m_admin->get_users($skip);
        $config['base_url'] = site_url('admin/users/');
        $config['total_rows'] = $this->m_admin->count_all_users();

        $config['page_query_string'] = TRUE;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->render('users', $this->data);
    }
    public function find()
    {
        $this->data['page'] = 'Users';
        $user = $this->db->escape_str($this->input->post('user'));
        $type = $this->input->post('type');
        if ($this->input->get('ip') != NULL) {
            $type = 'ip';
            $user = $this->input->get('ip');
        }

        switch ($type) {
            case 'id':
                $this->data['users'] = $this->m_admin->getUserFromId($user);
                break;
            case 'username':
                $this->data['users'] = $this->m_admin->getUserFromUsername($user);
                break;
            case 'email':
                $this->data['users'] = $this->m_admin->getUserFromEmail($user);
                break;
            case 'ip':
                $this->data['users'] = $this->m_admin->getUserFromIp($user);
                break;
            default:
                $this->data['users'] = $this->m_admin->getUserFromId($user);
        };
        $this->data['pagination'] = '';
        $this->render('users', $this->data);
    }
    public function details($id = 0)
    {

        $this->data['page'] = 'User Detail';

        $this->data['user'] = $this->m_admin->get_user($id);
        $this->data['referrals'] = $this->m_admin->get_referrals($id);

        $this->data['faucet_history'] = $this->m_account->get_faucet_history($id);
        $this->data['shortlinks_history'] = $this->m_account->get_shortlinks_history($id);
        $this->data['ptc_history'] = $this->m_account->get_ptc_history($id);
        $this->data['offerwall_history'] = $this->m_account->get_offerwall_history($this->data['user']['id']);
        $this->data['withdrawals_history'] = $this->m_account->get_withdrawals_history($id);
        $this->data['deposit_history'] = $this->m_deposit->getDepositByUser($id);
        $this->data['countLotteries'] = $this->m_admin->countLotteries($id);
        $this->data['countReferrals'] = $this->m_admin->countReferrals($id);
        $this->data['logs'] = $this->m_admin->cheatLogs($id);
        $this->render('user_detail', $this->data);
    }
    public function send_message($userId)
    {
        $content = $this->db->escape_str($this->input->post('message'));
        $this->m_core->addNotification($userId, $content, 3);
        redirect(site_url('admin/users/details/' . $userId));
    }
    public function update($userId)
    {
        $email = $this->db->escape_str($this->input->post('email'));
        $username = $this->db->escape_str($this->input->post('username'));
        $ipAddress = $this->db->escape_str($this->input->post('ip_address'));
        $exp = $this->db->escape_str($this->input->post('exp'));
        $level = $this->db->escape_str($this->input->post('level'));
        $referredBy = $this->db->escape_str($this->input->post('referred_by'));
        $verified = $this->db->escape_str($this->input->post('verified'));
        $wallet = $this->db->escape_str($this->input->post('wallet'));
        $balance = $this->db->escape_str($this->input->post('balance'));
        $depBalance = $this->db->escape_str($this->input->post('dep_balance'));
        $energy = $this->db->escape_str($this->input->post('energy'));
        $faucetCount = $this->db->escape_str($this->input->post('faucet_count'));
        $shortlinkCount = $this->db->escape_str($this->input->post('shortlink_count'));
        $offerwallCount = $this->db->escape_str($this->input->post('offerwall_count'));
        $refCount = $this->db->escape_str($this->input->post('ref_count'));
        $totalEarned = $this->db->escape_str($this->input->post('total_earned'));

        $this->m_admin->updateUser($userId, $email, $username, $ipAddress, $exp, $level, $referredBy, $verified, $wallet, $balance, $depBalance, $energy, $faucetCount, $shortlinkCount, $offerwallCount, $refCount, $totalEarned);
        redirect(site_url('admin/users/details/' . $userId));
    }
    public function same_password()
    {
        $skip = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 0;

        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination_config');

        $this->data['page'] = 'Users have same password';
        $this->data['fraudContent'] = 'Password hash';

        $this->data['users'] = [];
        $passwords = $this->m_admin->getSamePassword($skip);
        foreach ($passwords as $password) {
            $this->data['users'][$password['password']] = $this->m_admin->getUsersByPassword($password['password']);
        }
        $config['base_url'] = site_url('admin/same_password/');
        $config['total_rows'] = $this->m_admin->countSamePassword();

        $config['page_query_string'] = TRUE;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->render('frauds', $this->data);
    }

    public function same_ip()
    {
        $skip = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 0;
        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination_config');

        $this->data['page'] = 'Users have same IP Address';
        $this->data['fraudContent'] = 'IP Address';

        $this->data['users'] = [];
        $ipAddresses = $this->m_admin->getSameIpAddress($skip);
        foreach ($ipAddresses as $ipAddress) {
            $this->data['users'][$ipAddress['ip_address']] = $this->m_admin->getUsersByIpAddress($ipAddress['ip_address']);
        }
        $config['base_url'] = site_url('admin/same_ip/');
        $config['total_rows'] = $this->m_admin->countSameIpAddress();

        $config['page_query_string'] = TRUE;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->render('frauds', $this->data);
    }

    public function country()
    {
        $isocode = $this->input->get('iso');
        $skip = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 0;
        $isocode = strtolower(($isocode));
        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination_config');

        $this->data['page'] = 'Users by country';
        $this->data['fraudContent'] = 'IP Address';

        if ($isocode) {
            $this->data['users'] = $this->m_admin->getUsersByCountry($isocode, $skip);
            $config['base_url'] = site_url('admin/users/country/?iso=' . $isocode);
            $config['total_rows'] = $this->m_admin->countUsersByCountry($isocode);

            $config['page_query_string'] = TRUE;
            $this->pagination->initialize($config);
            $this->data['pagination'] = $this->pagination->create_links();
        } else {
            $this->data['users'] = [];
            $this->data['pagination'] = '';
        }

        $this->render('country', $this->data);
    }
    public function ban($id)
    {
        $id = $this->db->escape_str($id);
        $reason = $this->input->post('reason');
        if ($reason == NULL) {
            $reason = 'Banned by admin!';
        }
        $this->m_core->insertCheatLog($id, $reason, 0, 'Not available');
        redirect(site_url('admin/users/details/' . $id));
    }
    public function unban($id)
    {
        $id = $this->db->escape_str($id);
        $this->db->delete('cheat_logs', array('user_id' => $id));
        redirect(site_url('admin/users/details/' . $id));
    }
    public function active($id)
    {
        $id = $this->db->escape_str($id);
        $this->m_admin->active($id);
        return redirect(site_url('/admin/users/details/' . $id));
    }

    public function delete_logs($id)
    {
        $this->db->set('ip_address', 'removed');
        $this->db->where('id', $id);
        $this->db->update('users');

        $this->db->delete('faucet_history', array('user_id' => $id));
        $this->db->delete('link_history', array('user_id' => $id));
        $this->db->delete('ptc_history', array('user_id' => $id));

        $this->db->delete('cheat_logs', array('user_id' => $id));

        redirect(site_url('admin/users/details/' . $id));
    }

    public function banned()
    {
        $skip = (isset($_GET['per_page']) && is_numeric($_GET['per_page'])) ? $_GET['per_page'] : 0;

        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination_config');
        $config['base_url'] = site_url('admin/users/banned');
        $config['total_rows'] = $this->m_admin->count_banned_users();
        $config['page_query_string'] = TRUE;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['page'] = 'Banned Users';
        $this->data['users'] = $this->m_admin->get_banned_users($skip);
        $this->render('users', $this->data);
    }

    public function logs($id = 0)
    {
        $this->data['page'] = 'Relate Logs';
        $this->data['logs'] = $this->m_admin->getSameLogs($id);
        $this->render('logs', $this->data);
    }
}
