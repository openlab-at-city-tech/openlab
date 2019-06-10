## BuddyPress Docs In Group

Once upon a time, BuddyPress Docs was limited to groups: all Docs were necessarily linked to groups, and all Docs editing, creation, and viewing happened within the groups interface. In BuddyPress Docs 1.2, group affiliation was made optional, and the interface for Docs was mostly moved out of the group context.

A few of my clients have refrained from updating BuddyPress Docs for several years because of this change. This is obviously very bad. So I have written this plugin, which puts group-related Docs back into the groups interface. Doc creation, viewing, and editing of group-affiliated Docs now takes place under a group URL and within the groups UI wrapper (group group group group group).

This plugin is somewhat rough and experimental. I may opt to make this the default behavior for Docs in the future, at which point it will be cleaned up. Until then, use at your own risk.

Requires BuddyPress Docs 1.8.3. If that's not available yet where you live, use https://github.com/boonebgorges/buddypress-docs/tree/1.8.x with a changeset of at least 6dcc4b51.
