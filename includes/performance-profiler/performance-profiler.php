<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_performance_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    if (!defined('SAVEQUERIES') || !SAVEQUERIES) {
        define('SAVEQUERIES', true);
    }

    $load_time = timer_stop(0, 3);
    $query_count = $wpdb->num_queries;
    $memory_usage = size_format(memory_get_usage());
    $peak_memory = size_format(memory_get_peak_usage());

    $slow_queries = [];
    if (!empty($wpdb->queries)) {
        foreach ($wpdb->queries as $query) {
            $sql = $query[0];
            $time = $query[1];
            $slow_queries[] = [
                'sql' => $sql,
                'time' => number_format($time, 4),
            ];
        }
        usort($slow_queries, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        $slow_queries = array_slice($slow_queries, 0, 10);
    }

    ?>
    <div class="wrap">
        <h1>Performance Profiler</h1>
        <p>Analyze performance metrics for the current page.</p>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Performance Metrics</h2>
            </div>
            <div class="perf-metrics">
                <table class="wp-list-table widefat fixed striped">
                    <tbody>
                        <tr>
                            <th>Page Load Time</th>
                            <td><?php echo esc_html($load_time); ?> seconds</td>
                        </tr>
                        <tr>
                            <th>Database Queries</th>
                            <td><?php echo esc_html($query_count); ?></td>
                        </tr>
                        <tr>
                            <th>Memory Usage</th>
                            <td><?php echo esc_html($memory_usage); ?></td>
                        </tr>
                        <tr>
                            <th>Peak Memory Usage</th>
                            <td><?php echo esc_html($peak_memory); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($slow_queries)) : ?>
        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Top 10 Slowest Queries</h2>
            </div>
            <div class="perf-queries">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Query</th>
                            <th>Time (seconds)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slow_queries as $query) : ?>
                        <tr>
                            <td><pre><?php echo esc_html($query['sql']); ?></pre></td>
                            <td><?php echo esc_html($query['time']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div class="tool-card" style="width: 100%; max-width: 100%;">
            <div class="tool-card-header">
                <div class="tool-card-ico"></div>
                <h2>Profiling Tips</h2>
            </div>
            <div class="perf-tips">
                <p><strong>Note:</strong> For accurate query profiling, ensure <code>define('SAVEQUERIES', true);</code> is set in <code>wp-config.php</code>.</p>
            </div>
        </div>
    </div>
    <style>
    .perf-metrics, .perf-queries, .perf-tips { padding: 10px; }
    .perf-metrics table, .perf-queries table { width: 100%; }
    .perf-metrics th, .perf-queries th { width: 30%; padding: 8px; text-align: left; }
    .perf-metrics td, .perf-queries td { padding: 8px; }
    .perf-queries pre { margin: 0; font-size: 12px; white-space: pre-wrap; word-wrap: break-word; }
    .perf-tips p { margin: 10px 0; }
    </style>
    <?php
}

add_action('wp_footer', 'dev_tools_profile_footer');
add_action('admin_footer', 'dev_tools_profile_footer');
function dev_tools_profile_footer() {
    if (!current_user_can('manage_options') || !isset($_GET['profile'])) {
        return;
    }

    global $wpdb;
    $load_time = timer_stop(0, 3);
    $query_count = $wpdb->num_queries;
    $memory_usage = size_format(memory_get_usage());
    $peak_memory = size_format(memory_get_peak_usage());
    ?>
    <div style="position: fixed; bottom: 0; left: 0; background: #fff; padding: 10px; border: 1px solid #ddd; z-index: 9999;">
        <strong>Performance:</strong> 
        Load Time: <?php echo esc_html($load_time); ?>s | 
        Queries: <?php echo esc_html($query_count); ?> | 
        Memory: <?php echo esc_html($memory_usage); ?> | 
        Peak: <?php echo esc_html($peak_memory); ?>
    </div>
    <?php
}

function dev_tools_performance_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 1024 1024" class="icon" version="1.1"><path d="M981.333333 469.333333a21.333333 21.333333 0 0 0-21.333333 21.333334v85.333333a85.333333 85.333333 0 0 1-85.333333 85.333333H149.333333a85.333333 85.333333 0 0 1-85.333333-85.333333V149.333333a85.333333 85.333333 0 0 1 85.333333-85.333333h725.333334a85.333333 85.333333 0 0 1 85.333333 85.333333 21.333333 21.333333 0 0 0 42.666667 0 128 128 0 0 0-128-128H149.333333a128 128 0 0 0-128 128v426.666667a128 128 0 0 0 128 128h725.333334a128 128 0 0 0 128-128v-85.333333a21.333333 21.333333 0 0 0-21.333334-21.333334z" fill="#231815"/><path d="M981.333333 298.666667a21.333333 21.333333 0 0 0-21.333333 21.333333v85.333333a21.333333 21.333333 0 0 0 42.666667 0v-85.333333a21.333333 21.333333 0 0 0-21.333334-21.333333zM966.186667 219.52a21.333333 21.333333 0 0 0 0 30.293333 21.333333 21.333333 0 0 0 30.293333 0l2.56-3.413333a11.946667 11.946667 0 0 0 1.92-3.626667 13.226667 13.226667 0 0 0 1.706667-3.84 26.88 26.88 0 0 0 0-4.266666 21.333333 21.333333 0 0 0-36.48-15.146667zM896 960H128a21.333333 21.333333 0 0 0 0 42.666667h768a21.333333 21.333333 0 0 0 0-42.666667zM682.666667 853.333333a21.333333 21.333333 0 0 0 0-42.666666H341.333333a21.333333 21.333333 0 0 0 0 42.666666zM704 384a21.333333 21.333333 0 0 0 0-42.666667h-44.373333a149.333333 149.333333 0 0 0-28.16-67.84l31.36-31.36a21.333333 21.333333 0 1 0-30.08-30.08l-31.573334 31.146667A149.333333 149.333333 0 0 0 533.333333 215.04V170.666667a21.333333 21.333333 0 0 0-42.666666 0v44.373333a149.333333 149.333333 0 0 0-67.84 28.16l-31.573334-31.36a21.333333 21.333333 0 0 0-30.08 30.08l31.36 31.36A149.333333 149.333333 0 0 0 364.373333 341.333333H320a21.333333 21.333333 0 0 0 0 42.666667h44.373333a149.333333 149.333333 0 0 0 28.16 67.84l-31.36 31.36a21.333333 21.333333 0 1 0 30.08 30.08l31.36-31.36A149.333333 149.333333 0 0 0 490.666667 510.293333V554.666667a21.333333 21.333333 0 0 0 42.666666 0v-44.373334a149.333333 149.333333 0 0 0 67.84-28.16l31.36 31.36a21.333333 21.333333 0 0 0 30.08-30.08l-31.146666-31.573333A149.333333 149.333333 0 0 0 659.626667 384z m-192 85.333333a106.666667 106.666667 0 1 1 106.666667-106.666666 106.666667 106.666667 0 0 1-106.666667 106.666666z" fill="#231815"/></svg>
            </div>
            <h2>Performance Profiler</h2>
        </div>
        <p>Analyze page load time and resource usage</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-performance'); ?>" class="tool-card-btn">to profiler</a>
    </div>
    <?php
}