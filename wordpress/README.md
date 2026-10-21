# WordPress plugin

`china-visa-check/` is the plugin for the WordPress.org directory: the "China Visa Check" block, a classic widget and the shortcode `[china_visa_check passport="jp" lang="ja" theme="light"]`. It shows the same checker as the embed code, follows the site's language and adds a credit link only when the site owner turns it on (a directory rule).

- `tests/` and `.wp-env.json`: the workflow `.github/workflows/wordpress.yml` starts a real WordPress (wp-env), checks the shortcode, block and widget, the front end, and runs WordPress.org's Plugin Check. The zip to upload is the artifact of each run.
- `assets/`: icon, banners and screenshot for the directory page. They go in the plugin's SVN `assets/` folder after approval, not in the zip.

Approved in the WordPress.org Plugin Directory on 20 Oct 2026 as [china-visa-check](https://wordpress.org/plugins/china-visa-check/) (owner account `chinavisacheck`, SVN `https://plugins.svn.wordpress.org/china-visa-check`).

Publishing a version: raise `Version:` in `china-visa-check.php` and `Stable tag:` in `readme.txt` (with a changelog entry), push to `main`, wait for the "WordPress plugin" check, then push a tag `wp-vX.Y.Z`. The workflow `.github/workflows/wordpress-deploy.yml` commits the plugin to SVN `trunk/` and `tags/X.Y.Z/`, and `assets/` to SVN `assets/`. It needs the repository secret `SVN_PASSWORD` (WordPress.org > Profile > Account & Security > SVN credentials).

The plugin is GPL-2.0-or-later (a directory requirement); the rest of this repository stays MIT.
