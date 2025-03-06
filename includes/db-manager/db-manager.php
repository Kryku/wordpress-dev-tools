<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_db_manager_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico"><svg xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" viewBox="0 0 600 600" version="1.1" id="svg9724" sodipodi:docname="database.svg" inkscape:version="1.2.2 (1:1.2.2+202212051550+b0a8486541)" width="600" height="600">
  <defs id="defs9728"/>
  <sodipodi:namedview id="namedview9726" pagecolor="#ffffff" bordercolor="#666666" borderopacity="1.0" inkscape:showpageshadow="2" inkscape:pageopacity="0.0" inkscape:pagecheckerboard="0" inkscape:deskcolor="#d1d1d1" showgrid="true" inkscape:zoom="0.42059316" inkscape:cx="148.59966" inkscape:cy="296.01052" inkscape:window-width="1920" inkscape:window-height="1009" inkscape:window-x="0" inkscape:window-y="1080" inkscape:window-maximized="1" inkscape:current-layer="svg9724" showguides="true">
    <inkscape:grid type="xygrid" id="grid9972" originx="0" originy="0"/>
    <sodipodi:guide position="300,-90" orientation="1,0" id="guide385" inkscape:locked="false"/>
    <sodipodi:guide position="140,100" orientation="0,-1" id="guide1388" inkscape:locked="false"/>
    <sodipodi:guide position="140,100" orientation="0,-1" id="guide2256" inkscape:locked="false"/>
    <sodipodi:guide position="0,475" orientation="0,-1" id="guide1920" inkscape:locked="false"/>
  </sodipodi:namedview>
  
  <path id="path3428" style="color:#000000;fill:#010101;stroke-linejoin:round;-inkscape-stroke:none;paint-order:stroke fill markers" d="M 300 0 C 221.30245 0 150.09841 8.0113158 97.068359 21.535156 C 70.553346 28.297076 48.605538 36.277916 31.677734 46.484375 C 16.579982 55.587421 3.2445893 67.928721 0.53125 85 L 0 85 L 0 90 C 0 95.160045 3.6392602 102.94345 17.03125 112.83789 C 30.423241 122.73233 52.11942 133.00486 79.691406 141.62109 C 134.83535 158.85361 213.32376 170 300 170 C 386.67624 170 465.16467 158.85361 520.30859 141.62109 C 547.8806 133.00486 569.57675 122.73233 582.96875 112.83789 C 596.36075 102.94345 600 95.160045 600 90 L 599.87305 90 C 599.19452 70.318664 584.84711 56.447884 568.32227 46.484375 C 551.39442 36.277916 529.44664 28.297076 502.93164 21.535156 C 449.90159 8.0113158 378.69755 0 300 0 z M 0 149.67969 L 0 234.10742 C 0.70499641 239.21983 4.6599347 246.30446 16.722656 255.2168 C 30.11466 265.11125 51.810798 275.38376 79.382812 284 C 134.52681 301.23251 213.01506 312.37891 299.69141 312.37891 C 386.36774 312.37891 464.85602 301.23251 520 284 C 547.57201 275.38376 569.26815 265.11125 582.66016 255.2168 C 596.05215 245.32235 599.69141 237.53895 599.69141 232.37891 L 600 232.37891 L 600 149.67969 C 581.93283 161.57337 559.1282 171.3983 532.24023 179.80078 C 471.56758 198.761 390.05399 210 300 210 C 209.94601 210 128.43244 198.761 67.759766 179.80078 C 40.871811 171.3983 18.067172 161.57337 0 149.67969 z M 600 291.79688 C 590.25148 298.2521 579.18165 304.12941 566.75 309.46875 C 556.06951 314.05598 544.44003 318.27081 531.93164 322.17969 C 471.2589 341.13992 389.74549 352.37891 299.69141 352.37891 C 209.63733 352.37891 128.12391 341.13993 67.451172 322.17969 C 40.720883 313.82647 18.016718 304.0712 0 292.27148 L 0 380 C 0 385.16005 3.6392334 392.94343 17.03125 402.83789 C 30.423267 412.73235 52.119364 423.00484 79.691406 431.62109 C 134.83545 448.85363 213.32358 460 300 460 C 386.67642 460 465.16455 448.85363 520.30859 431.62109 C 547.88068 423.00484 569.57666 412.73235 582.96875 402.83789 C 596.36074 392.94343 600 385.16005 600 380 L 600 291.79688 z M 0 439.67969 L 0 508.59375 L 0 515 L 0.53125 515 C 3.2445947 532.0713 16.579952 544.41257 31.677734 553.51562 C 48.605572 563.7221 70.553292 571.70292 97.068359 578.46484 C 150.09851 591.98873 221.30229 600 300 600 C 378.69771 600 449.90149 591.98873 502.93164 578.46484 C 529.4467 571.70292 551.3944 563.7221 568.32227 553.51562 C 583.42003 544.41257 596.7554 532.0713 599.46875 515 L 600 515 L 600 508.59375 L 600 439.67969 C 581.93278 451.57339 559.1283 461.39828 532.24023 469.80078 C 471.56747 488.76104 390.05417 500 300 500 C 209.94583 500 128.43256 488.76104 67.759766 469.80078 C 40.871757 461.39828 18.067208 451.57339 0 439.67969 z "/>
</svg></div>
            <h2>Database Manager</h2>
        </div>
        <p>View tables and execute SQL queries</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-db-manager'); ?>" 
           class="tool-card-btn">to manager</a>
    </div>
    <?php
}

function dev_tools_db_manager_page() {
    global $wpdb;
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Database Manager</h1>
        <h2>Database tables</h2>
        <?php
        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        if ($tables) {
            echo '<ul class="db-tools-table-list">';
            foreach ($tables as $table) {
                echo '<li>' . esc_html($table[0]) . '</li>';
            }
            echo '</ul>';
        }
        ?>

        <h2>Execute SQL query</h2>
        <form method="post">
            <?php wp_nonce_field('db_manager_query', 'db_manager_nonce'); ?>
            <textarea name="sql_query" rows="5" cols="50" placeholder="Enter the SQL query"></textarea>
            <br>
            <input type="submit" class="db-tools-table-btn" value="Execute request">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['sql_query']) && 
            wp_verify_nonce($_POST['db_manager_nonce'], 'db_manager_query')) {
            
            $query = sanitize_textarea_field($_POST['sql_query']);
            $results = $wpdb->get_results($query);
            
            if ($wpdb->last_error) {
                echo '<div class="error"><p>Error: ' . esc_html($wpdb->last_error) . '</p></div>';
            } elseif ($results) {
                echo '<h3>Results:</h3>';
                echo '<pre class="db-tools-table-result">';
                print_r($results);
                echo '</pre>';
            } else {
                echo '<p>The request was successful, but there are no results.</p>';
            }
        }
        ?>
    </div>
    <?php
}

// Підключення стилів модуля
add_action('admin_enqueue_scripts', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'dev-tools-db-manager') {
        wp_enqueue_style(
            'db-manager-style', 
            plugin_dir_url(__FILE__) . 'style.css'
        );
    }
});