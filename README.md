# Dev Tools
## Description
A ~~simple~~ WordPress plugin that I created for myself to make my life as a developer easier. So far, it contains five handy tools - a database manager, server commands, a file editor, and a code snippet runner - all combined into one neat package. I built it to work with databases, run server commands, edit files, and test code without leaving the WordPress admin. Sometimes, you need to perform tasks only in the admin without server/hosting access. It’s free, customizable, and won’t overload your server. No need for bloated paid alternatives!


![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools.jpg)

## Features
* **Database Manager**: Peek at your DB tables and run custom SQL queries right from the admin.
* **Server Commands**: Send shell commands (like `ls` or `dir`) to your hosting server—if it lets you.
* **File Editor**: Browse your WP file tree, edit code with syntax highlighting, rename files, and collapse folders.
* **Code Snippet Runner**: Test PHP snippets, functions, and shortcodes without touching your site’s files.
* **Save Our Souls (SOS)**: Emergency file editor for when everything’s on fire and you’ve got no server access.
* Enable/disable tools as you need them.
* Clean, simple UI with zero fluff.
* Lightweight and tweak-friendly.

## Installation
1. Grab the plugin file (`dev-tools.zip`).
2. Head to **Plugins > Add New** in your WordPress admin.
3. Upload it, hit activate, and you’re good to go.

## Settings
Find **Dev Tools** in the WordPress admin menu. It’s your hub.

### What’s Inside:
- **Database Manager**:
  - Lists all DB tables.
  - Lets you run custom SQL queries and see results on the spot.
  - ![Database Manager](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-db-manager.jpg)
- **Server Commands**:
  - Input field for shell commands (e.g., `whoami`, `ls -l`, `dir`).
  - Shows output or errors if your server plays nice.
  - ![Server Commands](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-server-commands.jpg)
- **File Editor**:
  - File tree with collapsible folders (click to expand/collapse).
  - Code editor with syntax highlighting (thanks, [@CodeMirror](https://github.com/codemirror)!).
  - Save edits or rename files with a button.
  - ![File Editor](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-file-manager.jpg)
- **Code Snippet Runner**:
  - Run one-off PHP snippets, test functions, or preview shortcodes.
  - Syntax highlighting via CodeMirror.
  - Output shows below—no risk to your live site.
  - ![Code Snippet Runner](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-code-snippet-runner.jpg)
- **Save Our Souls (SOS)**:
  - Emergency file editor accessible via a direct URL (e.g., `/wp-content/plugins/dev-tools/includes/save-our-soul/`).
  - Enable it in settings, then use it when the site’s down and your client won’t give you FTP or hosting access.
  - Same file tree and CodeMirror goodness, but works outside the admin if you’ve borked something bad—like a typo in `functions.php`.
  - If you’re *rly* crazy, you can even tweak `wp-config.php`… but, uh, one shot at that before you’re in deeper trouble!
  - ![SOS Editor](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-sos-1.jpg)
  - ![SOS Editor](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-sos-2.jpg)

## Usage
1. Activate the plugin.
2. Go to **Dev Tools** in the admin menu.
3. Pick a tool from the catalog or submenu:
   - **Database Manager**: Check tables or run something like `SELECT * FROM wp_options`.
   - **Server Commands**: Try `whoami` or `ls` (works if your host allows shell access).
   - **File Editor**: Navigate the file tree, click a file to edit, tweak it, save, or rename.
   - **Code Snippet Runner**: Test a function or shortcode like `[my_shortcode]`.
   - **SOS**: Enable it in settings first, then hit the direct URL when disaster strikes—no admin login needed, just basic auth creds (`admin`/`ohf*k` by default, change ‘em!).

## Example Scenarios
- **Database Manager**:  
```
SELECT * FROM wp_users WHERE user_email = 'test@example.com'
```
- **Server Commands**:  
```
ls -l
```
- **Code Snippet Runner**:  
```
<?php
add_shortcode('test_shortcode', function() {
    return "Hello from shortcode!";
});
echo do_shortcode('[test_shortcode]');
```

## Bottom Line
Bottom Line This is my personal Swiss knife for WordPress tinkering. Database Manager is solid for SQL, Server Commands works if your host isn't strict, File Editor's tree is a lifesaver, and Code Snippet Runner lets you test code without breaking stuff.