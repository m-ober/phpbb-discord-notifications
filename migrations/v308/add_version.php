<?php

namespace mober\discordnotifications\migrations\v308;

class add_version extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\mober\discordnotifications\migrations\extension_installation'];
	}

	public function update_data()
	{
		return [
			['config.add', ['discord_notification_version', '3.0.8']],
		];
	}
}
