<?php

/**
 * User Config
 *
 * Roundcube plugin that loads user-specific configuration.
 *
 * https://github.com/sbehuret/roundcube-plugin-user-config
 *
 * @version 1.0
 * @author Sébastien Béhuret <sebastien@behuret.net>
 */

class user_config extends rcube_plugin
{
    public function init()
    {
        $rcmail = rcube::get_instance();

	$user_config_includes = $rcmail->config->get('user_config_includes', false);

        if (!is_bool($user_config_includes) && !is_array($user_config_includes)) {
            $rcmail->write_log('errors', 'Setting user_config_includes must be a boolean or an array of logins to configuration filenames');
            return;
        }

        if ($user_config_includes === false)
            return;

	if (!$rcmail->user || !$rcmail->user->ID)
            return;

        $username = $rcmail->user->data['username'];

	$filename = null;

	if (is_array($user_config_includes)) {
            if (array_key_exists($username, $user_config_includes))
                $filename = $user_config_includes[$username];
	} else
            $filename = preg_replace('/[^a-z0-9\.\-_@]/i', '', $username) . '.inc.php';

        $plugins_before = $rcmail->config->get('plugins', array());

        if ($filename)
            $rcmail->config->load_from_file($filename);

        $plugins_after = $rcmail->config->get('plugins', array());

        foreach (array_unique(array_values(array_diff($plugins_after, $plugins_before))) as $plugin)
            $rcmail->plugins->load_plugin($plugin);
    }
}
