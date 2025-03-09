<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_cron_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cron_action']) && wp_verify_nonce($_POST['cron_nonce'], 'cron_action')) {
        $hook = sanitize_text_field($_POST['hook']);
        if ($_POST['cron_action'] === 'run' && !empty($hook)) {
            do_action($hook);
            $message = '<div class="updated"><p>Cron job "' . esc_html($hook) . '" executed successfully!</p></div>';
        } elseif ($_POST['cron_action'] === 'delete' && !empty($hook)) {
            $timestamp = (int)$_POST['timestamp'];
            wp_unschedule_event($timestamp, $hook);
            $message = '<div class="updated"><p>Cron job "' . esc_html($hook) . '" deleted!</p></div>';
        } elseif ($_POST['cron_action'] === 'add' && !empty($hook)) {
            $timestamp = strtotime($_POST['schedule_time']);
            $recurrence = sanitize_text_field($_POST['recurrence']);
            if ($timestamp && $recurrence) {
                wp_schedule_event($timestamp, $recurrence, $hook);
                $message = '<div class="updated"><p>Cron job "' . esc_html($hook) . '" scheduled!</p></div>';
            } else {
                $message = '<div class="error"><p>Invalid time or recurrence!</p></div>';
            }
        }
    }

    $cron_jobs = _get_cron_array();
    $schedules = wp_get_schedules();
    ?>
    <div class="wrap">
        <h1>Cron Scheduler</h1>
        <p>View, run, or manage WordPress cron jobs.</p>
        <?php if ($message) echo $message; ?>
        <div style="display: flex; gap: 20px;">
        <div class="tool-card" style="width: 20%; max-width: 20%; max-height: 300px">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Add New Cron Job</h2>
            </div>
            <form method="post" class="cron-form">
                <?php wp_nonce_field('cron_action', 'cron_nonce'); ?>
                <input type="hidden" name="cron_action" value="add">
                <p>
                    <label>Hook Name:</label><br>
                    <input type="text" name="hook" required placeholder="my_custom_cron_hook">
                </p>
                <p>
                    <label>Schedule Time:</label><br>
                    <input type="datetime-local" name="schedule_time" required>
                </p>
                <p>
                    <label>Recurrence:</label><br>
                    <select name="recurrence" required>
                        <option value="">Select recurrence</option>
                        <?php
                        foreach ($schedules as $key => $schedule) {
                            echo '<option value="' . esc_attr($key) . '">' . esc_html($schedule['display']) . '</option>';
                        }
                        ?>
                        <option value="none">One-time</option>
                    </select>
                </p>
                <input type="submit" class="tool-card-btn" value="Schedule Job">
            </form>
        </div>
        <div class="tool-card"  style="width: 80%; max-width: 80%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Scheduled Tasks</h2>
            </div>
            <div class="cron-table">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Hook</th>
                            <th>Next Run</th>
                            <th>Recurrence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($cron_jobs) {
                            foreach ($cron_jobs as $timestamp => $cron) {
                                foreach ($cron as $hook => $events) {
                                    foreach ($events as $event) {
                                        $recurrence = isset($event['schedule']) && isset($schedules[$event['schedule']]) ? $schedules[$event['schedule']]['display'] : 'One-time';
                                        ?>
                                        <tr>
                                            <td><?php echo esc_html($hook); ?></td>
                                            <td><?php echo date('Y-m-d H:i:s', $timestamp); ?></td>
                                            <td><?php echo esc_html($recurrence); ?></td>
                                            <td>
                                                <form method="post" style="display:inline;">
                                                    <?php wp_nonce_field('cron_action', 'cron_nonce'); ?>
                                                    <input type="hidden" name="hook" value="<?php echo esc_attr($hook); ?>">
                                                    <input type="hidden" name="timestamp" value="<?php echo esc_attr($timestamp); ?>">
                                                    <input type="hidden" name="cron_action" value="run">
                                                    <input type="submit" class="tool-card-btn" value="Run Now">
                                                </form>
                                                <form method="post" style="display:inline;">
                                                    <?php wp_nonce_field('cron_action', 'cron_nonce'); ?>
                                                    <input type="hidden" name="hook" value="<?php echo esc_attr($hook); ?>">
                                                    <input type="hidden" name="timestamp" value="<?php echo esc_attr($timestamp); ?>">
                                                    <input type="hidden" name="cron_action" value="delete">
                                                    <input type="submit" class="tool-card-btn delete-btn" value="Delete" onclick="return confirm('Are you sure?');">
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        } else {
                            echo '<tr><td colspan="4">No scheduled tasks found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
    <style>
    .cron-table { padding: 10px; }
    .cron-table table { width: 100%; }
    .cron-table th, .cron-table td { padding: 8px; text-align: left; }
    .cron-form { padding: 10px; }
    .cron-form p { margin: 10px 0; }
    .cron-form label { font-weight: bold; }
    .cron-form input[type="text"], .cron-form input[type="datetime-local"], .cron-form select { width: 100%; max-width: 300px; padding: 5px; }
    .tool-card-btn.delete-btn { background: #d63638; border-color: #d63638; }
    .tool-card-btn.delete-btn:hover { background: #b32d2e; }
    </style>
    <?php
}

function dev_tools_cron_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
<path fill-rule="evenodd" clip-rule="evenodd" d="M12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.8284 6.75736C12.3807 6.75736 12.8284 7.20507 12.8284 7.75736V12.7245L16.3553 14.0653C16.8716 14.2615 17.131 14.8391 16.9347 15.3553C16.7385 15.8716 16.1609 16.131 15.6447 15.9347L11.4731 14.349C11.085 14.2014 10.8284 13.8294 10.8284 13.4142V7.75736C10.8284 7.20507 11.2761 6.75736 11.8284 6.75736Z" fill="#0F1729"/>
</svg>
            </div>
            <h2>Cron Scheduler</h2>
        </div>
        <p>Manage and test WordPress cron jobs</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-cron'); ?>" class="tool-card-btn">to scheduler</a>
    </div>
    <?php
}