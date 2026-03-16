# OpenLab Scripts

This directory contains utility scripts for the OpenLab network.

## collect-visibility-data.php

A WP-CLI tool for collecting networkwide data about 'More visibility options' usage.

### Purpose

This script analyzes the usage of the `openlab_post_visibility` postmeta option across all sites in the network. It provides statistics on how groups are using visibility restrictions (group-members-only, members-only, default) on their posts and pages.

### Usage

Run the script using WP-CLI's `eval-file` command:

```bash
wp eval-file scripts/collect-visibility-data.php
```

### What It Does

The script:
1. Loops through all sites in the network with a progress bar
2. For each site:
   - Identifies the associated group and group type (course, project, club, portfolio)
   - Finds all published posts and pages with visibility options
   - Tracks the post author's member type (student, faculty, staff, alumni)
3. Generates a comprehensive report including:
   - Overall statistics (total sites, sites with visibility options, total restricted posts)
   - Breakdown by group type
   - Breakdown by member type
   - Breakdown by visibility option
   - Detailed breakdown combining group type and visibility option

### Requirements

- WP-CLI must be installed and configured
- Script must be run from the WordPress root directory
- Requires access to OpenLab custom functions:
  - `openlab_get_group_id_by_blog_id()`
  - `openlab_get_group_type()`
  - `openlab_get_user_member_type()`

### Output Example

```
Found 150 sites to analyze.
Processing sites 100% [============================] 0:02 / 0:02

============================================
VISIBILITY OPTIONS USAGE REPORT
============================================

--- OVERALL STATISTICS ---
Total sites checked: 150
Sites with visibility-restricted posts: 45
Total posts/pages with visibility options: 234

--- BY GROUP TYPE ---
Number of groups (by type) where the associated site has posts/pages with visibility options:
  Course: 25
  Project: 12
  Club: 5
  Portfolio: 3
  Unknown: 0

--- BY MEMBER TYPE ---
Number of posts/pages with visibility options by author member type:
  Student: 120
  Faculty: 89
  Staff: 15
  Alumni: 10
  Unknown: 0

--- BY VISIBILITY OPTION ---
Total posts/pages by visibility setting:
  group-members-only: 189
  members-only: 40
  default: 5

--- DETAILED BREAKDOWN: GROUP TYPE + VISIBILITY OPTION ---
Course:
  group-members-only: 145
  members-only: 23
  default: 2
Project:
  group-members-only: 30
  members-only: 12
  default: 3
...
============================================
Success: Data collection complete!
```

### Notes

- The script only analyzes published posts and pages
- Sites without associated groups are tracked as "unknown"
- Users without member types are tracked as "unknown"
- The script uses prepared statements for SQL queries to prevent SQL injection
- Progress is displayed in real-time for large networks
