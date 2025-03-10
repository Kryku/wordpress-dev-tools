<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_log_viewer_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    $log_file = defined('WP_DEBUG_LOG') && is_string(WP_DEBUG_LOG) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';
    $logs = file_exists($log_file) ? file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $wp_config_path = ABSPATH . 'wp-config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_action']) && wp_verify_nonce($_POST['log_nonce'], 'log_action')) {
        if ($_POST['log_action'] === 'clear') {
            if (file_put_contents($log_file, '') !== false) {
                $message = '<div class="updated"><p>Log file cleared!</p></div>';
                $logs = [];
            } else {
                $message = '<div class="error"><p>Failed to clear log file.</p></div>';
            }
        } elseif ($_POST['log_action'] === 'download') {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="debug-' . current_time('mysql') . '.log"');
            readfile($log_file);
            exit;
        } elseif ($_POST['log_action'] === 'update_config') {
            $wp_debug = isset($_POST['wp_debug']) ? 'true' : 'false';
            $wp_debug_log = isset($_POST['wp_debug_log']) ? 'true' : 'false';
            $wp_debug_display = isset($_POST['wp_debug_display']) ? 'true' : 'false';

            if (is_writable($wp_config_path)) {
                $config_content = file_get_contents($wp_config_path);
                $patterns = [
                    "/define\(\s*['\"]WP_DEBUG['\"],\s*(true|false)\s*\);/",
                    "/define\(\s*['\"]WP_DEBUG_LOG['\"],\s*(true|false)\s*\);/",
                    "/define\(\s*['\"]WP_DEBUG_DISPLAY['\"],\s*(true|false)\s*\);/"
                ];
                $replacements = [
                    "define('WP_DEBUG', $wp_debug);",
                    "define('WP_DEBUG_LOG', $wp_debug_log);",
                    "define('WP_DEBUG_DISPLAY', $wp_debug_display);"
                ];

                if (!preg_match($patterns[0], $config_content)) {
                    $config_content = str_replace(
                        "/* That's all, stop editing! Happy publishing. */",
                        "define('WP_DEBUG', $wp_debug);\n/* That's all, stop editing! Happy publishing. */",
                        $config_content
                    );
                }
                if (!preg_match($patterns[1], $config_content)) {
                    $config_content = str_replace(
                        "/* That's all, stop editing! Happy publishing. */",
                        "define('WP_DEBUG_LOG', $wp_debug_log);\n/* That's all, stop editing! Happy publishing. */",
                        $config_content
                    );
                }
                if (!preg_match($patterns[2], $config_content)) {
                    $config_content = str_replace(
                        "/* That's all, stop editing! Happy publishing. */",
                        "define('WP_DEBUG_DISPLAY', $wp_debug_display);\n/* That's all, stop editing! Happy publishing. */",
                        $config_content
                    );
                }

                $new_config = preg_replace($patterns, $replacements, $config_content);
                if (file_put_contents($wp_config_path, $new_config) !== false) {
                    $message = '<div class="updated"><p>wp-config.php updated!</p></div>';
                } else {
                    $message = '<div class="error"><p>Failed to update wp-config.php.</p></div>';
                }
            } else {
                $message = '<div class="error"><p>wp-config.php is not writable. Check file permissions.</p></div>';
            }
        }
    }

    $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : '';
    if ($filter) {
        $logs = array_filter($logs, function($line) use ($filter) {
            return stripos($line, $filter) !== false;
        });
    }

    ?>
    <div class="wrap">
        <h1>Log Viewer</h1>
        <p>View and manage your debug.log file.</p>
        <?php if ($message) echo $message; ?>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Debug Settings</h2>
            </div>
            <div class="debug-settings">
                <?php if (!is_writable($wp_config_path)) { ?>
                    <div class="notice notice-error"><p>wp-config.php is not writable. Make it writable to edit debug settings.</p></div>
                <?php } else { ?>
                    <form method="post">
                        <?php wp_nonce_field('log_action', 'log_nonce'); ?>
                        <input type="hidden" name="log_action" value="update_config">
                        <p>
                            <label><input type="checkbox" name="wp_debug" <?php checked(defined('WP_DEBUG') && WP_DEBUG); ?>> WP_DEBUG</label><br>
                            <small>Enable debug mode to log errors.</small>
                        </p>
                        <p>
                            <label><input type="checkbox" name="wp_debug_log" <?php checked(defined('WP_DEBUG_LOG') && WP_DEBUG_LOG); ?>> WP_DEBUG_LOG</label><br>
                            <small>Save debug messages to <?php echo esc_html($log_file); ?>.</small>
                        </p>
                        <p>
                            <label><input type="checkbox" name="wp_debug_display" <?php checked(defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY); ?>> WP_DEBUG_DISPLAY</label><br>
                            <small>Show debug messages on the site (not recommended for live sites).</small>
                        </p>
                        <input type="submit" class="tool-card-btn" value="Update Settings">
                    </form>
                <?php } ?>
            </div>
        </div>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Debug Logs</h2>
            </div>
            <div class="log-controls">
                <div class="log-controls-forms">
                    <form method="post" class="log-controls-form-clear">
                        <?php wp_nonce_field('log_action', 'log_nonce'); ?>
                        <input type="hidden" name="log_action" value="clear">
                        <input type="submit" class="tool-card-btn" value="Clear Logs" <?php echo empty($logs) ? 'disabled' : ''; ?>>
                    </form>
                    <form method="post" class="log-controls-form-logs">
                        <?php wp_nonce_field('log_action', 'log_nonce'); ?>
                        <input type="hidden" name="log_action" value="download">
                        <input type="submit" class="tool-card-btn" value="Download Logs" <?php echo empty($logs) ? 'disabled' : ''; ?>>
                    </form>
                    <form method="post" class="log-controls-form-filter">
                        <?php wp_nonce_field('log_action', 'log_nonce'); ?>
                        <input type="text" name="filter" value="<?php echo esc_attr($filter); ?>" placeholder="Filter logs (e.g., 'error')">
                        <input type="submit" class="tool-card-btn" style="color: #ffffff; background-color: #141414" value="Filter">
                    </form>
                </div>
            </div>
            <div class="log-viewer">
               
                    <?php
                    if ($logs) {
                        echo '<pre>';
                        foreach (array_reverse($logs) as $line) {
                            echo esc_html($line) . "\n";
                        }
                        echo '<pre>';
                    } else {
                        echo "No logs to display.";
                    }
                    ?>
              
            </div>
        </div>
    </div>
    <style>
    .debug-settings { padding: 10px; }
    .debug-settings p { margin: 10px 0; }
    .debug-settings small { color: #666; }
    .log-controls { padding: 10px; }
    .log-controls-forms{ display: flex; flex-wrap: wrap;}
    .log-controls-form-clear, .log-controls-form-logs{ width: 50%;}
    .log-controls-form-filter{display: flex; width: 100%; margin-top: 10px}
    .log-controls-form-filter input[type="text"] { width: 500%;}
    .log-viewer { padding: 10px; background: #f5f5f5; border: 1px solid #ddd; max-height: 400px; overflow-y: auto; }
    .log-viewer pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
    </style>
    <?php
}

function dev_tools_log_viewer_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z" fill="#0F1729"/>
                </svg>
            </div>
            <h2>Log Viewer</h2>
        </div>
        <p>View and manage debug logs</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-logs'); ?>" class="tool-card-btn">to logs</a>
    </div>
    <?php
}