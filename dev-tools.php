<?php
/*
Plugin Name: Dev Tools
Description: Developer toolkit
Version: 1.0
Author: V.Krykun
*/

define('DEV_TOOLS_DB_MANAGER', true);
define('DEV_TOOLS_SERVER_COMMANDS', true);
define('DEV_TOOLS_FILE_EDITOR', true);

if (DEV_TOOLS_DB_MANAGER) {
    require_once plugin_dir_path(__FILE__) . 'includes/db-manager/db-manager.php';
}
if (DEV_TOOLS_SERVER_COMMANDS) {
    require_once plugin_dir_path(__FILE__) . 'includes/server-commands/server-commands.php';
}
if (DEV_TOOLS_FILE_EDITOR) {
    require_once plugin_dir_path(__FILE__) . 'includes/file-editor/file-editor.php';
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
            ?>
        </div>
    </div>
    <?php
}

function dev_tools_styles() {
    wp_enqueue_style('dev-tools-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('admin_enqueue_scripts', 'dev_tools_styles');