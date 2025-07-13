<?php
defined('BASEPATH') or exit('No direct script access allowed');
class M_Faucet extends CI_Model
{

	public function check_history($timer)
	{
		$find = $this->db->get_where('faucet_history', array('ip_address' => $this->input->ip_address(), 'claim_time>' => time() - $timer));
		return ($find->num_rows() == 0);
	}

	public function countHistory($id)
	{
		$today_midnight = strtotime('today midnight');
		$ip_address = $this->input->ip_address();
		$count_claim = $this->db->query('SELECT COUNT(id) AS cnt FROM faucet_history WHERE (ip_address = "' . $ip_address . '" OR user_id = ' . $id . ') AND claim_time > ' . $today_midnight)->result_array()[0]['cnt'];
		return $count_claim;
	}

	public function check_limit($limit, $id)
	{
		$count_claim = $this->countHistory($id);
		return $count_claim < $limit;
	}

	public function reduce_energy($id)
	{
		$this->db->where('id', $id);
		$this->db->set('energy', 'energy-10', FALSE);
		$this->db->update('users');
	}

	public function insert_history($id, $amount)
	{
		$insert = array(
			'user_id' => $id,
			'ip_address' => $this->input->ip_address(),
			'amount' => $amount,
			'claim_time' => time()
		);
		$this->db->insert('faucet_history', $insert);
	}
	public function update_user($id, $amount)
	{
		$this->db->where('id', $id);
		$this->db->set('balance', 'balance+' . $amount, FALSE);
		$this->db->set('total_earned', 'total_earned+' . $amount, FALSE);
		$this->db->set('faucet_count', 'faucet_count+1', FALSE);
		$this->db->set('faucet_count_tmp', 'faucet_count_tmp+1', FALSE);
		$this->db->set('last_claim', time());
		$this->db->set('last_active', time());
		$this->db->set('token', random_string('alnum', 30));
		$this->db->update('users');
	}
	public function findCheaters($userId)
	{
		$past = time() - 86400;
		$ip = $this->input->ip_address();
		$find = $this->db->query("SELECT COUNT(*) as cnt FROM faucet_history WHERE claim_time>" . $past . " AND ip_address = '" . $ip . "' AND user_id<>" . $userId)->result_array()[0]['cnt'];
		if ($find > 0) {
			$this->db->query("UPDATE users SET status='Multiple Accounts on Faucet' WHERE id IN (SELECT user_id FROM faucet_history WHERE claim_time>" . $past . " AND ip_address = '" . $ip . "')");
		}
	}
}
