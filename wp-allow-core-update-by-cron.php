<?php
/*
Plugin Name:  Core Update with Cronjob for production sites
Plugin URI:   https://danfield.eu
Description:  Minor updates of the core will automatically be processed
Version:      1.0.0
Author:       Daniel Feldbrugge
Author URI:   https://danfield.eu/
License:      MIT License
*/


if (!defined('DOING_CRON')) {
    return;
}

if (!wp_next_scheduled('cron_updater_event')) {
    wp_schedule_event(time(), 'hourly', 'cron_updater_event');
}
add_action('cron_updater_event', function () {

    add_filter('file_mod_allowed', '__return_true');
    add_filter('automatic_updater_disabled', '__return_false');
    add_filter('allow_dev_auto_core_updates', '__return_false');
    add_filter('allow_minor_auto_core_updates', '__return_true');         // Enable minor updates
    add_filter('allow_major_auto_core_updates', '__return_false');
    add_filter('automatic_updates_is_vcs_checkout', '__return_false', 1);
    add_filter('auto_update_plugin', '__return_false');
    add_filter('auto_update_theme', '__return_false');

    include_once ABSPATH . 'wp-admin/includes/admin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    class Cron_updater extends WP_Automatic_Updater
    {

        function run_cron()
        {
            // Next, process any core update.
            wp_version_check(); // Check for core updates.
            $core_update = find_core_auto_update();
            if ($core_update) {
                $this->update('core', $core_update);
            }

        }
    }

    $upgrader = new Cron_updater();
    $upgrader->run_cron();

});


