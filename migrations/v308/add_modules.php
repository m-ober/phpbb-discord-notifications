<?php

namespace mober\discordnotifications\migrations\v308;

class add_modules extends \phpbb\db\migration\container_aware_migration
{
	static public function depends_on()
	{
		return ['\mober\discordnotifications\migrations\extension_installation'];
	}

	public function effectively_installed()
	{
		/** @var \phpbb\db\migration\tool\module $module_manager */
		$module_manager = $this->container->get('migrator.tool.module');
		return $module_manager->exists('acp', 'ACP_DISCORD_NOTIFICATIONS', 'ACP_DISCORD_NOTIFICATIONS_SETTINGS');
	}

	public function update_data()
	{
		return [
			['module.remove', [
				'acp',
				'ACP_DISCORD_NOTIFICATIONS',
				[
					'module_langname' => 'ACP_DISCORD_NOTIFICATIONS_TITLE',
				],
			]],
			['module.add', [
				'acp',
				'ACP_DISCORD_NOTIFICATIONS',
				[
					'module_basename'	=> '\mober\discordnotifications\acp\discord_notifications_module',
					'modes'				=> [
						'settings',
						'webhooks',
						'mapping',
					],
				],
			]],
		];
	}
}
