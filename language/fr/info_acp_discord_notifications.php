<?php
/**
 *
 * Discord Notifications extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Tyler Olsen, https://github.com/rootslinux
 * @copyright (c) 2022, French translation by Fred Rimbert https://forums.caforum.fr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * This file contains the language strings for the ACP settings page for this extension.
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	// ACP Module
	'ACP_DISCORD_NOTIFICATIONS'					=> 'Notifications Discord',
	'ACP_DISCORD_NOTIFICATIONS_SETTINGS'		=> 'Paramètres',
	'ACP_DISCORD_NOTIFICATIONS_WEBHOOKS'		=> 'Webhooks',
	'ACP_DISCORD_NOTIFICATIONS_MAPPING'			=> 'Mapping',

	// ACP Logs
	'ACP_DISCORD_NOTIFICATIONS_LOG_UPDATE'		=> 'Paramètres des notifications Discord modifiées',
	'ACP_DISCORD_NOTIFICATIONS_WEBHOOK_ERROR'	=> 'Discord Webhook a renvoyé le code d‘état HTTP %d',
	'ACP_DISCORD_NOTIFICATIONS_CURL_ERROR'		=> 'Code d‘erreur Discord Webhook cURL %d',
	'ACP_DISCORD_NOTIFICATIONS_JSON_ERROR'		=> 'Erreur d‘encodage Discord JSON : %s',
));
