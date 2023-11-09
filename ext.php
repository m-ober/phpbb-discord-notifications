<?php

namespace mober\discordnotifications;

class ext extends \phpbb\extension\base
{
	/**
	 * {@inheritDoc}
	 */
	public function is_enableable()
	{
		$language = $this->container->get('language');
		$language->add_lang('acp_discord_notifications', 'mober/discordnotifications');

		$curl_available = extension_loaded('curl');
		if (!$curl_available)
		{
			return $language->lang('DN_MISSING_CURL');
		}

		return true;
	}
}
