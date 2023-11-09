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
		return ['\mober\discordnotifications\migrations\extension_installation'];
	}

	/**
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'discord_webhooks' => [
					'COLUMNS' => [
						'alias'	=> [
							'VCHAR:255',
							'',
						],
						'url' => [
							'VCHAR:255',
							'',
						],
					],
					'PRIMARY_KEY' => 'alias',
				],
			],
		];
	}

	/**
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'discord_webhooks',
			],
		];
	}
}
