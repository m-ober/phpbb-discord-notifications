<?php
/**
 * Discord Notifications extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Tyler Olsen, https://github.com/rootslinux
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mober\discordnotifications\migrations;

class add_language_config extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\mober\discordnotifications\migrations\extension_installation');
	}

	public function effectively_installed()
	{
		return isset($this->config['discord_notifications_default_lang']);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('discord_notifications_default_lang', ''))
		);
	}
}
