<?php
/**
 * Discord Notifications extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Tyler Olsen, https://github.com/rootslinux
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mober\discordnotifications\acp;

/**
 * Discord Notifications ACP module.
 */
class discord_notifications_module
{
	// The minimum number of characters that the user can set for the post preview setting.
	// Note that a zero value is valid and disables previews.
	const MIN_POST_PREVIEW_LENGTH = 10;

	// The maximum number of characters that the user can set for the post preview setting.
	// Note that this is slightly less than the actual allowed maximum by Discord (2048), but we reserve
	// some space to prepend the preview text with something like "Preview: " or "Reason: "
	const MAX_POST_PREVIEW_LENGTH = 2000;

	// The name for the form used for this page
	const PAGE_FORM_NAME = 'acp_mober_discord_notifications';

	/** @var string */
	public $page_title;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $u_action;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var bool */
	private  $curl_available;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->cache = $phpbb_container->get('cache.driver');
		$this->config = $phpbb_container->get('config');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->language = $phpbb_container->get('language');
		$this->log = $phpbb_container->get('log');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');
		$this->curl_available = extension_loaded('curl');

		$this->language->add_lang('acp_discord_notifications', 'mober/discordnotifications');
		$this->page_title = $this->language->lang('ACP_DISCORD_NOTIFICATIONS');

		add_form_key(self::PAGE_FORM_NAME);

		switch ($mode)
		{
			case 'webhooks':
				$this->tpl_name = 'acp_discord_notifications_webhooks';

				if ($this->request->is_set_post('action_send_test_message'))
				{
					$this->process_send_test_message();
				}
				else if ($this->request->is_set_post('action_delete_alias'))
				{
					$this->process_delete_alias();
				}
				else if ($this->request->is_set_post('submit'))
				{
					$this->process_webhooks_form_submit();
				}

				$this->generate_webhook_section();
				break;

			case 'mapping':
				$this->tpl_name = 'acp_discord_notifications_mapping';

				if ($this->request->is_set_post('submit'))
				{
					$this->process_mapping_form_submit();
				}

				$this->generate_webhook_section();
				$this->generate_forum_section();
				break;

			case 'settings':
			default:
				$this->tpl_name = 'acp_discord_notifications_settings';

				if ($this->request->is_set_post('submit'))
				{
					$this->process_settings_form_submit();
				}
				break;
		}

		// Assign template values so that the page reflects the state of the extension settings
		$this->template->assign_vars([
			'DN_MASTER_ENABLE'			=> $this->config['discord_notifications_enabled'],
			'DN_POST_PREVIEW_LENGTH'	=> $this->config['discord_notifications_post_preview_length'],
			'DN_TEST_MESSAGE_TEXT'		=> $this->language->lang('DN_TEST_MESSAGE_TEXT'),
			'DN_CONNECT_TIMEOUT'		=> $this->config['discord_notifications_connect_timeout'],
			'DN_EXEC_TIMEOUT'			=> $this->config['discord_notifications_exec_timeout'],

			'DN_POST_CREATE'			=> $this->config['discord_notification_type_post_create'],
			'DN_POST_UPDATE'			=> $this->config['discord_notification_type_post_update'],
			'DN_POST_DELETE'			=> $this->config['discord_notification_type_post_delete'],
			'DN_POST_LOCK'				=> $this->config['discord_notification_type_post_lock'],
			'DN_POST_UNLOCK'			=> $this->config['discord_notification_type_post_unlock'],
			'DN_POST_APPROVE'			=> $this->config['discord_notification_type_post_approve'],

			'DN_TOPIC_CREATE'			=> $this->config['discord_notification_type_topic_create'],
			'DN_TOPIC_UPDATE'			=> $this->config['discord_notification_type_topic_update'],
			'DN_TOPIC_DELETE'			=> $this->config['discord_notification_type_topic_delete'],
			'DN_TOPIC_LOCK'				=> $this->config['discord_notification_type_topic_lock'],
			'DN_TOPIC_UNLOCK'			=> $this->config['discord_notification_type_topic_unlock'],
			'DN_TOPIC_APPROVE'			=> $this->config['discord_notification_type_topic_approve'],

			'DN_USER_CREATE'			=> $this->config['discord_notification_type_user_create'],
			'DN_USER_DELETE'			=> $this->config['discord_notification_type_user_delete'],

			'DN_TOPIC_REACTION'			=> $this->config['discord_notification_type_reaction'],

			'DN_DEFAULT_WEBHOOK'		=> $this->config['discord_notification_default_webhook'],

			'DN_CURL_AVAILABLE'			=> $this->curl_available,
			'U_ACTION'					=> $this->u_action,
		]);
	}

	/**
	 * Additional validation compared to `filter_var($url, FILTER_VALIDATE_URL)`:
	 * Scheme must be "http" or "https".
	 *
	 * @param string $url
	 * @return bool
	 */
	private function validate_url($url)
	{
		$info = parse_url($url);
		if ($info === false || !isset($info['scheme'], $info['host'], $info['path']))
		{
			return false;
		}
		$scheme = strtolower($info['scheme']);
		if ($scheme !== 'http' && $scheme !== 'https')
		{
			return false;
		}
		return true;
	}

	/**
	 * Called when the user clicks the "Send Test Message" button on the page. Sends the content in the
	 * Text Message input to Discord.
	 */
	private function process_send_test_message()
	{
		global $table_prefix, $phpbb_container;

		$this->validate_post_request();

		$webhook = $this->request->variable('dn_test_webhook', '', true);
		$test_message = $this->request->variable('dn_test_message', '');

		// Check user inputs before attempting to send the message
		if ($test_message == '')
		{
			trigger_error($this->language->lang('DN_TEST_BAD_MESSAGE') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		if ($webhook == '')
		{
			trigger_error($this->language->lang('DN_TEST_BAD_WEBHOOK') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = "SELECT url FROM {$table_prefix}discord_webhooks WHERE alias = '" . $this->db->sql_escape($webhook) . "'";
		$result = $this->db->sql_query($sql);
		$data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$notification_service = $phpbb_container->get('mober.discordnotifications.notification_service');
		$result = $notification_service->force_send_discord_notification($data['url'], $test_message);
		if ($result)
		{
			trigger_error($this->language->lang('DN_TEST_SUCCESS') . adm_back_link($this->u_action));
		}
		else
		{
			trigger_error($this->language->lang('DN_TEST_FAILURE') . adm_back_link($this->u_action), E_USER_WARNING);
		}
	}

	private function process_delete_alias()
	{
		global $table_prefix;

		$this->validate_post_request();

		$delete_alias = $this->request->variable('action_delete_alias',  ['' => ''], true);
		foreach ($delete_alias as $alias => $url)
		{
			$sql = "DELETE FROM {$table_prefix}discord_webhooks WHERE alias = '" . $this->db->sql_escape($alias) . "'";
			$this->db->sql_query($sql);

			$sql = "UPDATE " . FORUMS_TABLE . " SET discord_notifications = '' WHERE discord_notifications = '" . $this->db->sql_escape($alias) . "'";
			$this->db->sql_query($sql);
		}
		trigger_error($this->language->lang('DN_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	private function process_settings_form_submit()
	{
		$this->validate_post_request();

		// Get form values for the main settings
		$master_enable = $this->request->variable('dn_master_enable', 0);
		$preview_length = $this->request->variable('dn_post_preview_length', '');

		// Verify that the post preview length is a numeric string, convert to an int and check the valid range
		if (!is_numeric($preview_length))
		{
			trigger_error($this->language->lang('DN_POST_PREVIEW_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		$preview_length = (int) $preview_length;
		if ($preview_length != 0 && ($preview_length < self::MIN_POST_PREVIEW_LENGTH || $preview_length > self::MAX_POST_PREVIEW_LENGTH))
		{
			trigger_error($this->language->lang('DN_POST_PREVIEW_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$connect_timeout = max(1, (int) $this->request->variable('dn_connect_to', 0));
		$exec_timeout = max(1, (int) $this->request->variable('dn_exec_to', 0));

		$this->config->set('discord_notifications_enabled', $master_enable);
		$this->config->set('discord_notifications_post_preview_length', $preview_length);
		$this->config->set('discord_notifications_connect_timeout', $connect_timeout);
		$this->config->set('discord_notifications_exec_timeout', $exec_timeout);

		// Log the settings change
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_DISCORD_NOTIFICATIONS_LOG_UPDATE');
		// Destroy any cached discord notification data
		$this->cache->destroy('mober_discord_notifications');

		trigger_error($this->language->lang('DN_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	private function process_webhooks_form_submit()
	{
		global $table_prefix;

		$this->validate_post_request();

		// Create new entry
		$new_alias = $this->request->variable('dn_webhook_new_alias', '', true);
		$new_url = $this->request->variable('dn_webhook_new_url', '');
		if ($new_alias !== '' && $new_url !== '')
		{
			if (!$this->validate_url($new_url))
			{
				trigger_error($this->language->lang('DN_WEBHOOK_URL_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}
			$sql = "INSERT INTO {$table_prefix}discord_webhooks(alias, url) VALUES('" . $this->db->sql_escape($new_alias) . "', '" . $this->db->sql_escape($new_url) . "')";
			$this->db->sql_query($sql);
		}

		// Update existing entries
		$webhook_configuration = $this->request->variable('dn_webhook', ['' => ''], true);
		// Validate all entries before updating
		foreach ($webhook_configuration as $url)
		{
			if (!$this->validate_url($url))
			{
				trigger_error($this->language->lang('DN_WEBHOOK_URL_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}
		foreach ($webhook_configuration as $alias => $url)
		{
			$sql = "UPDATE {$table_prefix}discord_webhooks SET url = '" . $this->db->sql_escape($url) . "' WHERE alias = '" . $this->db->sql_escape($alias) . "'";
			$this->db->sql_query($sql);
		}

		// Log the settings change
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_DISCORD_NOTIFICATIONS_LOG_UPDATE');
		// Destroy any cached discord notification data
		$this->cache->destroy('mober_discord_notifications');

		trigger_error($this->language->lang('DN_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	private function process_mapping_form_submit()
	{
		$this->validate_post_request();

		// Update configuration per forum
		$forum_configuration = $this->request->variable('dn_forum', [0 => ''], true);
		foreach ($forum_configuration as $id => $value)
		{
			$sql = "UPDATE " . FORUMS_TABLE . " SET discord_notifications = '" . $this->db->sql_escape($value) .
				"' WHERE forum_id = " . (int) $id;
			$this->db->sql_query($sql);
		}

		$this->config->set('discord_notification_type_post_create', $this->request->variable('dn_post_create', 0));
		$this->config->set('discord_notification_type_post_update', $this->request->variable('dn_post_update', 0));
		$this->config->set('discord_notification_type_post_delete', $this->request->variable('dn_post_delete', 0));
		$this->config->set('discord_notification_type_post_lock', $this->request->variable('dn_post_lock', 0));
		$this->config->set('discord_notification_type_post_unlock', $this->request->variable('dn_post_unlock', 0));
		$this->config->set('discord_notification_type_post_approve', $this->request->variable('dn_post_approve', 0));

		$this->config->set('discord_notification_type_topic_create', $this->request->variable('dn_topic_create', 0));
		$this->config->set('discord_notification_type_topic_update', $this->request->variable('dn_topic_update', 0));
		$this->config->set('discord_notification_type_topic_delete', $this->request->variable('dn_topic_delete', 0));
		$this->config->set('discord_notification_type_topic_lock', $this->request->variable('dn_topic_lock', 0));
		$this->config->set('discord_notification_type_topic_unlock', $this->request->variable('dn_topic_unlock', 0));
		$this->config->set('discord_notification_type_topic_approve', $this->request->variable('dn_topic_approve', 0));

		$this->config->set('discord_notification_type_user_create', $this->request->variable('dn_user_create', 0));
		$this->config->set('discord_notification_type_user_delete', $this->request->variable('dn_user_delete', 0));

		$this->config->set('discord_notification_type_reaction', $this->request->variable('dn_topic_reaction', 0));

		$this->config->set('discord_notification_default_webhook', $this->request->variable('dn_webhook_default', '', true));

		// Log the settings change
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_DISCORD_NOTIFICATIONS_LOG_UPDATE');
		// Destroy any cached discord notification data
		$this->cache->destroy('mober_discord_notifications');

		trigger_error($this->language->lang('DN_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	private function generate_webhook_section()
	{
		global $table_prefix;

		$sql = "SELECT alias, url FROM {$table_prefix}discord_webhooks ORDER BY alias";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$tpl_row = [
				'ALIAS'	=> $row['alias'],
				'URL'	=> $row['url'],
			];
			$this->template->assign_block_vars('webhookrow', $tpl_row);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Generates the section of the ACP page listing all of the forums, in order, with the radio button option
	 * that allows the user to enable or disable discord notifications for that forum.
	 */
	private function generate_forum_section()
	{
		global $phpbb_root_path, $phpEx;
		if (!function_exists('get_forum_parents'))
		{
			include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		}

		$sql = "SELECT forum_id, forum_type, forum_name, left_id, right_id, parent_id, forum_parents, discord_notifications
			FROM " . FORUMS_TABLE . "
			ORDER BY left_id";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			// Category forums are displayed for organizational purposes, but have no configuration
			if ($row['forum_type'] == FORUM_CAT)
			{
				$tpl_row = [
					'S_IS_CAT'		=> true,
					'FORUM_NAME'	=> $row['forum_name'],
					'PARENTS'		=> '',
				];
				$this->template->assign_block_vars('forumrow', $tpl_row);
			}
			else if ($row['forum_type'] == FORUM_POST)
			{
				// The labels for all the inputs are constructed based on the forum IDs to make it easy to know which
				$tpl_row = [
					'S_IS_CAT'		=> false,
					'FORUM_NAME'	=> $row['forum_name'],
					'FORUM_ID'		=> $row['forum_id'],
					'ALIAS'			=> $row['discord_notifications'],
					'PARENTS'		=> '',
				];
				$parents = get_forum_parents($row);
				if (is_array($parents))
				{
					foreach ($parents as $parent)
					{
						$tpl_row['PARENTS'] .= $parent[0] . ' &rarr; ';
					}
				}
				$this->template->assign_block_vars('forumrow', $tpl_row);
			}
			// Other forum types (links) are ignored
		}
		$this->db->sql_freeresult($result);
	}

	private function validate_post_request()
	{
		if (!check_form_key(self::PAGE_FORM_NAME))
		{
			trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}
	}
}
