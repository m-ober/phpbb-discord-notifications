<?php
namespace mober\discordnotifications\migrations;

class add_modules extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\mober\discordnotifications\migrations\extension_installation');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_DISCORD_NOTIFICATIONS',
				array(
					'module_basename'	=> '\mober\discordnotifications\acp\discord_notifications_module',
					'modes'				=> array('webhooks', 'mapping')
				)
			))
		);
	}
}
