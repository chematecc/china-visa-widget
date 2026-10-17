# WordPress plugin

`china-visa-check/` is the plugin for the WordPress.org directory: the "China Visa Check" block, a classic widget and the shortcode `[china_visa_check passport="jp" lang="ja" theme="light"]`. It shows the same checker as the embed code, follows the site's language and adds a credit link only when the site owner turns it on (a directory rule).

- `tests/` and `.wp-env.json`: the workflow `.github/workflows/wordpress.yml` starts a real WordPress (wp-env), checks the shortcode, block and widget, the front end, and runs WordPress.org's Plugin Check. The zip to upload is the artifact of each run.
- `assets/`: icon, banners and screenshot for the directory page. They go in the plugin's SVN `assets/` folder after approval, not in the zip.

Submitting: log in at wordpress.org with the account that will own the plugin, set that username in `Contributors:` in `readme.txt`, and upload the zip at https://wordpress.org/plugins/developers/add/. After approval, commit the plugin to the SVN `trunk/`, tag `1.0.0`, and put `assets/` in SVN `assets/`.

The plugin is GPL-2.0-or-later (a directory requirement); the rest of this repository stays MIT.
