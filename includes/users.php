<?php
/**
* @package ivacuum.ru
* @copyright (c) 2012
*/

namespace app;

use app\models\page;

/**
* Пользователи
*/
class users extends page
{
	public function index()
	{
		trigger_error('NOT_IMPLEMENTED');
	}
	
	/**
	* Просмотр профиля
	*/
	public function profile($user = false)
	{
		if( !$user )
		{
			trigger_error('PAGE_NOT_FOUND');
		}
		
		$u = '';
		$uid = 0;
		
		if( ((string)(int) $user) === ((string) $user) )
		{
			/* Числовой идентификатор */
			$uid = (int) $user;
		}
		else
		{
			/* Символьное имя */
			$u = $user;
		}
		
		/**
		* Получение данных пользователя
		*/
		if( !($uid && $uid == $this->user['user_id']) && !($u && $u == $this->user['user_url']) )
		{
			$sql = '
				SELECT
					*
				FROM
					' . USERS_TABLE . '
				WHERE
					' . (($u) ? 'user_url = ' . $this->db->check_value($u) : 'user_id = ' . $this->db->check_value($uid));
			$this->db->query($sql);
			$row = $this->db->fetchrow();
			$this->db->freeresult();
		}
		else
		{
			/* Просмотр своего профиля */
			$row = $this->user->data;
		}
		
		if( !$row )
		{
			trigger_error('PAGE_NOT_FOUND');
		}
		
		navigation_link($this->user_profile_link('raw', $row['username'], false, $row['user_url'], $row['user_id']), $row['username'], 'card_address');
		
		/* Загрузка званий */
		$ranks = $this->cache->obtain_ranks();
		
		$this->template->assign(array(
			'COMMENTS'   => $row['user_posts'],
			'FROM'       => $row['user_from'],
			'INTERESTS'  => $row['user_interests'],
			'ICQ'        => number_format(intval($row['user_icq']), 0, '.', '-'),
			'IP'         => $row['user_ip'],
			'JID'        => $row['user_jid'],
			'LASTVISIT'  => ( $row['user_session_time'] ) ? $this->user->create_date($row['user_session_time']) : '',
			'OCCUPATION' => $row['user_occ'],
			'ONLINE'     => $this->user->ctime - $row['user_session_time'] < $this->config['load_online_time'],
			
			'RANK_IMG'   => isset($ranks[$row['user_rank']]['rank_image']) ? $ranks[$row['user_rank']]['rank_image'] : '',
			'RANK_TITLE' => isset($ranks[$row['user_rank']]['rank_title']) ? $ranks[$row['user_rank']]['rank_title'] : '',
			'REGDATE'    => $this->user->create_date($row['user_regdate']),
			'USERNAME'   => $this->user_profile_link('plain', $row['username'], $row['user_colour'])
		));
	}
}
