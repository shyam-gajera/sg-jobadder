# Featured Job From JobAdder

**Contributors:** shyam-gajera
**Tags:** jobadder, jobs, featured jobs, recruitment, api
**Requires at least:** 5.6
**Tested up to:** 6.5
**Stable tag:** 1.0.0
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

A custom WordPress plugin that integrates with the JobAdder API to display job listings in the admin panel and allows you to mark selected jobs as "featured" using bulk actions.

---

## Features

- Fetch jobs directly from JobAdder API
- Display jobs using a custom admin table (`WP_List_Table`)
- Mark/unmark jobs as **Featured** with a bulk action
- Store featured job references in WordPress options (`featured_job_data`)
- Easily extendable for custom display or frontend use

---

## How It Works

1. The plugin fetches live job data using the JobAdder API via the function `get_jobadderads_data()`.
2. The job list is shown on a custom admin screen using a table interface.
3. Admins can check multiple jobs and use the **"Mark As Featured / Non-Featured"** bulk action.
4. Featured job IDs are stored in the WordPress options table as a serialized array (`featured_job_data`).
5. On each page load, the plugin cross-references the fetched jobs against this option to highlight featured ones.

---

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Set your JobAdder API token inside the `get_jobadderads_data()` function or via a settings panel (if implemented).
4. Navigate to the plugin’s admin screen to see and manage jobs.

---

## Configuration

Currently, you must manually set your JobAdder API token in the `get_jobadderads_data()` function located in the main plugin file or helper file:
$api_token = 'YOUR_API_TOKEN_HERE';