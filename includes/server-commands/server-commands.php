<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_server_commands_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="800px" width="800px" version="1.1" id="Capa_1" viewBox="0 0 511 511" xml:space="preserve"> <g> <path d="M487.5,51.999h-464c-12.958,0-23.5,10.542-23.5,23.5v304c0,12.958,10.542,23.5,23.5,23.5h158.057l-11.714,41H135.5   c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h39.976c0.009,0,0.019,0.002,0.028,0.002c0.008,0,0.016-0.002,0.024-0.002h159.944   c0.008,0,0.016,0.002,0.024,0.002c0.009,0,0.019-0.002,0.028-0.002H375.5c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5h-34.343   l-11.714-41H487.5c12.958,0,23.5-10.542,23.5-23.5v-304C511,62.541,500.458,51.999,487.5,51.999z M325.557,443.999H185.443   l11.714-41h116.686L325.557,443.999z M496,379.499c0,4.687-3.813,8.5-8.5,8.5H319.677c-0.001,0-0.002,0-0.003,0H23.5   c-4.687,0-8.5-3.813-8.5-8.5v-304c0-4.687,3.813-8.5,8.5-8.5h464c4.687,0,8.5,3.813,8.5,8.5V379.499z"/> <path d="M471.5,83.999h-432c-4.142,0-7.5,3.358-7.5,7.5v272c0,4.142,3.358,7.5,7.5,7.5h432c4.142,0,7.5-3.358,7.5-7.5v-272   C479,87.357,475.642,83.999,471.5,83.999z M464,98.999v49H47v-49H464z M47,355.999v-193h417v193H47z"/> <path d="M151.5,130.999h240c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5h-240c-4.142,0-7.5,3.358-7.5,7.5   S147.358,130.999,151.5,130.999z"/> <path d="M423.5,130.999h16c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5h-16c-4.142,0-7.5,3.358-7.5,7.5   S419.358,130.999,423.5,130.999z"/> <path d="M71.5,130.999c1.97,0,3.91-0.8,5.3-2.2c1.4-1.39,2.2-3.33,2.2-5.3c0-1.97-0.8-3.91-2.2-5.3c-1.39-1.4-3.33-2.2-5.3-2.2   c-1.97,0-3.91,0.8-5.3,2.2c-1.4,1.39-2.2,3.33-2.2,5.3c0,1.97,0.8,3.91,2.2,5.3C67.59,130.199,69.53,130.999,71.5,130.999z"/> <path d="M95.5,130.999c1.97,0,3.91-0.8,5.3-2.2c1.4-1.39,2.2-3.33,2.2-5.3c0-1.97-0.8-3.91-2.2-5.3c-1.39-1.4-3.33-2.2-5.3-2.2   c-1.98,0-3.91,0.8-5.3,2.2c-1.4,1.39-2.2,3.33-2.2,5.3c0,1.97,0.8,3.91,2.2,5.3C91.59,130.199,93.53,130.999,95.5,130.999z"/> <path d="M119.5,130.999c1.97,0,3.91-0.8,5.3-2.2c1.4-1.39,2.2-3.33,2.2-5.3c0-1.97-0.8-3.91-2.2-5.3c-1.39-1.4-3.33-2.2-5.3-2.2   c-1.97,0-3.91,0.8-5.3,2.2c-1.4,1.39-2.2,3.33-2.2,5.3c0,1.97,0.8,3.91,2.2,5.3C115.59,130.199,117.53,130.999,119.5,130.999z"/> <path d="M100.803,198.196c-2.929-2.929-7.678-2.929-10.606,0c-2.929,2.929-2.929,7.678,0,10.606l50.697,50.697l-50.697,50.697   c-2.929,2.929-2.929,7.678,0,10.606c1.464,1.464,3.384,2.197,5.303,2.197s3.839-0.732,5.303-2.197l56-56   c2.929-2.929,2.929-7.678,0-10.606L100.803,198.196z"/> <path d="M247.5,307.999h-80c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h80c4.142,0,7.5-3.358,7.5-7.5   S251.642,307.999,247.5,307.999z"/> </g> </svg>
            </div>
            <h2>Server Commands</h2>
        </div>
        <p>Executing commands on the server</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-server-commands'); ?>" 
           class="tool-card-btn">to commands</a>
    </div>
    <?php
}

function dev_tools_server_commands_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $output = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
        isset($_POST['server_command']) && 
        wp_verify_nonce($_POST['server_commands_nonce'], 'server_commands_action')) {
        
        $command = sanitize_text_field($_POST['server_command']);
        
        if (function_exists('shell_exec')) {
            $output = shell_exec($command . ' 2>&1');
            if ($output === null) {
                $error = 'The command execution is not supported on this server.';
            }
        } else {
            $error = 'The shell_exec() function is disabled on the server.';
        }
    }
    ?>
    <div class="wrap">
        <h1>Server Commands</h1>
        
        <form method="post" class="server-commands-request">
            <?php wp_nonce_field('server_commands_action', 'server_commands_nonce'); ?>
            <input type="text" name="server_command" placeholder="Enter a command (e.g. ls -l, dir)" 
                   style="width: 100%; max-width: 600px; margin: 10px 0;" 
                   value="<?php echo isset($_POST['server_command']) ? esc_attr($_POST['server_command']) : ''; ?>">
            <br>
            <input type="submit" class="server-commands-btn" value="Execute command">
        </form>

        <?php if ($error): ?>
            <div class="error"><p><?php echo esc_html($error); ?></p></div>
        <?php elseif ($output): ?>
            <h3>Result:</h3>
            <pre><?php echo esc_html($output); ?></pre>
        <?php endif; ?>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'dev-tools-server-commands') {
        wp_enqueue_style(
            'server-commands-style', 
            plugin_dir_url(__FILE__) . 'style.css'
        );
    }
});