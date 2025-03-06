<?php

$root_dir = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$wp_load = $root_dir . '/wp-load.php';

if (!file_exists($wp_load)) {
    $debug_message = 'Error: Could not load WordPress. Expected wp-load.php at: ' . $wp_load;
    http_response_code(500);
    die($debug_message);
}

require_once $wp_load;

$sos_enabled = get_option('dev_tools_sos_enabled', false);
if (!$sos_enabled) {
    http_response_code(403);
    die('SOS is disabled. Enable it in Dev Tools > SOS Settings in WordPress admin.');
}

$username = 'admin';
$password = 'ohf*k';
if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] !== $username || 
    $_SERVER['PHP_AUTH_PW'] !== $password) {
    header('WWW-Authenticate: Basic realm="SOS Access"');
    header('HTTP/1.0 401 Unauthorized');
    die('Access denied. Please provide valid credentials.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_content'])) {
    $file_path = realpath($_POST['file_path']);
    if ($file_path && strpos($file_path, $root_dir) === 0 && is_writable($file_path)) {
        $content = stripslashes($_POST['file_content']);
        file_put_contents($file_path, $content);
        $message = '<div class="success">File saved successfully!</div>';
    } else {
        $message = '<div class="error">Error: Cannot save file.</div>';
    }
}

function build_sos_direct_file_tree($dir) {
    $output = '<ul>';
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $output .= '<li class="folder">' . htmlspecialchars($file) . build_sos_direct_file_tree($path) . '</li>';
        } else {
            $output .= '<li class="file"><a href="?file=' . urlencode($path) . '">' . htmlspecialchars($file) . '</a></li>';
        }
    }
    $output .= '</ul>';
    return $output;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Save Our Souls - File Editor</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/codemirror.min.css">
    <script src="assets/codemirror.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Save Our Souls - Emergency File Editor <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1">
<title>hands-pray</title>
<path d="M25.063 15.214c-0.458-1.030-0.941-1.905-1.49-2.732l0.043 0.068c-0.174-0.282-0.348-0.563-0.518-0.849-0.807-1.36-1.51-2.448-2.215-3.425-0.342-0.479-0.679-0.897-1.037-1.294l0.011 0.013c-0.267-0.326-0.6-0.587-0.981-0.763l-0.017-0.007c-0.081-0.031-0.175-0.050-0.274-0.050-0.055 0-0.108 0.006-0.159 0.016l0.005-0.001c-0.88 0.128-1.615 0.658-2.021 1.395l-0.007 0.014c-0.155 0.365-0.245 0.79-0.245 1.236 0 0.496 0.111 0.967 0.311 1.387l-0.008-0.020 2.077 4.622c-1.166 0.027-2.102 0.979-2.102 2.149 0 0.057 0.002 0.113 0.007 0.169l-0-0.007v3.11l-0.435 1.485-0.435-1.485v-3.11c0.004-0.048 0.006-0.104 0.006-0.16 0-1.171-0.936-2.123-2.1-2.148l-0.002-0 2.072-4.613c0.194-0.404 0.307-0.878 0.307-1.379 0-0.445-0.090-0.87-0.252-1.256l0.008 0.021c-0.413-0.751-1.148-1.282-2.013-1.408l-0.015-0.002c-0.047-0.010-0.1-0.015-0.155-0.015-0.099 0-0.193 0.018-0.28 0.051l0.005-0.002c-0.405 0.189-0.744 0.457-1.010 0.787l-0.004 0.005c-0.342 0.379-0.672 0.79-0.979 1.22l-0.028 0.042c-0.707 0.977-1.411 2.064-2.216 3.424-0.167 0.281-0.34 0.561-0.512 0.84-0.509 0.762-0.994 1.639-1.406 2.557l-0.047 0.116c-0.459 1.106-0.726 2.39-0.726 3.737 0 1.178 0.204 2.309 0.579 3.358l-0.022-0.070c0.452 1.090 0.82 2.367 1.044 3.692l0.015 0.107h-0.69c-0.414 0-0.75 0.336-0.75 0.75v0 3.211c0 0.414 0.336 0.75 0.75 0.75h17.712c0.414-0 0.75-0.336 0.75-0.75v0-3.211c-0-0.414-0.336-0.75-0.75-0.75h-0.691c0.244-1.444 0.616-2.731 1.114-3.951l-0.045 0.123c0.347-0.971 0.548-2.092 0.548-3.259 0-1.347-0.267-2.632-0.752-3.805l0.024 0.066zM8.187 21.715c-0.289-0.823-0.456-1.771-0.456-2.758 0-1.143 0.224-2.234 0.63-3.231l-0.021 0.057c0.424-0.948 0.871-1.753 1.379-2.514l-0.039 0.062c0.178-0.287 0.355-0.575 0.527-0.865 0.78-1.318 1.461-2.369 2.139-3.307 0.305-0.429 0.606-0.804 0.926-1.16l-0.010 0.011c0.083-0.089 0.184-0.199 0.276-0.28 0.31 0.075 0.566 0.267 0.724 0.525l0.003 0.005c0.059 0.168 0.092 0.363 0.092 0.565 0 0.277-0.064 0.539-0.177 0.773l0.005-0.011-2.892 6.436c-0.041 0.090-0.065 0.196-0.065 0.308v0 6.437c0 0.414 0.336 0.75 0.75 0.75s0.75-0.336 0.75-0.75v0-5.632c0-1.271 1.343-1.268 1.345 0v3.218c0 0.001 0 0.002 0 0.003 0 0.074 0.011 0.146 0.032 0.213l-0.001-0.005 1.147 3.918v1.557h-5.902c-0.236-1.618-0.642-3.074-1.207-4.449l0.045 0.124zM24.113 29.25h-16.212v-1.711h16.212zM23.838 21.686c-0.522 1.258-0.931 2.724-1.158 4.25l-0.013 0.104h-5.902v-1.557l1.147-3.918c0.019-0.062 0.030-0.134 0.030-0.208 0-0.001 0-0.002 0-0.003v0-3.218c-0.001-0.969 0.784-1.019 1.118-0.678 0.142 0.163 0.229 0.378 0.229 0.613 0 0.023-0.001 0.045-0.002 0.067l0-0.003v5.632c0 0.414 0.336 0.75 0.75 0.75s0.75-0.336 0.75-0.75v0-6.437c-0-0.111-0.025-0.217-0.068-0.312l0.002 0.005-2.896-6.446c-0.104-0.221-0.165-0.481-0.165-0.754 0-0.199 0.032-0.391 0.092-0.57l-0.004 0.013c0.16-0.266 0.418-0.46 0.721-0.534l0.008-0.002c0.094 0.084 0.201 0.199 0.295 0.303 0.303 0.337 0.597 0.704 0.871 1.087l0.026 0.038c0.676 0.938 1.357 1.988 2.139 3.309 0.174 0.293 0.354 0.582 0.531 0.872 0.467 0.697 0.914 1.5 1.294 2.338l0.044 0.107c0.386 0.939 0.61 2.029 0.61 3.172 0 0.977-0.164 1.916-0.466 2.791l0.018-0.060zM6.671 12.335c0.050-0.098 0.079-0.213 0.079-0.335 0-0.292-0.167-0.545-0.411-0.669l-0.004-0.002-4-2c-0.098-0.050-0.213-0.079-0.335-0.079-0.414 0-0.75 0.336-0.75 0.75 0 0.292 0.167 0.545 0.411 0.669l0.004 0.002 4 2c0.098 0.050 0.213 0.079 0.335 0.079 0.292 0 0.545-0.167 0.669-0.411l0.002-0.004zM7.47 8.53c0.136 0.14 0.327 0.227 0.538 0.227 0.414 0 0.75-0.336 0.75-0.75 0-0.211-0.087-0.401-0.227-0.537l-6-6c-0.135-0.131-0.32-0.212-0.523-0.212-0.414 0-0.75 0.336-0.75 0.75 0 0.203 0.081 0.388 0.213 0.523l-0-0zM29.664 9.329l-4 2c-0.248 0.126-0.414 0.379-0.414 0.671 0 0.414 0.336 0.75 0.75 0.75 0.122 0 0.238-0.029 0.34-0.081l-0.004 0.002 4-2c0.248-0.126 0.414-0.379 0.414-0.671 0-0.414-0.336-0.75-0.75-0.75-0.122 0-0.238 0.029-0.34 0.081l0.004-0.002zM24 8.75c0 0 0 0 0.001 0 0.207 0 0.395-0.084 0.531-0.22l6-6c0.134-0.136 0.218-0.322 0.218-0.528 0-0.415-0.336-0.751-0.751-0.751-0.207 0-0.394 0.083-0.529 0.218l-6 6c-0.136 0.136-0.22 0.323-0.22 0.53 0 0.414 0.336 0.75 0.75 0.75 0 0 0.001 0 0.001 0h-0zM20.621 4.648c0.1 0.054 0.219 0.085 0.345 0.085 0 0 0.001 0 0.001 0h-0c0.288-0 0.538-0.162 0.664-0.399l0.002-0.004 1.033-1.983c0.052-0.1 0.083-0.218 0.083-0.343 0-0.415-0.336-0.751-0.751-0.751-0.287 0-0.536 0.16-0.662 0.396l-0.002 0.004-1.033 1.983c-0.053 0.1-0.084 0.219-0.084 0.346 0 0.288 0.162 0.538 0.4 0.664l0.004 0.002zM10.368 4.33c0.129 0.237 0.376 0.395 0.661 0.395 0.414 0 0.75-0.336 0.75-0.75 0-0.123-0.030-0.24-0.083-0.343l0.002 0.004-1.033-1.983c-0.129-0.237-0.376-0.395-0.661-0.395-0.414 0-0.75 0.336-0.75 0.75 0 0.123 0.030 0.24 0.083 0.343l-0.002-0.004z"/>
</svg></h1>
        <?php if (isset($message)) echo $message; ?>
        <div class="file-editor">
            <div class="file-tree">
                <?php echo build_sos_direct_file_tree($root_dir); ?>
            </div>
            <div class="file-content">
                <?php
                $file = isset($_GET['file']) ? realpath($_GET['file']) : '';
                if ($file && file_exists($file) && strpos($file, $root_dir) === 0) {
                    $content = file_get_contents($file);
                    $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                    // Визначаємо режим підсвітки залежно від розширення файлу
                    $mode = 'php';
                    if ($file_extension === 'css') $mode = 'css';
                    elseif ($file_extension === 'js') $mode = 'javascript';
                    elseif ($file_extension === 'html') $mode = 'htmlmixed';
                    ?>
                    <form method="post" id="editor-form">
                        <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file); ?>">
                        <textarea id="code-editor" name="file_content"><?php echo htmlspecialchars($content); ?></textarea>
                        <input type="submit" value="Save File">
                    </form>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
                            lineNumbers: true,
                            mode: '<?php echo $mode; ?>',
                            theme: 'default',
                            indentUnit: 4,
                            tabSize: 4,
                            lineWrapping: true
                        });
                        editor.setSize(null, 500);
                    });
                    </script>
                    <?php
                } else {
                    echo '<p style="color: #ffffff; text-align: center">Select a file to edit.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.folder').forEach(folder => {
            folder.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('collapsed');
            });
        });
    });
    </script>
</body>
</html>