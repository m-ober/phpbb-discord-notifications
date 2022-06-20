<?php
/**
 * Discord Notifications extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Tyler Olsen, https://github.com/rootslinux
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mober\discordnotifications\migrations;

class create_tables extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\mober\discordnotifications\migrations\extension_installation');
	}

	/**
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return array(
			'add_tables' => array (
				$this->table_prefix . 'discord_webhooks' => array (
					'COLUMNS' => array (
						'discord_webhook_id' => array (
							'UINT',
							NULL,
							'auto_increment'
						),
						'alias' => array (
							'VCHAR:255',
							''
						),
						'url' => array (
							'VCHAR:255',
							''
						),
					),
					'PRIMARY_KEY' => 'discord_webhook_id',
					'alias'	=> array('INDEX', 'alias'),
				),
				$this->table_prefix . 'discord_webhooks_forums' => array (
					'COLUMNS' => array (
						'discord_webhook_forums_id' => array (
							'UINT',
							NULL,
							'auto_increment'
						),
						'discord_webhook_id' => array (
							'UINT',
							0
						),
						'forum_id' => array (
							'UINT',
							0
						),
					),
					'PRIMARY_KEY' => 'discord_webhook_forums_id',
					'discord_webhook_id'	=> array('INDEX', 'discord_webhook_id'),
					'forum_id'	=> array('INDEX', 'forum_id'),
				),
			),
		);
	}

	/**
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return array (
			'drop_tables' => array (
				$this->table_prefix . 'discord_webhooks',
				$this->table_prefix . 'discord_webhooks_forums'
			)
		);
	}
}
