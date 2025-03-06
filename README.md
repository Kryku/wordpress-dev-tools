# Dev Tools
## Description
A simple WordPress plugin that I created for myself to make my life as a developer easier. So far, it contains three handy tools - a database manager, server commands, and a file editor - all combined into one neat package. I created it for myself to work with databases, run server commands, and edit files without leaving the WordPress admin. Sometimes, you need to perform the necessary tasks only while working in the admin without access to the server/hosting. It's free, customizable, and won't overload your server. No need for bloated paid alternatives!

![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools.jpg)

## Features
* **Database Manager**: Peek at your DB tables and run custom SQL queries right from the admin.
* **Server Commands**: Send shell commands (like `ls` or `dir`) to your hosting server—if it lets you.
* **File Editor**: Browse your WP file tree, edit code with syntax highlighting, rename files, and collapse folders.
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
  ![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-db-manager.jpg)
- **Server Commands**:
  - Input field for shell commands (e.g., `whoami`, `ls -l`, `dir`).
  - Shows output or errors if your server plays nice.
  ![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-server-commands.jpg)
- **File Editor**:
  - File tree with collapsible folders (click to expand/collapse).
  - Code editor with syntax highlighting (thanks, [@CodeMirror](https://github.com/codemirror)!).
  - Save edits or rename files with a button.
  ![Dev Tools Main](https://raw.githubusercontent.com/Kryku/wordpress-dev-tools/refs/heads/main/screenshots/dev-tools-file-manager.jpg)

## Usage
1. Activate the plugin.
2. Go to **Dev Tools** in the admin menu.
3. Pick a tool from the catalog or submenu:
   - **Database Manager**: Check tables or run something like `SELECT * FROM wp_options`.
   - **Server Commands**: Try `whoami` or `ls` (works if your host allows shell access).
   - **File Editor**: Navigate the file tree, click a file to edit, tweak it, save, or rename.

## Example Scenarios
- **Database Manager**:  
```SELECT * FROM wp_users WHERE user_email = 'test@example.com'```
- **Server Commands**:  
```ls -l```

## Bottom Line
This is my personal Swiss knife for WordPress tinkering. It’s not polished for a big production rollout—think of it as a rough-and-ready dev companion. Database Manager is solid for SQL fiddling, Server Commands works if your host isn’t too strict, and File Editor’s file tree with collapsible folders is a lifesaver for quick edits.