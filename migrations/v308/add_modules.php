<?php

namespace mober\discordnotifications\migrations\v308;

class add_modules extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\mober\discordnotifications\migrations\extension_installation'];
	}

	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_DISCORD_NOTIFICATIONS',
				[
					'module_basename'	=> '\mober\discordnotifications\acp\discord_notifications_module',
					'modes'				=> [
						'webhooks',
						'mapping',
					],
				],
			]],
			['custom', [
				[$this, 'rename_settings_module']
			]],
		];
	}

	public function rename_settings_module()
	{
		$sql = 'UPDATE ' . MODULES_TABLE . "
			SET module_langname = 'ACP_DISCORD_NOTIFICATIONS_SETTINGS'
			WHERE module_langname = 'ACP_DISCORD_NOTIFICATIONS_TITLE'";
		$this->sql_query($sql);
	}
}
