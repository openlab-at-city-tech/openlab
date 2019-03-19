---
title: Genesis Privacy
menuTitle: Privacy
layout: layouts/base.njk
permalink: basics/privacy/index.html
minVersion: Genesis 2.7.0+ and WordPress 4.9.6+
tags: docs
---

Developers and site owners can use Genesis without negatively affecting user privacy. Genesis makes use of WordPress personal data export and deletion features to help site owners remove or provide personal data in response to a privacy request.

## Genesis user data
Genesis may store personally identifying information about users if they provide personal data in their user profiles. This data is stored in the WordPress database with other user profile information that users provide. It is not sent or stored additionally elsewhere.

Genesis refers to this user profile data as:
- Author Archive Settings
- Author Archive SEO Settings

These settings can be found on the user profile edit screen in the WordPress admin area.

<p class="notice">
If a user does not provide personally identifying data, Genesis does not store anything that is personally identifying about that user.
</p>

Genesis also offers the option to email someone when a new version of Genesis is available. This option allows an email address to be stored, which appears in the theme settings page under Genesis → Theme Settings and in the Customizer at Theme Settings → Updates. This email will be deleted if it matches the address of a user who has requested their data to be removed.

## WordPress privacy tools
WordPress offers privacy tools for site owners, accessible from the Tools menu in the WordPress admin area:
- **Export Personal Data**: allows admin users to export a user's personal data.
- **Erase Personal Data**: allows admin users to erase a user's personal data.

When someone uses these tools, Genesis informs WordPress of the data mentioned above so that WordPress can include it when processing the privacy request.

For example, here is the output of an Erase Personal Data action, showing that user-provided data relating to Genesis features was successfully erased upon request:

<img src="{{ '/img/data-removal.png' | url }}" alt="Screenshot of a data removal request showing that information submitted by a user and stored in fields specific to Genesis features has been successfully removed.">
