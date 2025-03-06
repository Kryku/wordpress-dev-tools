<?php
if (!defined('ABSPATH')) {
    exit;
}

function dev_tools_file_editor_card() {
    ?>
    <div class="tool-card">
        <div class="tool-card-header">
            <div class="tool-card-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 16 16" fill="none"> <path d="M7 0H2V16H14V7H7V0Z" fill="#000000"/> <path d="M9 0V5H14L9 0Z" fill="#000000"/> </svg>
            </div>
            <h2>File Editor</h2>
        </div>
        <p>Editing files with a directory tree</p>
        <a href="<?php echo admin_url('admin.php?page=dev-tools-file-editor'); ?>" 
           class="tool-card-btn">to editor</a>
    </div>
    <?php
}

function dev_tools_file_editor_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $root_dir = ABSPATH;
    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
        isset($_POST['file_content']) && 
        wp_verify_nonce($_POST['file_editor_nonce'], 'file_editor_action')) {
        
        $file_path = sanitize_text_field($_POST['file_path']);
        $content = wp_unslash($_POST['file_content']);
        
        if (is_writable($file_path)) {
            file_put_contents($file_path, $content);
            $message = '<div class="updated"><p>File saved!</p></div>';
        } else {
            $message = '<div class="error"><p>Error: File not available for writing</p></div>';
        }
    }

    add_action('wp_ajax_rename_file', function() {
        check_ajax_referer('file_editor_action');
        $old_name = sanitize_text_field($_POST['old_name']);
        $new_name = sanitize_text_field($_POST['new_name']);
        if (rename($old_name, $new_name)) {
            wp_send_json_success('File renamed');
        } else {
            wp_send_json_error('Rename error');
        }
    });
    ?>
    <div class="wrap">
        <h1>File Editor</h1>
        <?php echo $message; ?>
        <div class="file-editor-container">
            <div class="file-tree">
                <?php echo build_file_tree($root_dir); ?>
            </div>
            <div class="file-content">
                <?php
                $file = isset($_GET['file']) ? realpath($_GET['file']) : '';
                if ($file && file_exists($file) && strpos($file, $root_dir) === 0) {
                    $content = file_get_contents($file);
                    ?>
                    <form method="post" class="file-editor-form">
                        <?php wp_nonce_field('file_editor_action', 'file_editor_nonce'); ?>
                        <input type="hidden" name="file_path" value="<?php echo esc_attr($file); ?>">
                        <textarea id="code-editor" name="file_content"><?php echo esc_textarea($content); ?></textarea>
                        <div class="file-editor-btns">
                            <button id="rename-file" class="file-editor-rename">Rename</button>
                            <input type="submit" class="file-editor-submit" value="Save">
                        </div>
                    </form>
                    <?php
                } else {
                    echo '<p>Select the file to edit</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
            lineNumbers: true,
            mode: 'php',
            theme: 'default'
        });

        $('.file-tree .folder').click(function(e) {
            e.stopPropagation();
            $(this).children('ul').toggle();
            $(this).toggleClass('collapsed');
        });

        $('#rename-file').click(function() {
            var filePath = $('input[name="file_path"]').val();
            var newName = prompt('Enter a new file name:', filePath);
            if (newName) {
                $.post(ajaxurl, {
                    action: 'rename_file',
                    old_name: filePath,
                    new_name: newName,
                    _ajax_nonce: '<?php echo wp_create_nonce('file_editor_action'); ?>'
                }, function(response) {
                    if (response.success) {
                        alert(response.data);
                        window.location.href = '<?php echo admin_url('admin.php?page=dev-tools-file-editor&file='); ?>' + newName;
                    } else {
                        alert(response.data);
                    }
                });
            }
        });
    });
    </script>
    <?php
}

function build_file_tree($dir) {
    $output = '<ul>';
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $output .= '<li class="folder">' . esc_html($file);
            $output .= build_file_tree($path); // Рекурсія для підпапок
            $output .= '</li>';
        } else {
            $output .= '<li class="file"><a href="' . admin_url('admin.php?page=dev-tools-file-editor&file=' . urlencode($path)) . '">' . esc_html($file) . '</a></li>';
        }
    }
    $output .= '</ul>';
    return $output;
}

add_action('admin_enqueue_scripts', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'dev-tools-file-editor') {
        wp_enqueue_script('jquery');
        wp_enqueue_script('codemirror', plugin_dir_url(__FILE__) . 'assets/codemirror.min.js', [], '6.65.7', true);
        wp_enqueue_style('codemirror-css', plugin_dir_url(__FILE__) . 'assets/codemirror.min.css');
        wp_enqueue_style('file-editor-style', plugin_dir_url(__FILE__) . 'style.css');
    }
});