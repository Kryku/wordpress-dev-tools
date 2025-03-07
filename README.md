# Dev Tools
## Description
A ~~simple~~ WordPress plugin I whipped up to make my developer life less of a headache. It’s packed with handy tools—Database Manager, Server Commands, File Editor, Code Snippet Runner, Cron Scheduler, Performance Profiler, and an emergency SOS mode—all in one lightweight package. Built for database tweaks, server poking, file editing, code testing, cron wrangling, and performance checks, right from the WordPress admin. Perfect when you’re stuck without server or hosting access. Free, customizable, and won’t bog down your site. Skip the bloated paid stuff!


![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools.jpg)

## Features
* **Database Manager**: Peek at your DB tables and run custom SQL queries right from the admin.
* **Server Commands**: Send shell commands (like `ls` or `dir`) to your hosting server—if it lets you.
* **File Editor**: Browse your WP file tree, edit code with syntax highlighting, rename files, and collapse folders.
* **Code Snippet Runner**: Test PHP snippets, functions, and shortcodes without touching your site’s files.
* **Cron Scheduler**: View, run, or tweak WordPress cron jobs. Fix stuck schedules or test hooks on the fly.
* **Performance Profiler**: Measure page load time, memory usage, and SQL queries. Pinpoint performance bottlenecks with ease.
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
- **Cron Scheduler**:
  - Lists all scheduled cron jobs with next run time and recurrence.
  - Run jobs manually, delete them, or add new ones with a simple form.
  - Logs every execution (manual or auto) so you know what’s happening.
  - ![Cron Scheduler](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-cron-scheduler.jpg)
- **Performance Profiler**:
  - Shows page load time, query count, and memory usage for the current page.
  - Lists top 10 slowest SQL queries (if `SAVEQUERIES` is on).
  - ![Performance Profiler](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-performance-profiler.jpg)
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
   - **Cron Scheduler**: Check cron jobs, hit "Run Now," or schedule a new one like `my_custom_hook`.
   - **Performance Profiler**: See how fast (or slow) your page loads, spot heavy queries.
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
This is my personal Swiss knife for WordPress tinkering. Database Manager is solid for SQL, Server Commands works if your host isn’t strict, File Editor’s tree is a lifesaver, Code Snippet Runner lets you test code without breaking stuff, Cron Scheduler keeps your tasks in check, and Performance Profiler helps you figure out why your site’s dragging. SOS is there when it all hits the fan. No fluff, just tools that get the job done.