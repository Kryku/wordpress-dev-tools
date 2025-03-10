<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_plugin_rollback_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    $rollback_logs = get_option('dev_tools_rollback_logs', []);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rollback_action']) && wp_verify_nonce($_POST['rollback_nonce'], 'rollback_action')) {
        $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
        $version = sanitize_text_field($_POST['version']);

        $plugins = get_plugins();
        $plugin_file = '';
        foreach ($plugins as $file => $data) {
            if (strpos($file, $plugin_slug . '/') === 0) {
                $plugin_file = $file;
                break;
            }
        }

        if ($plugin_file && $version) {
            $api_url = "https://api.wordpress.org/plugins/info/1.0/$plugin_slug.json";
            $response = wp_remote_get($api_url);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $plugin_data = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($plugin_data['versions'][$version])) {
                    $download_url = $plugin_data['versions'][$version];
                    $result = dev_tools_install_plugin_version($download_url, $plugin_file);
                    if ($result) {
                        $message = '<div class="updated"><p>Plugin rolled back to version ' . esc_html($version) . '!</p></div>';
                        $rollback_logs[] = [
                            'plugin' => $plugin_file,
                            'from_version' => $plugins[$plugin_file]['Version'],
                            'to_version' => $version,
                            'time' => current_time('mysql')
                        ];
                        update_option('dev_tools_rollback_logs', array_slice($rollback_logs, -10));
                    } else {
                        $message = '<div class="error"><p>Failed to rollback plugin.</p></div>';
                    }
                } else {
                    $message = '<div class="error"><p>Version not found.</p></div>';
                }
            } else {
                $message = '<div class="error"><p>Could not fetch plugin data.</p></div>';
            }
        }
    }

    $plugins = get_plugins();
    ?>
    <div class="wrap">
        <h1>Plugin Rollback</h1>
        <p>Rollback plugins to previous versions.</p>
        <?php if ($message) echo $message; ?>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Installed Plugins</h2>
            </div>
            <div class="rollback-plugins">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Plugin</th>
                            <th>Current Version</th>
                            <th>Rollback To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($plugins as $file => $data) {
                            $slug = explode('/', $file)[0];
                            $api_url = "https://api.wordpress.org/plugins/info/1.0/$slug.json";
                            $response = wp_remote_get($api_url);
                            $versions = [];
                            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                                $plugin_data = json_decode(wp_remote_retrieve_body($response), true);
                                $versions = isset($plugin_data['versions']) ? array_keys($plugin_data['versions']) : [];
                                usort($versions, 'version_compare');
                                $versions = array_reverse($versions);
                            }
                            ?>
                            <tr>
                                <td><?php echo esc_html($data['Name']); ?></td>
                                <td><?php echo esc_html($data['Version']); ?></td>
                                <td>
                                    <form method="post">
                                        <?php wp_nonce_field('rollback_action', 'rollback_nonce'); ?>
                                        <input type="hidden" name="rollback_action" value="rollback">
                                        <input type="hidden" name="plugin_slug" value="<?php echo esc_attr($slug); ?>">
                                        <select name="version">
                                            <option value="">Select version</option>
                                            <?php
                                            foreach ($versions as $ver) {
                                                if ($ver !== $data['Version']) {
                                                    echo '<option value="' . esc_attr($ver) . '">' . esc_html($ver) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                </td>
                                <td>
                                        <input type="submit" class="tool-card-btn" value="Rollback" <?php echo empty($versions) ? 'disabled' : ''; ?>>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Rollback Logs</h2>
            </div>
            <div class="rollback-logs">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Plugin</th>
                            <th>From Version</th>
                            <th>To Version</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($rollback_logs) {
                            foreach (array_reverse($rollback_logs) as $log) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html($log['plugin']); ?></td>
                                    <td><?php echo esc_html($log['from_version']); ?></td>
                                    <td><?php echo esc_html($log['to_version']); ?></td>
                                    <td><?php echo esc_html($log['time']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="4">No rollbacks yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>
    .rollback-plugins, .rollback-logs { padding: 10px; }
    .rollback-plugins table, .rollback-logs table { width: 100%; }
    .rollback-plugins th, .rollback-plugins td, .rollback-logs th, .rollback-logs td { padding: 8px; text-align: left; }
    </style>
    <?php
}

function dev_tools_install_plugin_version($download_url, $plugin_file) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $upgrader = new Plugin_Upgrader();
    $result = $upgrader->install($download_url, ['overwrite_package' => true]);
    return !is_wp_error($result);
}

function dev_tools_plugin_rollback_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
<path d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM13.92 16.13H9C8.59 16.13 8.25 15.79 8.25 15.38C8.25 14.97 8.59 14.63 9 14.63H13.92C15.2 14.63 16.25 13.59 16.25 12.3C16.25 11.01 15.21 9.97 13.92 9.97H8.85L9.11 10.23C9.4 10.53 9.4 11 9.1 11.3C8.95 11.45 8.76 11.52 8.57 11.52C8.38 11.52 8.19 11.45 8.04 11.3L6.47 9.72C6.18 9.43 6.18 8.95 6.47 8.66L8.04 7.09C8.33 6.8 8.81 6.8 9.1 7.09C9.39 7.38 9.39 7.86 9.1 8.15L8.77 8.48H13.92C16.03 8.48 17.75 10.2 17.75 12.31C17.75 14.42 16.03 16.13 13.92 16.13Z" fill="#292D32"/>
</svg>
            </div>
            <h2>Plugin Rollback</h2>
        </div>
        <p>Rollback plugins to previous versions</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-rollback'); ?>" class="tool-card-btn">to rollback</a>
    </div>
    <?php
}