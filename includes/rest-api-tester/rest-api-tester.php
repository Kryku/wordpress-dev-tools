<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_rest_api_tester_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $response = '';
    $request_history = get_option('dev_tools_rest_api_history', []);
    $rest_url = rest_url();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rest_action']) && wp_verify_nonce($_POST['rest_nonce'], 'rest_action')) {
        $method = sanitize_text_field($_POST['method']);
        $endpoint = sanitize_text_field($_POST['endpoint']);
        $headers = !empty($_POST['headers']) ? wp_kses_post($_POST['headers']) : '';
        $body = !empty($_POST['body']) ? wp_kses_post($_POST['body']) : '';

        $url = $rest_url . ltrim($endpoint, '/');

        $header_array = [];
        if ($headers) {
            foreach (explode("\n", $headers) as $line) {
                $parts = explode(':', trim($line), 2);
                if (count($parts) === 2) {
                    $header_array[trim($parts[0])] = trim($parts[1]);
                }
            }
        }
        $header_array['X-WP-Nonce'] = wp_create_nonce('wp_rest');

        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name, 'wordpress_logged_in_') === 0) {
                $cookies[] = "$name=" . urlencode($value);
            }
        }
        if ($cookies) {
            $header_array['Cookie'] = implode('; ', $cookies);
        }

        $args = [
            'method' => $method,
            'headers' => $header_array,
            'timeout' => 15,
        ];

        if ($method === 'GET') {
            if ($body) {
                $params = [];
                parse_str($body, $params);
                $url = add_query_arg($params, $url);
            }
        } else {
            $args['body'] = $body;
        }

        $start_time = microtime(true);
        $result = wp_remote_request($url, $args);
        $end_time = microtime(true);
        $execution_time = number_format($end_time - $start_time, 3);

        if (is_wp_error($result)) {
            $response = '<div class="error"><p>Error: ' . esc_html($result->get_error_message()) . '</p></div>';
        } else {
            $status_code = wp_remote_retrieve_response_code($result);
            $response_body = wp_remote_retrieve_body($result);
            $response_headers = wp_remote_retrieve_headers($result)->getAll();

            $response = '<div class="updated">';
            $response .= '<p><strong>Status:</strong> ' . esc_html($status_code) . '</p>';
            $response .= '<p><strong>Execution Time:</strong> ' . esc_html($execution_time) . ' seconds</p>';
            $response .= '<p><strong>Response Headers:</strong><pre>' . esc_html(print_r($response_headers, true)) . '</pre></p>';
            $response .= '<p><strong>Response Body:</strong><pre>' . esc_html($response_body) . '</pre></p>';
            $response .= '</div>';

            $request_history[] = [
                'method' => $method,
                'endpoint' => $endpoint,
                'headers' => $headers,
                'body' => $body,
                'time' => current_time('mysql'),
                'status' => $status_code
            ];
            update_option('dev_tools_rest_api_history', array_slice($request_history, -10));
        }
    }

    ?>
    <div class="wrap">
        <h1>REST API Tester</h1>
        <p>Test WordPress REST API requests from the admin.</p>
        <?php if ($response) echo $response; ?>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Send Request</h2>
            </div>
            <form method="post" class="rest-form">
                <?php wp_nonce_field('rest_action', 'rest_nonce'); ?>
                <input type="hidden" name="rest_action" value="send">
                <p>
                    <label>Method:</label><br>
                    <select name="method" required>
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </p>
                <p>
                    <label>Endpoint:</label><br>
                    <input type="text" name="endpoint" required placeholder="wp/v2/posts" value="wp/v2/posts">
                    <small>Base URL: <?php echo esc_html($rest_url); ?></small>
                </p>
                <p>
                    <label>Headers (one per line, e.g., Authorization: Bearer token):</label><br>
                    <textarea name="headers" rows="3" placeholder="Content-Type: application/json"></textarea>
                </p>
                <p>
                    <label>Body (JSON or raw data):</label><br>
                    <textarea name="body" rows="5" placeholder='{"title": "Test Post", "content": "Hello World"}'></textarea>
                </p>
                <input type="submit" class="tool-card-btn" value="Send Request">
            </form>
        </div>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Request History</h2>
            </div>
            <div class="rest-history">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($request_history) {
                            foreach (array_reverse($request_history) as $request) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html($request['method']); ?></td>
                                    <td><?php echo esc_html($request['endpoint']); ?></td>
                                    <td><?php echo esc_html($request['time']); ?></td>
                                    <td><?php echo esc_html($request['status']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="4">No requests yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Tips</h2>
            </div>
            <div class="rest-tips">
                <p><strong>Authentication:</strong> Nonce and cookie are added automatically. For custom auth, add headers like <code>Authorization: Bearer token</code>.</p>
                <p><strong>Examples:</strong> Try <code>GET wp/v2/users/me</code> or <code>POST wp/v2/posts</code> with JSON body.</p>
            </div>
        </div>
    </div>
    <style>
    .rest-form { padding: 10px; }
    .rest-form p { margin: 10px 0; }
    .rest-form label { font-weight: bold; }
    .rest-form input[type="text"], .rest-form textarea, .rest-form select { 
        width: 100%; max-width: 400px; padding: 5px; 
    }
    .rest-form textarea { height: 100px; }
    .rest-form small { display: block; color: #666; }
    .rest-history { padding: 10px; }
    .rest-history table { width: 100%; }
    .rest-history th, .rest-history td { padding: 8px; text-align: left; }
    .rest-tips { padding: 10px; }
    .rest-tips p { margin: 10px 0; }
    </style>
    <?php
}

function dev_tools_rest_api_tester_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.58 20 4 16.42 4 12C4 7.58 7.58 4 12 4C16.42 4 20 7.58 20 12C20 16.42 16.42 20 12 20ZM9 11H7V13H9V11ZM17 11H15V13H17V11ZM13 11H11V13H13V11Z" fill="#0F1729"/>
                </svg>
            </div>
            <h2>REST API Tester</h2>
        </div>
        <p>Test WordPress REST API requests</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-rest-api'); ?>" class="tool-card-btn">to tester</a>
    </div>
    <?php
}