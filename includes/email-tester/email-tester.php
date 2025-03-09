<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_email_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    $email_logs = get_option('dev_tools_email_logs', []);

    $mailserver_url = get_option('mailserver_url');
    $mailserver_login = get_option('mailserver_login');
    $mailserver_pass = get_option('mailserver_pass');
    $mailserver_port = get_option('mailserver_port');
    $admin_email = get_option('admin_email');
    $pop3_enabled = ($mailserver_url && $mailserver_login && $mailserver_pass && $mailserver_port);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_action']) && wp_verify_nonce($_POST['email_nonce'], 'email_action')) {
        $to = sanitize_email($_POST['to']);
        $subject = sanitize_text_field($_POST['subject']);
        $body = wp_kses_post($_POST['body']);
        $format = isset($_POST['format']) && $_POST['format'] === 'html' ? 'text/html' : 'text/plain';

        if ($_POST['email_action'] === 'send') {
            if (isset($_POST['use_pop3']) && $_POST['use_pop3'] === 'yes' && $pop3_enabled && function_exists('imap_open')) {
                // Відправка через POP3-сервер із налаштувань
                $server = '{' . $mailserver_url . ':' . $mailserver_port . '/pop3}INBOX';
                $connection = @imap_open($server, $mailserver_login, $mailserver_pass, OP_HALFOPEN);
                if ($connection) {
                    $headers = [
                        'From: ' . $admin_email,
                        'Subject: ' . $subject,
                        'Content-Type: ' . $format . '; charset=UTF-8',
                        'Date: ' . date('r')
                    ];
                    $message_body = implode("\r\n", $headers) . "\r\n\r\n" . $body;
                    $sent = imap_mail($to, $subject, $body, implode("\r\n", $headers));
                    imap_close($connection);
                } else {
                    $sent = false;
                    $error = imap_last_error();
                }
            } else {
                $headers = ['Content-Type: ' . $format . '; charset=UTF-8'];
                $sent = wp_mail($to, $subject, $body, $headers);
            }

            $log_entry = [
                'to' => $to,
                'subject' => $subject,
                'time' => current_time('mysql'),
                'status' => $sent ? 'success' : 'failed',
                'method' => (isset($_POST['use_pop3']) && $_POST['use_pop3'] === 'yes') ? 'POP3' : 'wp_mail'
            ];
            if (isset($error)) {
                $log_entry['error'] = $error;
            }
            $email_logs[] = $log_entry;
            update_option('dev_tools_email_logs', array_slice($email_logs, -10));

            $message = $sent ? 
                '<div class="updated"><p>Email sent successfully to ' . esc_html($to) . '!</p></div>' : 
                '<div class="error"><p>Failed to send email. Check logs or SMTP/POP3 settings.</p></div>';
        } elseif ($_POST['email_action'] === 'clear_logs') {
            update_option('dev_tools_email_logs', []);
            $email_logs = [];
            $message = '<div class="updated"><p>Email logs cleared!</p></div>';
        } elseif ($_POST['email_action'] === 'delete_selected' && !empty($_POST['selected_logs'])) {
            $selected = array_map('intval', $_POST['selected_logs']);
            $email_logs = array_values(array_filter($email_logs, function($key) use ($selected) {
                return !in_array($key, $selected);
            }, ARRAY_FILTER_USE_KEY));
            update_option('dev_tools_email_logs', $email_logs);
            $message = '<div class="updated"><p>Selected logs deleted!</p></div>';
        } elseif ($_POST['email_action'] === 'resend_selected' && !empty($_POST['selected_logs'])) {
            $selected = array_map('intval', $_POST['selected_logs']);
            foreach ($selected as $index) {
                if (isset($email_logs[$index])) {
                    $log = $email_logs[$index];
                    if ($log['method'] === 'POP3' && $pop3_enabled && function_exists('imap_open')) {
                        $server = '{' . $mailserver_url . ':' . $mailserver_port . '/pop3}INBOX';
                        $connection = @imap_open($server, $mailserver_login, $mailserver_pass, OP_HALFOPEN);
                        if ($connection) {
                            $headers = [
                                'From: ' . $admin_email,
                                'Subject: ' . $log['subject'],
                                'Content-Type: text/plain; charset=UTF-8',
                                'Date: ' . date('r')
                            ];
                            $sent = imap_mail($log['to'], $log['subject'], $log['time'] . "\n\nResent from Dev Tools", implode("\r\n", $headers));
                            imap_close($connection);
                        } else {
                            $sent = false;
                        }
                    } else {
                        $headers = ['Content-Type: text/plain; charset=UTF-8'];
                        $sent = wp_mail($log['to'], $log['subject'], $log['time'] . "\n\nResent from Dev Tools", $headers);
                    }
                    $email_logs[] = [
                        'to' => $log['to'],
                        'subject' => $log['subject'],
                        'time' => current_time('mysql'),
                        'status' => $sent ? 'success' : 'failed',
                        'method' => $log['method']
                    ];
                }
            }
            update_option('dev_tools_email_logs', array_slice($email_logs, -10));
            $message = '<div class="updated"><p>Selected emails resent!</p></div>';
        }
    }

    add_action('wp_mail_failed', function($wp_error) use (&$email_logs) {
        $log_entry = [
            'to' => $wp_error->get_error_data('to') ?? 'unknown',
            'subject' => $wp_error->get_error_data('subject') ?? 'unknown',
            'time' => current_time('mysql'),
            'status' => 'failed',
            'method' => 'wp_mail',
            'error' => $wp_error->get_error_message()
        ];
        $email_logs[] = $log_entry;
        update_option('dev_tools_email_logs', array_slice($email_logs, -10));
    });

    ?>
    <div class="wrap">
        <h1>Email Tester</h1>
        <p>Test email sending from WordPress.</p>
        <?php if ($message) echo $message; ?>
        <div style="display: flex; gap: 20px;">
        <div class="tool-card" style="width: 20%; max-width: 20%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Send Test Email</h2>
            </div>
            <form method="post" class="email-form">
                <?php wp_nonce_field('email_action', 'email_nonce'); ?>
                <input type="hidden" name="email_action" value="send">
                <p>
                    <label>To:</label><br>
                    <input type="email" name="to" required placeholder="recipient@example.com" value="<?php echo esc_attr($admin_email); ?>">
                    <?php if ($pop3_enabled && function_exists('imap_open')): ?>
                    <br><br><label><input type="checkbox" name="use_pop3" value="yes"> Use Post via Email settings (POP3: <?php echo esc_html($mailserver_url); ?>)</label>
                    <?php else: ?>
                    <label><input type="checkbox" name="use_pop3" value="yes" disabled> Use Post via Email settings (not configured or imap not available)</label>
                    <?php endif; ?>
                </p>
                <p>
                    <label>Subject:</label><br>
                    <input type="text" name="subject" required placeholder="Test Email" value="Test Email from Dev Tools">
                </p>
                <p>
                    <label>Message:</label><br>
                    <textarea name="body" rows="5" required placeholder="This is a test email from Dev Tools."><?php echo esc_textarea("This is a test email from Dev Tools.\nSent on: " . current_time('mysql')); ?></textarea>
                </p>
                <p>
                    <label>Format:</label><br>
                    <select name="format">
                        <option value="plain">Plain Text</option>
                        <option value="html">HTML</option>
                    </select>
                </p>
                <input type="submit" class="tool-card-btn" value="Send Email">
            </form>
        </div>

        <div class="tool-card" style="width: 80%; max-width: 80%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Email Logs</h2>
            </div>
            <div class="email-logs">
                <form method="post" style="margin-bottom: 10px;">
                    <?php wp_nonce_field('email_action', 'email_nonce'); ?>
                    <input type="hidden" name="email_action" value="clear_logs">
                    <input type="submit" class="tool-card-btn delete-btn" value="Clear Logs" onclick="return confirm('Are you sure you want to clear all logs?');">
                </form>
                <form method="post" class="email-logs-form">
                    <?php wp_nonce_field('email_action', 'email_nonce'); ?>
                    <input type="hidden" name="email_action" value="delete_selected" id="delete_selected_action">
                    <p>
                        <input type="submit" class="tool-card-btn delete-btn" value="Delete Selected" onclick="return confirm('Are you sure you want to delete selected logs?');" style="margin-bottom: 10px;">
                        <input type="submit" class="tool-card-btn" value="Resend Selected" onclick="document.getElementById('delete_selected_action').value='resend_selected'; return confirm('Resend selected emails?');">
                    </p>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select_all"></th>
                                <th>To</th>
                                <th>Subject</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($email_logs) {
                                foreach (array_reverse($email_logs) as $index => $log) {
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_logs[]" value="<?php echo $index; ?>"></td>
                                        <td><?php echo esc_html($log['to']); ?></td>
                                        <td><?php echo esc_html($log['subject']); ?></td>
                                        <td><?php echo esc_html($log['time']); ?></td>
                                        <td>
                                            <?php 
                                            if ($log['status'] === 'success') {
                                                echo '<span style="color: green;">Success</span>';
                                            } else {
                                                echo '<span style="color: red;">Failed' . (isset($log['error']) ? ' - ' . esc_html($log['error']) : '') . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo esc_html($log['method']); ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="6">No email logs found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
                <script>
                    document.getElementById('select_all').addEventListener('change', function() {
                        document.querySelectorAll('input[name="selected_logs[]"]').forEach(cb => cb.checked = this.checked);
                    });
                </script>
            </div>
            </div>
        </div>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Tips</h2>
            </div>
            <div class="email-tips">
                <p><strong>SMTP Issues?</strong> If wp_mail fails, consider configuring an SMTP plugin (e.g., WP Mail SMTP).</p>
                <p><strong>POP3 Setup:</strong> Configure "Post via Email" in <a href="<?php echo admin_url('options-writing.php'); ?>">Settings > Writing</a> for POP3 sending.</p>
                <p><strong>Debugging:</strong> Enable <code>WP_DEBUG</code> in <code>wp-config.php</code> for more details on failures.</p>
            </div>
        </div>
    </div>
    <style>
    .email-form { padding: 10px; }
    .email-form p { margin: 10px 0; }
    .email-form label { font-weight: bold; }
    .email-form input[type="email"], .email-form input[type="text"], .email-form textarea, .email-form select { 
        width: 100%; max-width: 400px; padding: 5px; 
    }
    .email-form textarea { height: 100px; }
    .email-logs { padding: 10px; }
    .email-logs table { width: 100%; }
    .email-logs th, .email-logs td { padding: 8px; text-align: left; }
    .email-logs-form p { margin: 10px 0; }
    .email-tips { padding: 10px; }
    .email-tips p { margin: 10px 0; }
    .tool-card-btn.delete-btn { background: #d63638; border-color: #d63638; }
    .tool-card-btn.delete-btn:hover { background: #b32d2e; }
    </style>
    <?php
}

function dev_tools_email_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
                    <path d="M20 4H4C2.9 4 2.01 4.9 2.01 6L2 18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 18H4V8L12 13L20 8V18ZM12 11L4 6H20L12 11Z" fill="#0F1729"/>
                </svg>
            </div>
            <h2>Email Tester</h2>
        </div>
        <p>Test email sending from WordPress</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-email'); ?>" class="tool-card-btn">to tester</a>
    </div>
    <?php
}