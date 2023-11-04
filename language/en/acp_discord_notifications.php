<?php
/**
 * Discord Notifications extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Tyler Olsen, https://github.com/rootslinux
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * This file contains the language strings for the ACP settings page for this extension.
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// ACP Extension Settings Page
	'DN_ACP_DESCRIPTION'			=> 'These settings allow for various forum events to send notification messages to a Discord server.',
	'DN_MISSING_CURL'				=> 'Please install the <a href="https://www.php.net/manual/en/curl.installation.php">ext-curl module for PHP</a>, otherwise this extension will not work.',

	'DN_MAIN_SETTINGS'				=> 'Main Settings',
	'DN_MASTER_ENABLE'				=> 'Enable Discord Notifications',
	'DN_POST_PREVIEW_LENGTH'		=> 'Post Preview Length',
	'DN_POST_PREVIEW_DESCRIPTION'	=> 'Specify the number of characters to display in a post preview (10 - 2000). A zero value disables post preview.',
	'DN_TEST_MESSAGE'				=> 'Message',
	'DN_TEST_MESSAGE_TEXT'			=> 'This is a test: Hello Discord!',
	'DN_TEST_DESCRIPTION'			=> 'A message to send to Discord to verify that the connection with phpBB is functioning.',
	'DN_SEND_TEST'					=> 'Send Test Message',
	'DN_SEND_TEST_DESCRIPTION'		=> 'Sends the contents of the test message to the Discord webhook. Use this to verify that your webhook is properly functioning.',
	'DN_CONNECT_TIMEOUT'			=> 'Connect timeout',
	'DN_CONNECT_TO_DESCRIPTION'		=> 'Timeout in seconds to establish a connection to the Webhook endpoint.',
	'DN_EXEC_TIMEOUT'				=> 'Transfer timeout',
	'DN_EXEC_TO_DESCRIPTION'		=> 'Timeout in seconds for data transfer.',
	'DN_TEST_SETTINGS'				=> 'Test Settings',

	'DN_WEBHOOK_SETTINGS'			=> 'Edit entries',
	'DN_WEBHOOK_SETTINGS_DESCRIPTION' => 'Edit or delete existing entries.',
	'DN_WEBHOOK_NEW'				=> 'Create new entry',
	'DN_WEBHOOK_NEW_DESCRIPTION'	=> 'Create a new entry. A full URL is required, starting with "http" or "https".',
	'DN_WEBHOOK_NEW_ALIAS'			=> 'New Alias',
	'DN_WEBHOOK_NEW_ALIAS_DESCRIPTION' => 'Can be freely chosen, for example something that describes the target channel of the webhook (like "public" or "marketplace").',
	'DN_WEBHOOK_NEW_URL'			=> 'Discord Webhook URL',
	'DN_WEBHOOK_NEW_URL_DESCRIPTION'=> 'The URL of the webhook generated by the Discord server. See <a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks">this article</a> to learn how to create a new webhook.',
	'DN_WEBHOOK_SELECT'				=> 'Select Webhook',
	'DN_WEBHOOK_DEFAULT'			=> 'Global Events',
	'DN_WEBHOOK_DEFAULT_DESCRIPTION' => 'Webhook for global events not related to a specific forum, e.g. user creation/deletion',
	'DN_NO_WEBHOOKS'				=> 'No entries exist.',

	'DN_TYPE_SETTINGS'				=> 'Notification Types',
	'DN_TYPE_DESCRIPTION'			=> 'Select which types of notifications should generate messages that get sent to Discord',
	'DN_POST_CREATE'				=> 'Post created',
	'DN_POST_UPDATE'				=> 'Post updated',
	'DN_POST_DELETE'				=> 'Post deleted',
	'DN_POST_LOCK'					=> 'Post locked',
	'DN_POST_UNLOCK'				=> 'Post unlocked',
	'DN_POST_APPROVE'				=> 'Post approved',
	'DN_TOPIC_CREATE'				=> 'Topic created',
	'DN_TOPIC_UPDATE'				=> 'Topic updated',
	'DN_TOPIC_DELETE'				=> 'Topic deleted',
	'DN_TOPIC_LOCK'					=> 'Topic locked',
	'DN_TOPIC_UNLOCK'				=> 'Topic unlocked',
	'DN_TOPIC_APPROVE'				=> 'Topic approved',
	'DN_USER_CREATE'				=> 'User created',
	'DN_USER_DELETE'				=> 'User deleted',

	'DN_FORUM_SETTINGS'				=> 'Notification Forums',
	'DN_FORUM_DESCRIPTION'			=> 'Select which forums should generate notifications for forum-specific events, such as those related to posts and topics',

	// Messages that appear after a user tries to send a test message
	'DN_TEST_SUCCESS'				=> 'Success! Check your Discord server and you should see the test message displayed there.',
	'DN_TEST_FAILURE'				=> 'There was a problem with sending the test message. Check your webhook URL and verify your server meets the extension requirements.',
	'DN_TEST_BAD_MESSAGE'			=> 'Please enter at least one character for the test message',
	'DN_TEST_BAD_WEBHOOK'			=> 'The text entered for the webhook URL is not a valid URL. Please check this setting and try again.',

	// Success/Failure messages that can be generated once the user saves
	'DN_SETTINGS_SAVED'				=> 'Discord Notification settings modified successfully.',
	'DN_WEBHOOK_URL_INVALID'		=> 'Discord webhook URL must be a full and valid URL.',
	'DN_POST_PREVIEW_INVALID'		=> 'Post preview length must be a number between 10 and 2000, or 0 to disable previews.',
));
