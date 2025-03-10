<?php
/*
Plugin Name: Dev Tools
Description: Developer toolkit
Version: 1.7
Author: <a href="https://github.com/Kryku">Vladyslav Krykun</a>
*/

define('DEV_TOOLS_DB_MANAGER', true);
define('DEV_TOOLS_SERVER_COMMANDS', true);
define('DEV_TOOLS_FILE_EDITOR', true);
define('DEV_TOOLS_SNIPPET_RUNNER', true);
define('DEV_TOOLS_CRON_SCHEDULER', true);
define('DEV_TOOLS_PERFORMANCE_PROFILER', true);
define('DEV_TOOLS_EMAIL_TESTER', true);
define('DEV_TOOLS_REST_API_TESTER', true);
define('DEV_TOOLS_PLUGIN_ROLLBACK', true);

$sos_enabled = get_option('dev_tools_sos_enabled', false);

if (DEV_TOOLS_DB_MANAGER) {
    require_once plugin_dir_path(__FILE__) . 'includes/db-manager/db-manager.php';
}
if (DEV_TOOLS_SERVER_COMMANDS) {
    require_once plugin_dir_path(__FILE__) . 'includes/server-commands/server-commands.php';
}
if (DEV_TOOLS_FILE_EDITOR) {
    require_once plugin_dir_path(__FILE__) . 'includes/file-editor/file-editor.php';
}
if (DEV_TOOLS_SNIPPET_RUNNER) {
    require_once plugin_dir_path(__FILE__) . 'includes/code-snippet/code-snippet.php';
}
if (DEV_TOOLS_CRON_SCHEDULER) {
    require_once plugin_dir_path(__FILE__) . 'includes/cron-scheduler/cron-scheduler.php';
}
if (DEV_TOOLS_PERFORMANCE_PROFILER) {
    require_once plugin_dir_path(__FILE__) . 'includes/performance-profiler/performance-profiler.php';
}
if (DEV_TOOLS_EMAIL_TESTER) {
    require_once plugin_dir_path(__FILE__) . 'includes/email-tester/email-tester.php';
}
if (DEV_TOOLS_REST_API_TESTER) {
    require_once plugin_dir_path(__FILE__) . 'includes/rest-api-tester/rest-api-tester.php';
}
if (DEV_TOOLS_PLUGIN_ROLLBACK) {
    require_once plugin_dir_path(__FILE__) . 'includes/plugin-rollback/plugin-rollback.php';
}
if ($sos_enabled) {
    require_once plugin_dir_path(__FILE__) . 'includes/save-our-soul/save-our-soul.php';
}

function dev_tools_menu() {
    add_menu_page(
        'Dev Tools',
        'Dev Tools',
        'manage_options',
        'dev-tools',
        'dev_tools_main_page',
        'dashicons-hammer',
        80
    );

    if (DEV_TOOLS_DB_MANAGER) {
        add_submenu_page(
            'dev-tools',
            'Database Manager',
            'Database Manager',
            'manage_options',
            'dev-tools-db-manager',
            'dev_tools_db_manager_page'
        );
    }

    if (DEV_TOOLS_SERVER_COMMANDS) {
        add_submenu_page(
            'dev-tools',
            'Server Commands',
            'Server Commands',
            'manage_options',
            'dev-tools-server-commands',
            'dev_tools_server_commands_page'
        );
    }

    if (DEV_TOOLS_FILE_EDITOR) {
        add_submenu_page(
            'dev-tools',
            'File Editor',
            'File Editor',
            'manage_options',
            'dev-tools-file-editor',
            'dev_tools_file_editor_page'
        );
    }

    if (DEV_TOOLS_SNIPPET_RUNNER) {
        add_submenu_page(
            'dev-tools',
            'Code Snippet Runner',
            'Code Snippet Runner',
            'manage_options',
            'dev-tools-snippet',
            'dev_tools_snippet_page'
        );
    }
    
    if (DEV_TOOLS_CRON_SCHEDULER) {
        add_submenu_page(
            'dev-tools',
            'Cron Scheduler',
            'Cron Scheduler',
            'manage_options',
            'dev-tools-cron',
            'dev_tools_cron_page'
        );
    }
    
    if (DEV_TOOLS_PERFORMANCE_PROFILER) {
        add_submenu_page(
            'dev-tools',
            'Performance Profiler',
            'Performance Profiler',
            'manage_options',
            'dev-tools-performance',
            'dev_tools_performance_page'
        );
    }
    
    if (DEV_TOOLS_EMAIL_TESTER) {
        add_submenu_page(
            'dev-tools',
            'Email Tester',
            'Email Tester',
            'manage_options',
            'dev-tools-email',
            'dev_tools_email_page'
        );
    }
    
    if (DEV_TOOLS_REST_API_TESTER) {
        add_submenu_page(
            'dev-tools',
            'REST API Tester',
            'REST API Tester',
            'manage_options',
            'dev-tools-rest-api',
            'dev_tools_rest_api_tester_page'
        );
    }
    
    if (DEV_TOOLS_PLUGIN_ROLLBACK) {
        add_submenu_page(
            'dev-tools',
            'Plugin Rollback',
            'Plugin Rollback',
            'manage_options',
            'dev-tools-rollback',
            'dev_tools_plugin_rollback_page'
        );
    }
    
    add_submenu_page(
        'dev-tools',
        'SOS Settings',
        'SOS Settings',
        'manage_options',
        'dev-tools-sos-settings',
        'dev_tools_sos_settings_page'
    );
}
add_action('admin_menu', 'dev_tools_menu');

function dev_tools_main_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Dev Tools</h1>
        <div class="dev-tools-catalog">
            <?php
            if (DEV_TOOLS_DB_MANAGER) {
                dev_tools_db_manager_card();
            }
            if (DEV_TOOLS_SERVER_COMMANDS) {
                dev_tools_server_commands_card();
            }
            if (DEV_TOOLS_FILE_EDITOR) {
                dev_tools_file_editor_card();
            }
            if (DEV_TOOLS_SNIPPET_RUNNER) {
                dev_tools_snippet_card();
            }
            if (DEV_TOOLS_CRON_SCHEDULER) {
                dev_tools_cron_card();
            }
            if (DEV_TOOLS_PERFORMANCE_PROFILER) {
                dev_tools_performance_card();
            }
            if (DEV_TOOLS_EMAIL_TESTER) {
                dev_tools_email_card();
            }
            if (DEV_TOOLS_REST_API_TESTER) {
                dev_tools_rest_api_tester_card();
            }
            if (DEV_TOOLS_PLUGIN_ROLLBACK) {
                dev_tools_plugin_rollback_card();
            }
            if (get_option('dev_tools_sos_enabled', false)) {
                dev_tools_sos_card();
            }
            ?>
        </div>
    </div>
    <?php
}

function dev_tools_sos_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['sos_submit']) && check_admin_referer('sos_settings_action')) {
        $enabled = isset($_POST['sos_enabled']) ? 1 : 0;
        update_option('dev_tools_sos_enabled', $enabled);
        echo '<div class="updated"><p>Settings saved!</p></div>';
    }

    $sos_enabled = get_option('dev_tools_sos_enabled', false);
    ?>
    <div class="wrap" style="max-width: 700px; background-color: #ffffff; margin: 0 auto; border-radius: 5px; padding: 20px; box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px; margin-top: 30px;">
        <h1 style="margin-bottom: 10px">SOS Settings</h1>
        <form method="post">
            <?php wp_nonce_field('sos_settings_action'); ?>
            <label>
                <input type="checkbox" name="sos_enabled" <?php checked($sos_enabled, 1); ?>>
                Enable Save Our Souls (Emergency File Editor)
            </label>
            <p><em>Note: When enabled, you can also access it directly at <br><br><code><?php echo plugin_dir_url(__FILE__) . 'includes/save-our-soul/'; ?></code></em></p>
            <input type="submit" name="sos_submit" class="tool-card-btn" value="Save Changes">
        </form>
    </div>
    <?php
}

function dev_tools_styles() {
    wp_enqueue_style('dev-tools-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('admin_enqueue_scripts', 'dev_tools_styles');