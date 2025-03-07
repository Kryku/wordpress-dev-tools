<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_snippet_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $output = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['snippet_code']) && wp_verify_nonce($_POST['snippet_nonce'], 'snippet_action')) {
        $code = stripslashes($_POST['snippet_code']);
        ob_start();
        try {
            eval('?>' . $code);
            $output = ob_get_clean();
        } catch (Throwable $e) {
            $output = '<div class="error"><p>Error: ' . esc_html($e->getMessage()) . '</p></div>';
            ob_end_clean();
        }
        
        if (preg_match('/\[.*?\]/', $code)) {
            $output .= '<pre>' . do_shortcode($code) . '</pre>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Code Snippet Runner</h1>
        <p>Test your PHP snippets or shortcodes here. It runs once and won’t affect your site.</p>
        <?php if ($output) : ?>
            <div class="snippet-output"><?php echo $output; ?></div>
        <?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('snippet_action', 'snippet_nonce'); ?>
            <textarea id="snippet-editor" name="snippet_code" rows="10" cols="50"><?php echo isset($code) ? esc_textarea($code) : '<?php // Write your code here'; ?></textarea>
            <br>
            <input type="submit" class="tool-card-btn" value="Run Snippet">
        </form>
    </div>
    <script src="<?php echo plugin_dir_url(__FILE__) . '../../assets/codemirror.min.js'; ?>"></script>
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . '../../assets/codemirror.min.css'; ?>">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var editor = CodeMirror.fromTextArea(document.getElementById('snippet-editor'), {
            lineNumbers: true,
            mode: 'php',
            theme: 'default',
            indentUnit: 4,
            tabSize: 4,
            lineWrapping: true
        });
        editor.setSize(null, 400);
    });
    </script>
    <style>
    .snippet-output { margin: 20px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; max-height: 300px; overflow: auto; }
    .snippet-output pre { margin: 0; }
    .snippet-output .error { color: red; }
    </style>
    <?php
}

function dev_tools_snippet_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 16 16" fill="none">
<path d="M8.01005 0.858582L6.01005 14.8586L7.98995 15.1414L9.98995 1.14142L8.01005 0.858582Z" fill="#000000"/>
<path d="M12.5 11.5L11.0858 10.0858L13.1716 8L11.0858 5.91422L12.5 4.5L16 8L12.5 11.5Z" fill="#000000"/>
<path d="M2.82843 8L4.91421 10.0858L3.5 11.5L0 8L3.5 4.5L4.91421 5.91422L2.82843 8Z" fill="#000000"/>
</svg>
            </div>
            <h2>Code Snippet Runner</h2>
        </div>
        <p>Test PHP snippets and shortcodes safely</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-snippet'); ?>" class="tool-card-btn">to runner</a>
    </div>
    <?php
}