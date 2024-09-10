<?php
if (!defined('ABSPATH')) {
    exit;
}
$allowedTags = [
    'a'       => [
        'href'   => [],
        'title'  => [],
        'target' => [],
    ],
    'abbr'    => [ 'title' => [] ],
    'acronym' => [ 'title' => [] ],
    'code'    => [],
    'pre'     => [],
    'em'      => [],
    'strong'  => [],
    'ul'      => [],
    'ol'      => [],
    'li'      => [],
    'p'       => [],
    'br'      => [],
    'img'     => [
        'src' => [],
        'alt' => []
    ]
];
$proURL = "https://go.premio.io/?edd_action=add_to_cart&download_id=687&edd_options[price_id]=";

$plans = [
    '1_website' => [
        '1_year' => [
            'price' => 49,
            'month' => 4.1,
            'link' => $proURL."37",
            'title' => esc_html__("1 year", "folders"),
            'mobile_title' => esc_html__("Updated and support for 1 year", "folders"),
        ],
        '2_years' => [
            'price' => 69,
            'month' => 2.9,
            'link' => $proURL."38",
            'title' => esc_html__("2 years", "folders"),
            'mobile_title' => esc_html__("Updated and support for 2 years", "folders"),
        ],
        'lifetime' => [
            'price' => 149,
            'month' => 0,
            'link' => $proURL."39",
            'title' => esc_html__("Lifetime", "folders"),
            'mobile_title' => esc_html__("Updated and support for lifetime", "folders"),
        ]
    ],
    '5_websites' => [
        '1_year' => [
            'price' => 109,
            'month' => 9.1,
            'link' => $proURL."40",
            'title' => esc_html__("1 year", "folders"),
            'mobile_title' => esc_html__("Updated and support for 1 year", "folders"),
        ],
        '2_years' => [
            'price' => 169,
            'month' => 7.1,
            'link' => $proURL."41",
            'title' => esc_html__("2 years", "folders"),
            'mobile_title' => esc_html__("Updated and support for 2 years", "folders"),
        ],
        'lifetime' => [
            'price' => 279,
            'month' => 0,
            'link' => $proURL."42",
            'title' => esc_html__("Lifetime", "folders"),
            'mobile_title' => esc_html__("Updated and support for lifetime", "folders")
        ]
    ],
    '50_websites' => [
        '1_year' => [
            'price' => 179,
            'month' => 15,
            'link' => $proURL."43",
            'title' => esc_html__("1 year", "folders"),
            'mobile_title' => esc_html__("Updated and support for 1 year", "folders"),
        ],
        '2_years' => [
            'price' => 279,
            'month' => 11.7,
            'link' => $proURL."44",
            'title' => esc_html__("2 years", "folders"),
            'mobile_title' => esc_html__("Updated and support for 2 years", "folders"),
        ],
        'lifetime' => [
            'price' => 479,
            'month' => 0,
            'link' => $proURL."45",
            'title' => esc_html__("Lifetime", "folders"),
            'mobile_title' => esc_html__("Updated and support for lifetime", "folders")
        ]
    ],
    '500_websites' => [
        '1_year' => [
            'price' => 349,
            'month' => 29.1,
            'link' => $proURL."46",
            'title' => esc_html__("1 year", "folders"),
            'mobile_title' => esc_html__("Updated and support for 1 year", "folders"),
        ],
        '2_years' => [
            'price' => 519,
            'month' => 21.7,
            'link' => $proURL."47",
            'title' => esc_html__("2 years", "folders"),
            'mobile_title' => esc_html__("Updated and support for 2 years", "folders"),
        ],
        'lifetime' => [
            'price' => 869,
            'month' => 0,
            'link' => $proURL."48",
            'title' => esc_html__("Lifetime", "folders"),
            'mobile_title' => esc_html__("Updated and support for lifetime", "folders")
        ]
    ],
    '1000_websites' => [
        '1_year' => [
            'price' => 479,
            'month' => 40,
            'link' => $proURL."49",
            'title' => esc_html__("1 year", "folders"),
            'mobile_title' => esc_html__("Updated and support for 1 year", "folders"),
        ],
        '2_years' => [
            'price' => 729,
            'month' => 30.5,
            'link' => $proURL."50",
            'title' => esc_html__("2 years", "folders"),
            'mobile_title' => esc_html__("Updated and support for 2 years", "folders"),
        ],
        'lifetime' => [
            'price' => 1199,
            'month' => 0,
            'link' => $proURL."51",
            'title' => esc_html__("Lifetime", "folders"),
            'mobile_title' => esc_html__("Updated and support for lifetime", "folders")
        ]
    ]
];

$features = [
    [
        'title' => esc_html__("Organize pages, media files, posts, plugins and custom post types", "folders"),
        'tooltip' => ""
    ],
    [
        'title' => esc_html__("Unlimited subfolders (with multilevel support)", "folders"),
        'tooltip' => esc_html__("Create unlimited folders, sub-folders with multi-level support", "folders")
    ],
    [
        'title' => esc_html__("Dynamic Folders", "folders"),
        'tooltip' => esc_html__("Automatically filter posts/pages/custom posts/media files based on author, date, file types & more ", "folders")."<img src='".WCP_FOLDER_URL."assets/images/dynamic-folders.gif' alt='".esc_html__("Dynamic Folders", "folders")."' />"
    ],
    [
        'title' => esc_html__("Create folders for various custom post types. I.e. WooCommerce, Elementor", "folders"),
        'tooltip' => esc_html__("Use folders with other plugins that have custom post type (e.g. Elementor templates, WooCommerce & more)", "folders")
    ],
    [
        'title' => esc_html__("User access restriction", "folders"),
        'tooltip' => esc_html__("Restrict users within their own folders only (restrict Dynamic Folder access as well)", "folders")
    ],
    [
        'title' => esc_html__("Folders access management", "folders"),
        'tooltip' => esc_html__("Advanced access management for your team with roles that restrict their access to view and edit your folders.", "folders")."<img src='".WCP_FOLDER_URL."assets/images/user-access.jpg' alt='".esc_html__("Metadata details on Media Library hover", "folders")."' />"
    ],
    [
        'title' => esc_html__("Email notifications for Folders changes", "folders"),
        'tooltip' => esc_html__("Stay informed with notifications whenever an action occurs on Folders — whether a new file is uploaded by a team member, a post is deleted, or any other change is made. Perfect for keeping track of updates.", "folders")
    ],
    [
        'title' => esc_html__("Clean unused media files from your Media Library", "folders"),
        'tooltip' => esc_html__("Media Cleaner will first analyze your whole WordPress and show you a list of unused media files for you to go through and delete them", "folders")
    ],
    [
        'title' => esc_html__("Directly upload folder or folder structures with their content", "folders"),
        'tooltip' => esc_html__("Upload a folder (all its content within the folder) directly", "folders")
    ],
    [
        'title' => esc_html__("Download folders as ZIP", "folders"),
        'tooltip' => esc_html__("Download all contents of a media folder as a ZIP", "folders")
    ],
    [
        'title' => esc_html__("Replace media for all file types and update file names from WordPress titles", "folders"),
        'tooltip' => esc_html__("Replace all types of media files including PDF/SVG/ZIP/DOCX/XSLX & more. While replacing compare the two files side by side", "folders")
    ],
    [
        'title' => esc_html__("Sort folders based on the date", "folders"),
        'tooltip' => esc_html__("Sort folders based on the creation date of the folders", "folders")
    ],
    [
        'title' => esc_html__("Advanced Media Replacements", "folders"),
        'tooltip' => esc_html__("Replace media files when uploading and update all references in the database, change dates, organize by newer date & more without hurting your SEO", "folders")
    ],
    [
        'title' => esc_html__("Metadata details on Media Library hover", "folders"),
        'tooltip' => esc_html__("Show useful metadata including title, size, type, date, dimension & more on hover.", "folders")."<img src='".WCP_FOLDER_URL."assets/images/folders-media.gif' alt='".esc_html__("Metadata details on Media Library hover", "folders")."' />"
    ],
    [
        'title' => esc_html__("Bookmark, lock & duplicate folders", "folders"),
        'tooltip' => esc_html__("Bookmark, lock/unlock or duplicate folders for faster & easier access to your favorite folders", "folders")
    ],
    [
        'title' => esc_html__("Export and import Folders structure", "folders"),
        'tooltip' => esc_html__("Easily export your folder structure in JSON format to back up your settings, or import structures from another WordPress account. Perfect for transferring setups, syncing across multiple sites, or sharing configurations with teammates", "folders")
    ],
    [
        'title' => esc_html__("Move files to trash before permanently removing", "folders"),
        'tooltip' => esc_html__("Before deleting files permanently, move them to trash to reduce accidental deletion", "folders")
    ],
    [
        'title' => esc_html__("Customize folder styles with fonts, custom colors & more", "folders"),
        'tooltip' => esc_html__("Customize folders with your preferred colors, font size, font family & more", "folders")
    ]
];
?>
<div class="pricing-top">
    <?php if(isset($hasBackButton)) { ?>
        <div class="back-button-box">
            <a href="<?php echo esc_url(admin_url("options-general.php?page=wcp_folders_settings")) ?>" class="go-back-button"><?php esc_html_e("Back", "folders"); ?></a>
        </div>
    <?php } ?>
    <div class="price-container">
        <div class="plan-details">
            <div class="unlock-features text-center">
                <div class="unlock-features-title"><?php esc_html_e("Unlock all features", "chaty"); ?></div>
                <div class="unlock-features-desc"><?php esc_html_e("(enjoy 30-day money back guarantee on all plans)", "chaty"); ?></div>
            </div>
            <div class="plan-details-bottom">
                <div class="plan-options">
                    <div class="plan-option active" data-id="1_year">
                        <div class="plan-title"><?php esc_html_e("1 year", "chaty"); ?></div>
                        <div class="plan-desc"><?php esc_html_e("Updates & Support", "chaty"); ?></div>
                    </div>
                    <div class="plan-option" data-id="2_years">
                        <div class="plan-title"><?php esc_html_e("2 years ", "chaty"); ?><span>(32% Off
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect y="0.712891" width="17" height="17" fill="url(#pattern0)"/>
                            <defs>
                            <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_1730_1878" transform="scale(0.0454545)"/>
                            </pattern>
                            <image id="image0_1730_1878" width="22" height="22" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAEsUlEQVRIiZ2VTWxc1RmGn++ce+7M+M54PElmnMTYIdRxUkBJKgQCRCsgLRWghgoptPwkC1Kx4WeBIF20i4mEECAhFrBqF4jQbhJFKt1VakUXkVqpi4QFEVBCsPmbePDYHs9M5s6993wsBps4BBZ80pGOjt7zfK++I70HrlBax1zp/Eq679IG64SKAIjgAbp/ueZGb+RmU7S7UIoAgjZ8PzvTm115W440G6sNpD688y2w1jGrwM6b2w+Rs08XKnaPjDkIZL2dfooUXbf7VuVk9/3F5+RI8/+Xw+XSjvP1arFyXfmvwRa3n5yln+AlEC8oYoZw9YoimFQD56D7aTejw6PFg+ePXQqXS6Ej15ZPRZNuT19NAmJxX4/GrXesiQ43iaYWcUE3odtMnyo9dO7V4wcIHzjBYO1GcmL6rWC6sB+RGENIHiE0YPn2KFKFRNG+IilK7EWXEkT6t8rPZ/8DID/bWbyzEtkn9+4Mf91J1Ac5MdYOQYURS7lsKI9ZynlDrEo/VprzKa2FlCxTrBV8SpZTtYuLyfnT55MTpVFekZ/ujF4s5MyRdk+TH09G7sJSn/mFBGshy74x2el4CnnBBoJzwq6pIp8t9GgtZTgneK8EgRAVDEmq15uTh0deyQc09s6M2JmpSDHK2KilFFnGRodrtGg4+sdx7t8/hrVw096ImalIA2sYG7VEBUMxMllUMBTz+trbZzvvmuq9teK2WlCbbw3M6XMtWW4PbXo/fKCLsWfbZMgDhzdx8OEKxgjtlvC/91uy3EnXab2HPVdpBcDwRad69WZn5j4f0FrKKOTMmtAYiGNlZkcOYs+GimVyi+O9uQ7tznrtas1s5aohGLjzhhGcEy7Gfk2Yemh+mVKrBjz4mwrESmljwMFHNpAkyko3Y5B+A11uezs54bhjd6gA9vHrw8L2naUnqj/KyexcwiBRQKiMBty1r0T9D5uZmM5BX8HDzO48e3flWVxQOr0M78Fa4brpyB99rCKjaee/z/8tPhnQ44teN/vkvttLU/tuinxjJTOBBckbLlxIAFiZT8nlDXglns8YH3c89GCZbVNVVloZgYWJivMuSU1zbuUMQFCrNzvtY9G/6aaHwtD66a2hUBRpLWb8rt5gqZ1xzVRIFA1DbHnZc/aDPvfdXeKFFyaoFS0SK/2LarLZJiz5f6xlRftPW2+NJkqnBhU3HJpD8mUjH3404LU/tzj9bo+VdoYNhA1jlrv2lXjs8EaKRoi7Xkk0zXvvFt758J+bHk9+oYqsZUX72NVvlHYUDvXVJDYwzlslVzIQCB+fi/m8mRGEsG0qZLzm0J5ncNEjGamkBL335hjMtX9Sq3NG6xhRRUTQ4wcIf/mr6r9Gd2y6LbZBCqCiVpyQGxFZzQvt69AlICqZSTXIZhu0P/vy0PgzvLlqdF1sHj9AeM+95TdGJjb/VsYciQoeUi4rYwBP4Po9Fj9oLGQL3Uerv+fv62JzLQovOWy+yP5wS/VZWyreEm00FhdCYCHNIBmQtfu0G0sL2o1fz+LKy7UjzcYVg34NrghHkVXB/EvVHWqaNwcu2C1OCgDpIPuEFf8OWfVUrd7sXG7qe0vrmNX/74fqvgLmDyyIpOFlEAAAAABJRU5ErkJggg=="/>
                            </defs>
                            </svg>
                            )</span>
                        </div>
                        <div class="plan-desc"><?php esc_html_e("Updates & Support", "chaty"); ?></div>
                    </div>
                    <div class="plan-option" data-id="lifetime">
                        <div class="plan-title"><?php esc_html_e("Lifetime ", "chaty"); ?><span>(60% Off
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect y="0.712891" width="17" height="17" fill="url(#pattern0)"/>
                            <defs>
                            <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_1730_1878" transform="scale(0.0454545)"/>
                            </pattern>
                            <image id="image0_1730_1878" width="22" height="22" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAEsUlEQVRIiZ2VTWxc1RmGn++ce+7M+M54PElmnMTYIdRxUkBJKgQCRCsgLRWghgoptPwkC1Kx4WeBIF20i4mEECAhFrBqF4jQbhJFKt1VakUXkVqpi4QFEVBCsPmbePDYHs9M5s6993wsBps4BBZ80pGOjt7zfK++I70HrlBax1zp/Eq679IG64SKAIjgAbp/ueZGb+RmU7S7UIoAgjZ8PzvTm115W440G6sNpD688y2w1jGrwM6b2w+Rs08XKnaPjDkIZL2dfooUXbf7VuVk9/3F5+RI8/+Xw+XSjvP1arFyXfmvwRa3n5yln+AlEC8oYoZw9YoimFQD56D7aTejw6PFg+ePXQqXS6Ej15ZPRZNuT19NAmJxX4/GrXesiQ43iaYWcUE3odtMnyo9dO7V4wcIHzjBYO1GcmL6rWC6sB+RGENIHiE0YPn2KFKFRNG+IilK7EWXEkT6t8rPZ/8DID/bWbyzEtkn9+4Mf91J1Ac5MdYOQYURS7lsKI9ZynlDrEo/VprzKa2FlCxTrBV8SpZTtYuLyfnT55MTpVFekZ/ujF4s5MyRdk+TH09G7sJSn/mFBGshy74x2el4CnnBBoJzwq6pIp8t9GgtZTgneK8EgRAVDEmq15uTh0deyQc09s6M2JmpSDHK2KilFFnGRodrtGg4+sdx7t8/hrVw096ImalIA2sYG7VEBUMxMllUMBTz+trbZzvvmuq9teK2WlCbbw3M6XMtWW4PbXo/fKCLsWfbZMgDhzdx8OEKxgjtlvC/91uy3EnXab2HPVdpBcDwRad69WZn5j4f0FrKKOTMmtAYiGNlZkcOYs+GimVyi+O9uQ7tznrtas1s5aohGLjzhhGcEy7Gfk2Yemh+mVKrBjz4mwrESmljwMFHNpAkyko3Y5B+A11uezs54bhjd6gA9vHrw8L2naUnqj/KyexcwiBRQKiMBty1r0T9D5uZmM5BX8HDzO48e3flWVxQOr0M78Fa4brpyB99rCKjaee/z/8tPhnQ44teN/vkvttLU/tuinxjJTOBBckbLlxIAFiZT8nlDXglns8YH3c89GCZbVNVVloZgYWJivMuSU1zbuUMQFCrNzvtY9G/6aaHwtD66a2hUBRpLWb8rt5gqZ1xzVRIFA1DbHnZc/aDPvfdXeKFFyaoFS0SK/2LarLZJiz5f6xlRftPW2+NJkqnBhU3HJpD8mUjH3404LU/tzj9bo+VdoYNhA1jlrv2lXjs8EaKRoi7Xkk0zXvvFt758J+bHk9+oYqsZUX72NVvlHYUDvXVJDYwzlslVzIQCB+fi/m8mRGEsG0qZLzm0J5ncNEjGamkBL335hjMtX9Sq3NG6xhRRUTQ4wcIf/mr6r9Gd2y6LbZBCqCiVpyQGxFZzQvt69AlICqZSTXIZhu0P/vy0PgzvLlqdF1sHj9AeM+95TdGJjb/VsYciQoeUi4rYwBP4Po9Fj9oLGQL3Uerv+fv62JzLQovOWy+yP5wS/VZWyreEm00FhdCYCHNIBmQtfu0G0sL2o1fz+LKy7UjzcYVg34NrghHkVXB/EvVHWqaNwcu2C1OCgDpIPuEFf8OWfVUrd7sXG7qe0vrmNX/74fqvgLmDyyIpOFlEAAAAABJRU5ErkJggg=="/>
                            </defs>
                            </svg>
                            )</span>
                        </div>
                        <div class="plan-desc"><?php esc_html_e("Updates & Support", "chaty"); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="pricing-bottom">
    <div class="price-container">
        <div class="pricing-table">
            <div class="desktop-table">
                <div class="pricing-header">
                    <div class="pricing-pointer"></div>
                    <div class="pricing-header-top">
                        <div class="plan-row">
                            <div class="plan-col first-col plan-data">
                                <span class="plan-plugin-name">Folders</span>
                                <span class="chaty-powered-by">
                                    Powered by
                                    <a href="https://premio.io" target="_blank">
                                        <svg width="132" height="37" viewBox="0 0 132 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.1257 7.10321C12.9732 7.10321 14.6467 7.57329 16.1463 8.51344C17.6458 9.42748 18.8239 10.7071 19.6808 12.3524C20.5644 13.9977 21.0062 15.8649 21.0062 17.9541C21.0062 20.0434 20.5778 21.9237 19.7209 23.5951C18.864 25.2403 17.686 26.533 16.1864 27.4732C14.7138 28.3873 13.0803 28.8442 11.2863 28.8442C10.001 28.8442 8.78271 28.5961 7.63132 28.0999C6.5067 27.5776 5.56952 26.9508 4.81978 26.2196V33.8976C4.81978 34.5766 4.59218 35.138 4.13698 35.582C3.68177 36.0521 3.10608 36.2871 2.40989 36.2871C1.7137 36.2871 1.13801 36.0652 0.682802 35.6212C0.2276 35.1772 0 34.6027 0 33.8976V9.8845C0 9.2055 0.2276 8.64402 0.682802 8.20006C1.13801 7.72998 1.7137 7.49495 2.40989 7.49495C3.10608 7.49495 3.68177 7.72998 4.13698 8.20006C4.59218 8.64402 4.81978 9.2055 4.81978 9.8845V10.0412C5.46242 9.25773 6.35944 8.57873 7.51082 8.00419C8.66221 7.40354 9.86716 7.10321 11.1257 7.10321ZM10.5232 24.5352C12.2368 24.5352 13.6426 23.9084 14.7405 22.6549C15.8384 21.4014 16.3873 19.8344 16.3873 17.9541C16.3873 16.0738 15.8384 14.52 14.7405 13.2926C13.6695 12.039 12.2637 11.4122 10.5232 11.4122C8.78271 11.4122 7.36356 12.039 6.26571 13.2926C5.16787 14.52 4.61896 16.0738 4.61896 17.9541C4.61896 19.8344 5.16787 21.4014 6.26571 22.6549C7.36356 23.9084 8.78271 24.5352 10.5232 24.5352ZM36.1903 7.10321C36.9936 7.10321 37.6631 7.32519 38.1986 7.76916C38.7609 8.21312 39.0421 8.73542 39.0421 9.33608C39.0421 10.1456 38.8279 10.7594 38.3994 11.1772C37.971 11.5689 37.4623 11.7648 36.8732 11.7648C36.4715 11.7648 36.0164 11.6734 35.5075 11.4906C35.4272 11.4645 35.2398 11.4122 34.9452 11.3339C34.6775 11.2556 34.3829 11.2164 34.0616 11.2164C33.3655 11.2164 32.696 11.4253 32.0534 11.8431C31.4107 12.261 30.8753 12.9008 30.4468 13.7626C30.0451 14.5984 29.8443 15.6038 29.8443 16.7789V26.063C29.8443 26.742 29.6167 27.3165 29.1615 27.7866C28.7064 28.2306 28.1306 28.4525 27.4344 28.4525C26.7383 28.4525 26.1626 28.2306 25.7073 27.7866C25.2522 27.3165 25.0245 26.742 25.0245 26.063V9.8845C25.0245 9.2055 25.2522 8.64402 25.7073 8.20006C26.1626 7.72998 26.7383 7.49495 27.4344 7.49495C28.1306 7.49495 28.7064 7.72998 29.1615 8.20006C29.6167 8.64402 29.8443 9.2055 29.8443 9.8845V10.3937C30.4602 9.32302 31.3438 8.51344 32.4952 7.96502C33.6467 7.39049 34.8783 7.10321 36.1903 7.10321ZM60.8168 17.4841C60.7901 18.1108 60.5356 18.6201 60.0537 19.0118C59.5717 19.4035 59.0094 19.5994 58.3667 19.5994H45.1123C45.4337 21.1402 46.17 22.3546 47.3214 23.2425C48.4728 24.1043 49.7715 24.5352 51.2174 24.5352C52.3153 24.5352 53.172 24.4438 53.7879 24.261C54.4038 24.052 54.8858 23.8432 55.2339 23.6342C55.6088 23.3992 55.8631 23.2425 55.997 23.1642C56.479 22.9291 56.9342 22.8116 57.3626 22.8116C57.9249 22.8116 58.4069 23.0075 58.8085 23.3992C59.2102 23.7909 59.411 24.2479 59.411 24.7702C59.411 25.4754 59.0361 26.1152 58.2864 26.6897C57.5367 27.2904 56.5326 27.7997 55.274 28.2175C54.0155 28.6353 52.7437 28.8442 51.4584 28.8442C49.2091 28.8442 47.2411 28.3873 45.5541 27.4732C43.894 26.5591 42.6087 25.3056 41.6983 23.7126C40.788 22.0934 40.3327 20.2784 40.3327 18.2675C40.3327 16.0216 40.8147 14.0499 41.7787 12.3524C42.7426 10.6549 44.0145 9.36219 45.5943 8.47427C47.1742 7.56023 48.8611 7.10321 50.6551 7.10321C52.4223 7.10321 54.0825 7.57329 55.6355 8.51344C57.2154 9.4536 58.4738 10.7202 59.411 12.3132C60.3482 13.9062 60.8168 15.6298 60.8168 17.4841ZM50.6551 11.4122C47.549 11.4122 45.7148 12.8356 45.1525 15.6821H55.6757V15.4079C55.5686 14.311 55.0196 13.3709 54.0289 12.5874C53.0382 11.804 51.9136 11.4122 50.6551 11.4122ZM89.6181 7.10321C92.269 7.10321 94.1032 7.89974 95.1207 9.49277C96.1382 11.0597 96.6469 13.2403 96.6469 16.0347V26.063C96.6469 26.742 96.4194 27.3165 95.9641 27.7866C95.509 28.2306 94.9333 28.4525 94.237 28.4525C93.5409 28.4525 92.9652 28.2306 92.5099 27.7866C92.0548 27.3165 91.8271 26.742 91.8271 26.063V16.0347C91.8271 14.5984 91.5327 13.4753 90.9435 12.6658C90.3812 11.8301 89.3771 11.4122 87.9312 11.4122C86.4317 11.4122 85.2535 11.8562 84.3966 12.7441C83.5666 13.6059 83.1515 14.7028 83.1515 16.0347V26.063C83.1515 26.742 82.924 27.3165 82.4687 27.7866C82.0136 28.2306 81.4379 28.4525 80.7416 28.4525C80.0455 28.4525 79.4698 28.2306 79.0145 27.7866C78.5594 27.3165 78.3317 26.742 78.3317 26.063V16.0347C78.3317 14.5984 78.0373 13.4753 77.4481 12.6658C76.8858 11.8301 75.8817 11.4122 74.4358 11.4122C72.9364 11.4122 71.7581 11.8562 70.9013 12.7441C70.0712 13.6059 69.6561 14.7028 69.6561 16.0347V26.063C69.6561 26.742 69.4286 27.3165 68.9734 27.7866C68.5182 28.2306 67.9425 28.4525 67.2463 28.4525C66.5501 28.4525 65.9744 28.2306 65.5192 27.7866C65.064 27.3165 64.8364 26.742 64.8364 26.063V9.8845C64.8364 9.2055 65.064 8.64402 65.5192 8.20006C65.9744 7.72998 66.5501 7.49495 67.2463 7.49495C67.9425 7.49495 68.5182 7.72998 68.9734 8.20006C69.4286 8.64402 69.6561 9.2055 69.6561 9.8845V10.5113C70.3791 9.57111 71.2896 8.77459 72.3874 8.12171C73.512 7.44271 74.7571 7.10321 76.1227 7.10321C79.4965 7.10321 81.6521 8.53955 82.5892 11.4122C83.2051 10.3154 84.1289 9.32302 85.3606 8.4351C86.6191 7.54718 88.0383 7.10321 89.6181 7.10321ZM106.402 26.063C106.402 26.742 106.174 27.3165 105.719 27.7866C105.264 28.2306 104.688 28.4525 103.992 28.4525C103.296 28.4525 102.72 28.2306 102.265 27.7866C101.81 27.3165 101.582 26.742 101.582 26.063V9.8845C101.582 9.2055 101.81 8.64402 102.265 8.20006C102.72 7.72998 103.296 7.49495 103.992 7.49495C104.688 7.49495 105.264 7.72998 105.719 8.20006C106.174 8.64402 106.402 9.2055 106.402 9.8845V26.063ZM103.952 4.9487C103.041 4.9487 102.399 4.80507 102.024 4.5178C101.649 4.23052 101.462 3.72127 101.462 2.99005V2.24576C101.462 1.51454 101.662 1.00529 102.064 0.718014C102.466 0.430743 103.108 0.287109 103.992 0.287109C104.929 0.287109 105.585 0.430743 105.96 0.718014C106.335 1.00529 106.522 1.51454 106.522 2.24576V2.99005C106.522 3.7474 106.322 4.2697 105.92 4.55697C105.545 4.81812 104.889 4.9487 103.952 4.9487ZM132 17.9933C132 20.0826 131.518 21.9629 130.554 23.6342C129.59 25.2795 128.265 26.5591 126.578 27.4732C124.917 28.3873 123.083 28.8442 121.075 28.8442C119.041 28.8442 117.193 28.3873 115.532 27.4732C113.872 26.5591 112.56 25.2795 111.596 23.6342C110.632 21.9629 110.15 20.0826 110.15 17.9933C110.15 15.904 110.632 14.0368 111.596 12.3916C112.56 10.7202 113.872 9.42748 115.532 8.51344C117.193 7.57329 119.041 7.10321 121.075 7.10321C123.083 7.10321 124.917 7.57329 126.578 8.51344C128.265 9.42748 129.59 10.7202 130.554 12.3916C131.518 14.0368 132 15.904 132 17.9933ZM127.18 17.9933C127.18 16.7137 126.899 15.5777 126.337 14.5853C125.801 13.5668 125.065 12.7833 124.128 12.2349C123.218 11.6865 122.2 11.4122 121.075 11.4122C119.951 11.4122 118.92 11.6865 117.982 12.2349C117.072 12.7833 116.336 13.5668 115.773 14.5853C115.238 15.5777 114.97 16.7137 114.97 17.9933C114.97 19.2729 115.238 20.4089 115.773 21.4014C116.336 22.3938 117.072 23.1642 117.982 23.7126C118.92 24.261 119.951 24.5352 121.075 24.5352C122.2 24.5352 123.218 24.261 124.128 23.7126C125.065 23.1642 125.801 22.3938 126.337 21.4014C126.899 20.4089 127.18 19.2729 127.18 17.9933Z" fill="#6641E5"/>
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            <div class="plan-col plan-detail plan-info" data-plan="1_website">
                                <span class="plan-name">Basic</span>
                                <span class="plan-price"><span>$<?php echo esc_attr($plans['1_website']['1_year']['price']) ?></span>/year</span>
                                <span class="plan-type">Billed Annually</span>
                                <span class="plan-monthly">Less than $<?php echo esc_attr($plans['1_website']['1_year']['month']) ?>/mo</span>
                                <span class="update-support">Updates & Support for</span>
                                <div class="plan-selector">
                                    <select class="multiple-options">
                                        <?php foreach($plans['1_website'] as $key=>$plan) { ?>
                                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                                <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['1_website']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                            </div>
                            <div class="plan-col plan-detail plus-plans plan-info" data-plan="5_websites">
                                <span class="plan-name">Plus</span>
                                <span class="plan-price"><span>$<?php echo esc_attr($plans['5_websites']['1_year']['price']) ?></span>/year</span>
                                <span class="plan-type">Billed Annually</span>
                                <span class="plan-monthly">Less than $<?php echo esc_attr($plans['5_websites']['1_year']['month']) ?>/mo</span>
                                <span class="update-support">Updates & Support for</span>
                                <div class="plan-selector">
                                    <select class="multiple-options">
                                        <?php foreach($plans['5_websites'] as $key=>$plan) { ?>
                                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                                <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['5_websites']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                            </div>
                            <div class="plan-col agency-plans plan-detail plan-info multiple-plan-info" data-plan="50_websites">
                                <span class="best-value">🎉 Best Value</span>
                                <span class="plan-name">Agency</span>
                                <span class="plan-price"><span>$<?php echo esc_attr($plans['50_websites']['1_year']['price']) ?></span>/year</span>
                                <span class="plan-type">Billed Annually</span>
                                <span class="plan-monthly">Less than $<?php echo esc_attr($plans['50_websites']['1_year']['month']) ?>/mo</span>
                                <span class="update-support">Updates & Support for</span>
                                <div class="plan-selector">
                                    <select class="multiple-options">
                                        <?php foreach($plans['50_websites'] as $key=>$plan) { ?>
                                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                                <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['50_websites']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                            </div>
                        </div>
                        <div class="plan-row">
                            <div class="plan-col first-row">Websites</div>
                            <div class="plan-col plan-detail">1</div>
                            <div class="plan-col plan-detail">5</div>
                            <div class="plan-col plan-detail">
                                <select class="domain-list">
                                    <option value="50">50 Websites</option>
                                    <option value="500">500 Websites</option>
                                    <option value="1000">1000 Websites</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pricing-body">
                    <?php
                    foreach ($features as $feature) { ?>
                        <div class="plan-row">
                            <div class="plan-col first-row">
                                <?php if(!empty($feature['tooltip'])) { ?>
                                    <a href="javascript:;" class="has-tooltip">
                                        <span class="table-text"><?php echo esc_attr($feature['title']); ?></span>
                                        <span class="table-tooltip">
                                        <span class="table-tooltip-text">
                                            <?php echo wp_kses($feature['tooltip'], $allowedTags); ?>
                                        </span>
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                    </span>
                                    </a>
                                <?php } else { ?>
                                    <div class="no-tooltip">
                                        <?php echo esc_attr($feature['title']); ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="plan-col plan-detail">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.3332 4.28711L5.99984 11.6204L2.6665 8.28711" stroke="#15C15D" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="plan-col plan-detail">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.3332 4.28711L5.99984 11.6204L2.6665 8.28711" stroke="#15C15D" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="plan-col plan-detail">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.3332 4.28711L5.99984 11.6204L2.6665 8.28711" stroke="#15C15D" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="mobile-table">
                <div class="mobile-plans">
                    <div class="mobile-plan" data-plan="">
                        <div class="mobile-pin"></div>
                        <div class="plan-head mobile-head-plan plan-info" data-plan="1_website">
                            <span class="plan-name">Basic</span>
                            <span class="plan-price"><span>$<?php echo esc_attr($plans['1_website']['1_year']['price']) ?></span>/year</span>
                            <span class="plan-type">Billed Annually</span>
                            <span class="plan-monthly">Less than $<?php echo esc_attr($plans['1_website']['1_year']['month']) ?>/mo</span>
                            <span class="update-support">Updates & Support for</span>
                            <div class="plan-selector">
                                <select class="multiple-options">
                                    <?php foreach($plans['1_website'] as $key=>$plan) { ?>
                                        <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                    <?php } ?>
                                </select>
                                <div class="clear"></div>
                            </div>
                            <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['1_website']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                        </div>
                        <div class="plan-body">
                            <ul>
                                <li>
                                    <a href="javascript:;" class="has-tooltip">
                                        <span class="table-text">1 website</span>
                                        <span class="table-tooltip">
                                            <span class="table-tooltip-text">
                                                Use Folders on 1 domain
                                            </span>
                                            <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                        </span>
                                    </a>
                                </li>
                                <?php
                                foreach ($features as $feature) { ?>
                                    <li>
                                        <?php if(!empty($feature['tooltip'])) { ?>
                                            <a href="javascript:;" class="has-tooltip">
                                                <span class="table-text"><?php echo esc_attr($feature['title']); ?></span>
                                                <span class="table-tooltip">
                                                    <span class="table-tooltip-text">
                                                        <?php echo wp_kses($feature['tooltip'], $allowedTags); ?>
                                                    </span>
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                                </span>
                                            </a>
                                        <?php } else {
                                            echo esc_attr($feature['title']);
                                        } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="mobile-plan" data-plan="">
                        <div class="mobile-pin"></div>
                        <div class="plan-head mobile-head-plan plus-plans plan-info" data-plan="5_websites">
                            <span class="best-value">🎉 Best Value</span>
                            <span class="plan-name">Plus</span>
                            <span class="plan-price"><span>$<?php echo esc_attr($plans['5_websites']['1_year']['price']) ?></span>/year</span>
                            <span class="plan-type">Billed Annually</span>
                            <span class="plan-monthly">Less than $<?php echo esc_attr($plans['5_websites']['1_year']['month']) ?>/mo</span>
                            <span class="update-support">Updates & Support for</span>
                            <div class="plan-selector">
                                <select class="multiple-options">
                                    <?php foreach($plans['5_websites'] as $key=>$plan) { ?>
                                        <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                    <?php } ?>
                                </select>
                                <div class="clear"></div>
                            </div>
                            <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['5_websites']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                        </div>
                        <div class="plan-body">
                            <ul>
                                <li>
                                    <a href="javascript:;" class="has-tooltip">
                                        <span class="table-text">1 website</span>
                                        <span class="table-tooltip">
                                            <span class="table-tooltip-text">
                                                Use Folders on 1 domain
                                            </span>
                                            <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                        </span>
                                    </a>
                                </li>
                                <?php
                                foreach ($features as $feature) { ?>
                                    <li>
                                        <?php if(!empty($feature['tooltip'])) { ?>
                                            <a href="javascript:;" class="has-tooltip">
                                                <span class="table-text"><?php echo esc_attr($feature['title']); ?></span>
                                                <span class="table-tooltip">
                                                    <span class="table-tooltip-text">
                                                        <?php echo wp_kses($feature['tooltip'], $allowedTags); ?>
                                                    </span>
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                                </span>
                                            </a>
                                        <?php } else {
                                            echo esc_attr($feature['title']);
                                        } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <div class="mobile-plan" data-plan="">
                        <div class="mobile-pin"></div>
                        <div class="plan-head mobile-head-plan plan-info multiple-plan-info" data-plan="50_websites">
                            <span class="plan-name">Agency</span>
                            <span class="plan-price"><span>$<?php echo esc_attr($plans['50_websites']['1_year']['price']) ?></span>/year</span>
                            <span class="plan-type">Billed Annually</span>
                            <span class="plan-monthly">Less than $<?php echo esc_attr($plans['50_websites']['1_year']['month']) ?>/mo</span>
                            <span class="update-support">Updates & Support for</span>
                            <div class="plan-selector">
                                <select class="multiple-options">
                                    <?php foreach($plans['50_websites'] as $key=>$plan) { ?>
                                        <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($plan['title']) ?></option>
                                    <?php } ?>
                                </select>
                                <div class="clear"></div>
                            </div>
                            <span class="plan-button"><a class="purchase-link" href="<?php echo esc_url($plans['50_websites']['1_year']['link']) ?>" target="_blank">Buy now</a></span>
                        </div>
                        <div class="plan-body">
                            <ul>
                                <li>
                                    <select class="domain-list">
                                        <option value="50" selected>50 Websites</option>
                                        <option value="500">500 Websites</option>
                                        <option value="1000">1000 Websites</option>
                                    </select>
                                    <div class="clear"></div>
                                </li>
                                <?php
                                foreach ($features as $feature) { ?>
                                    <li>
                                        <?php if(!empty($feature['tooltip'])) { ?>
                                            <a href="javascript:;" class="has-tooltip">
                                                <span class="table-text"><?php echo esc_attr($feature['title']); ?></span>
                                                <span class="table-tooltip">
                                                    <span class="table-tooltip-text">
                                                        <?php echo wp_kses($feature['tooltip'], $allowedTags); ?>
                                                    </span>
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <g opacity="0.5" clip-path="url(#clip0_1771_2646)"> <path d="M8.00016 14.9544C11.6821 14.9544 14.6668 11.9697 14.6668 8.28776C14.6668 4.60586 11.6821 1.62109 8.00016 1.62109C4.31826 1.62109 1.3335 4.60586 1.3335 8.28776C1.3335 11.9697 4.31826 14.9544 8.00016 14.9544Z" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 10.9538V8.28711" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8 5.62109H8.00667" stroke="#747C97" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_1771_2646"> <rect width="16" height="16" fill="white" transform="translate(0 0.287109)"/> </clipPath> </defs> </svg>
                                                </span>
                                            </a>
                                        <?php } else {
                                            echo esc_attr($feature['title']);
                                        } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="money-guaranteed">
            <p>
                <span class="dashicons dashicons-yes"></span>
                <?php esc_html_e("30 days money back guaranteed", 'chaty'); ?>
            </p>
            <p>
                <span class="dashicons dashicons-yes"></span>
                <?php esc_html_e("The plugin will always keep working even if you don't renew your license", 'chaty'); ?>
            </p>
            <div class="payments">
                <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/payment.png") ?>" alt="Payment" class="payment-img" />
            </div>
        </div>
    </div>
</div>
<div class="premio-client-section">
    <div class="price-container">
        <div class="client-testimonials">
            <div class="testimonial-list">
                <?php
                $reviews = [
                    [
                        'name'      => 'Chris Brady',
                        'position'  => 'Partner Innovation Experience - Microsoft',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/cris-brandy.webp',
                        'profile'   => 'https://wordpress.org/support/topic/excellent-product-265/',
                        'comment'   => "I really appreciate how easy it was to use Folders, and it's fulfillment of our exact needs. The ability to organize and categorize large amounts of images and videos was very helpful.
                    The overall experience with both the product and the Premio team has been fantastic."
                    ],
                    [
                        'name'      => 'Martin Stommer',
                        'position'  => 'Founder / CEO of Mark Beese',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/martin.webp',
                        'profile'   => 'https://wordpress.org/support/topic/great-customer-support-213/',
                        'comment'   => 'I had a unique challenge. Support was there quickly and helped me out right away. Working with these guys has been great, and the app is very useful.'
                    ],
                    [
                        'name'      => 'Ana Fenandez',
                        'position'  => 'Founder of The Solo Snowbird',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/ana.webp',
                        'profile'   => 'https://wordpress.org/support/topic/great-for-organizing-media/',
                        'comment'   => 'I have been using this plugin for over a year and I am very happy with the amount of media it can handle as well as the format in which it organizes the media. I highly recommend it and bought the 5 licence plan so I can use it on other client builds.'
                    ],
                    [
                        'name'      => 'Bakari Chavanu',
                        'position'  => 'Creator at MACAUTOMATIONTIPS',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/ian.webp',
                        'profile'   => 'https://wordpress.org/support/topic/very-useful-2897/',
                        'comment'   => 'Very useful. Helps me organize my content. At some point I will definitely update to the premium features.'
                    ],
                    [
                        'name'      => 'industryqorf',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/industryqorf.webp',
                        'profile'   => 'https://wordpress.org/support/plugin/folders/reviews/?filter=5',
                        'comment'   => 'While it is early days and I am currently still using the free version, my first impression is that this is an excellent plugin that will make my life much easier as I navigate the hundreds of pages and thousands of images on our site. Thank you'
                    ],
                    [
                        'name'      => 'jbalog',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/jbalog.webp',
                        'profile'   => 'https://wordpress.org/support/topic/outstanding-plugin-203/',
                        'comment'   => 'This plugin has made my ability to manage my wordpress site extremely easy. I would highly recommend this plugin to anyone looking to organize your pages and media!'
                    ],
                    [
                        'name'      => 'marinahleal',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/marinahleal.webp',
                        'profile'   => 'https://wordpress.org/support/topic/just-great-468/',
                        'comment'   => "So handy! Practical, streight to the point and light. It really makes my work easier. Thank you, Premio team, for creating such a great plugin!"
                    ],
                    [
                        'name'      => 'rahrah1',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/rahrah1.webp',
                        'profile'   => 'https://wordpress.org/support/topic/excellent-plugin-brilliant-for-organising/',
                        'comment'   => "We have a number of sites with huge libraries of images/pdfs/audio files, etc. Until now we've relied on the standard WP media library which is almost impossible to navigate. Folders has allowed us to  Everyone needs folders…	"
                    ],
                    [
                        'name'      => 'rmiller3000',
                        'image'     => WCP_FOLDER_URL . 'assets/images/testimonials/rmiller3000.webp',
                        'profile'   => 'https://wordpress.org/support/topic/perfect-for-lots-of-airplane-parts/',
                        'comment'   => "This plugin is perfect for our aircraft parts website. We can arrange folders by plane manufacturers and/or aircraft parts brands and item numbers. I highly recommend Folders for any website."
                    ],
                ];
                foreach($reviews as $review) { ?>
                    <div class="client-testimonial">
                        <div class="client-testimonial-slide">
                            <a href="<?php echo esc_url($review['profile']) ?>" target="_blank">
                                <div class="slide-quote">
                                    <svg width="51" height="44" viewBox="0 0 51 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.57143 44C0.857143 36.4163 0 29.8341 0 24.2537C0 16.2407 1.85714 10.2309 5.57143 6.22439C9.28571 2.0748 14.7143 0 21.8571 0V8.58537C18.1429 8.58537 15.4286 9.587 13.7143 11.5903C12.1429 13.5935 11.3571 16.5984 11.3571 20.6049V25.5415H22.0714V44H2.57143ZM31.5 44C29.7857 36.4163 28.9286 29.8341 28.9286 24.2537C28.9286 16.2407 30.7857 10.2309 34.5 6.22439C38.2143 2.0748 43.6429 0 50.7857 0V8.58537C47.0714 8.58537 44.3571 9.587 42.6429 11.5903C41.0714 13.5935 40.2857 16.5984 40.2857 20.6049V25.5415H51V44H31.5Z" fill="#EDEAF2"/>
                                    </svg>
                                </div>
                                <div class="client-text">
                                    <?php echo esc_attr($review['comment']) ?>
                                </div>
                                <div class="testimonial-bottom">
                                    <div class="client-image">
                                        <img src="<?php echo esc_url($review['image']) ?>" alt="Client image">
                                    </div>
                                    <div class="client-details">
                                        <div class="client-name"><?php echo esc_attr($review['name']) ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div id="slick-slider-options" class="slick-options">
                <button type="button" class="slick-btn slick-prev-btn ml-5 slick-arrow" style=""><svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="15.5" cy="15.5" r="15" stroke="#6641E5"/><path d="M18 11L13 16L18 21" stroke="#6641E5" stroke-linecap="round"/></svg></button>
                <div class="slider-dots product-slider-dots" id="slider-dots"></div>
                <button type="button" class="slick-btn slick-next-btn ml-5 slick-arrow" style=""><svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="15.5" cy="15.5" r="15" stroke="#6641E5"/><path d="M18 11L13 16L18 21" stroke="#6641E5" stroke-linecap="round"/></svg></button>
            </div>
        </div>
    </div>
</div>
<div class="premio-customers">
    <div class="price-container">
        <div class="customer-title"><?php esc_html_e("TRUSTED BY LEADING COMPANIES WORLDWIDE", "folders") ?></div>
        <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/companies.webp") ?>" alt="premio customers" />
    </div>
</div>
<div class="premio-faqs">
    <div class="price-container">
        <div class="faq-title"><?php esc_html_e("Frequently Asked Questions", "folders") ?></div>
        <div class="faq-lists">
            <?php
            $faqs = [
                [
                    'question' => "How long is my paid Folders plugin license valid for?",
                    'answer' => "Once you purchase any paid plan of Folders, you can use it forever. Support and updates are available for 1 year. You can renew your license each year to get another year of support and updates."
                ],
                [
                    'question' => "Can I use Folders plugin on more than 1 domain?",
                    'answer' => "There 2 ways to do it:
                        <ul>
                        <li>You can install the free Folders plugin on any website you want</li>
                        <li>You can buy the Pro plan that includes licenses for 5 domains, or the Agency plan that includes licenses for 50 domains (bigger plans are also available).</li>
                        </ul>"
                ],
                [
                    'question' => "Is there a time limit for the free plan?",
                    'answer' => "No. you can use the free plan as long as you want."
                ],
                [
                    'question' => "Will Folders stop working if I don’t renew my license?",
                    'answer' => "Of course NOT!<br/>Folders plugin and all your settings will continue to work as before; however, you will no longer receive plugin updates including feature additions, improvements, and support."
                ],
            ];
            foreach($faqs as $key => $faq) { ?>
                <div class="faq-list">
                    <div class="faq-question">
                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.5" width="20" height="20" rx="4" fill="#F2EEFF"/>
                            <path d="M9.80425 12.2145C9.72958 12.2145 9.66425 12.1865 9.60825 12.1305C9.55225 12.0745 9.52425 12.0092 9.52425 11.9345C9.52425 11.8692 9.52425 11.8039 9.52425 11.7385C9.52425 11.6732 9.52425 11.6079 9.52425 11.5425C9.57092 11.1972 9.67358 10.8892 9.83225 10.6185C10.0003 10.3385 10.1916 10.0819 10.4062 9.84855C10.6302 9.61521 10.8496 9.40055 11.0642 9.20455C11.2789 8.99921 11.4609 8.79388 11.6102 8.58855C11.7596 8.38321 11.8389 8.17788 11.8483 7.97255C11.8763 7.72988 11.8296 7.52921 11.7083 7.37055C11.5963 7.20255 11.4376 7.07655 11.2323 6.99255C11.0363 6.89921 10.8263 6.85255 10.6023 6.85255C10.1916 6.85255 9.86025 6.95521 9.60825 7.16055C9.36558 7.35655 9.19758 7.65988 9.10425 8.07055C9.06692 8.17321 9.01558 8.25255 8.95025 8.30855C8.88492 8.36455 8.79625 8.39255 8.68425 8.39255H7.22825C7.14425 8.39255 7.06958 8.35988 7.00425 8.29455C6.93892 8.22921 6.90625 8.14521 6.90625 8.04255C6.91558 7.66921 7.00425 7.30521 7.17225 6.95055C7.34025 6.59588 7.58758 6.27855 7.91425 5.99855C8.24092 5.70921 8.63758 5.48055 9.10425 5.31255C9.57092 5.14455 10.1123 5.06055 10.7283 5.06055C11.4002 5.06055 11.9556 5.14455 12.3943 5.31255C12.8423 5.48055 13.1969 5.69988 13.4583 5.97055C13.7289 6.23188 13.9202 6.51655 14.0323 6.82455C14.1442 7.12321 14.1909 7.40788 14.1723 7.67855C14.1629 8.02388 14.0883 8.33655 13.9482 8.61655C13.8176 8.88721 13.6543 9.13921 13.4583 9.37255C13.2716 9.60588 13.0709 9.83455 12.8563 10.0585C12.6509 10.2732 12.4643 10.4925 12.2963 10.7165C12.1376 10.9405 12.0209 11.1739 11.9463 11.4165C11.9276 11.4912 11.9089 11.5659 11.8903 11.6405C11.8809 11.7059 11.8716 11.7759 11.8623 11.8505C11.8156 11.9719 11.7643 12.0652 11.7083 12.1305C11.6523 12.1865 11.5682 12.2145 11.4562 12.2145H9.80425ZM9.80425 15.0005C9.70158 15.0005 9.61758 14.9679 9.55225 14.9025C9.48692 14.8372 9.45425 14.7532 9.45425 14.6505V13.2505C9.45425 13.1572 9.48692 13.0779 9.55225 13.0125C9.61758 12.9379 9.70158 12.9005 9.80425 12.9005H11.3723C11.4656 12.9005 11.5449 12.9379 11.6102 13.0125C11.6849 13.0779 11.7222 13.1572 11.7222 13.2505V14.6505C11.7222 14.7532 11.6849 14.8372 11.6102 14.9025C11.5449 14.9679 11.4656 15.0005 11.3723 15.0005H9.80425Z" fill="#6641E5"/>
                        </svg>
                        <?php echo esc_attr($faq['question']); ?>
                    </div>
                    <div class="faq-answer">
                        <?php echo wp_kses($faq['answer'], $allowedTags); ?>
                    </div>
                </div>
                <?php if($key%2 != 0) { ?>
                    <div class="clear-both"></div>
                <?php } ?>
            <?php } ?>
            <div class="clear-both"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function (factory) {
        "use strict";
        if (typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        }
        else if(typeof module !== 'undefined' && module.exports) {
            module.exports = factory(require('jquery'));
        }
        else {
            factory(jQuery);
        }
    }(function ($, undefined) {
        var pluginPlans = <?php echo wp_json_encode($plans) ?>;
        var selectedId = '1_year';
        $(document).ready(function(){
            $(".testimonial-list").slick({
                autoplay: true,
                arrows: true,
                dots: true,
                appendDots:"#slider-dots",
                nextArrow: '.slick-next-btn',
                prevArrow: '.slick-prev-btn',
            });
            $(document).on("click", ".plan-options .plan-option", function(){
                if(!$(this).hasClass("active")) {
                    $(".plan-options .plan-option").removeClass("active");
                    $(this).addClass("active");
                    selectedId = $(this).data("id");
                    if(selectedId != "1_year" && selectedId != "2_years" && selectedId != "lifetime") {
                        selectedId = "1_year";
                    }
                    $(".pricing-table .plan-info").each(function(){
                        var planName = $(this).data("plan");
                        if(typeof pluginPlans[planName] !== "undefined" && typeof pluginPlans[planName][selectedId] !== "undefined") {
                            $("select.multiple-options").val(selectedId).trigger("change");
                            $("select.multiple-options").niceSelect("update");
                        }
                    });
                }
            });

            if($(".multiple-options").length) {
                $(".multiple-options").niceSelect();
            }

            if($(".multiple-mobile-options").length) {
                $(".multiple-mobile-options").niceSelect();
            }

            if($(".domain-list").length) {
                $(".domain-list").niceSelect();
            }

            if($("select.multiple-options").length) {
                $(document).on("change", "select.multiple-options", function(){
                    selectedId = $(this).val();
                    var planName = $(this).closest(".plan-info").data("plan");
                    $(this).closest(".plan-info").removeClass("1_year").removeClass("2_years").removeClass("lifetime");
                    $(this).closest(".plan-info").addClass(selectedId);
                    if(typeof pluginPlans[planName] !== "undefined" && typeof pluginPlans[planName][selectedId] !== "undefined") {
                        var selectedPlan = pluginPlans[planName][selectedId];
                        var planMonthly = "Lifetime License 🚀";
                        var planType = "For lifetime";
                        var planPrice = "<span>$"+selectedPlan['price']+"</span>/lifetime";
                        if(selectedId == "2_years") {
                            planMonthly = "Less than $"+selectedPlan['month']+"/mo";
                            planType = "Billed Annually";
                            planPrice = "<span>$"+selectedPlan['price']+"</span>/2 years";
                        } else if(selectedId == "1_year") {
                            planMonthly = "Less than $"+selectedPlan['month']+"/mo";
                            planType = "Billed Annually";
                            planPrice = "<span>$"+selectedPlan['price']+"</span>/year"
                        }
                        $(this).closest(".plan-info").find(".plan-monthly").html(planMonthly);
                        $(this).closest(".plan-info").find(".plan-type").html(planType);
                        $(this).closest(".plan-info").find(".plan-price").html(planPrice);
                        $(this).closest(".plan-info").find(".purchase-link").attr("href", selectedPlan['link']);
                    }
                });
            }

            $(document).on("change", "select.domain-list", function(){
                $("select.domain-list").val($(this).val());
                $("div.domain-list .current").text($(this).val()+" Websites");
                $("div.domain-list li.selected").removeClass("selected");
                $("div.domain-list li[data-value='"+$(this).val()+"']").addClass("selected");
                $(".multiple-plan-info").data("plan", $(this).val()+"_websites");
                $(".multiple-plan-info select.multiple-options").each(function(){
                    $(this).trigger("change");
                })
            });

            tablePosition();
        });

        $(window).on("scroll", function(){
            tablePosition();
        });

        $(window).on("resize", function(){
            tablePosition();
        });

        function tablePosition() {
            if($(".pricing-pointer").length) {
                var offsetPos = $(".pricing-pointer").offset().top - $(window).scrollTop();

                if($(".pricing-pointer").offset().top - $(window).scrollTop() <= 40) {
                    $(".desktop-table").addClass("sticky-table-hide-col");
                } else {
                    $(".desktop-table").removeClass("sticky-table-hide-col");
                }

                if($(".pricing-pointer").offset().top - $(window).scrollTop() <= 32) {
                    $(".desktop-table").addClass("sticky-table");
                    $(".pricing-header-top").width($(".pricing-header").width() - 2);
                    $(".pricing-header-top").css("left", $(".pricing-header-top").offset().left);

                    var insideOffsetPos = $(".pricing-body").offset().top + $(".pricing-body").height() - $(window).scrollTop();
                    if(insideOffsetPos < 522) {
                        $(".pricing-header-top").css("margin-top", (insideOffsetPos - 522)+"px");
                    } else {
                        $(".pricing-header-top").css("margin-top", 0);
                    }
                } else {
                    $(".desktop-table").removeClass("sticky-table");
                    $(".pricing-header-top").attr("style", "");
                }
            }
            if($(".mobile-pin").length) {
                if($(window).width() <= 768) {
                    $(".mobile-pin").each(function () {
                        var scrollPos = $(this).offset().top - $(window).scrollTop();
                        console.log(scrollPos);
                        if (scrollPos < 0) {
                            $(this).closest(".mobile-plan").addClass("has-sticky-info");

                            scrollPos = $(this).offset().top + $(this).closest(".mobile-plan").height() - $(window).scrollTop();
                            if (parseInt(scrollPos) < 500) {
                                $(this).closest(".mobile-plan").find(".mobile-head-plan").css("margin-top", (parseInt(scrollPos) - 500) + "px");
                            } else {
                                $(this).closest(".mobile-plan").find(".mobile-head-plan").css("margin-top", "0px");
                            }
                        } else {
                            $(this).closest(".mobile-plan").removeClass("has-sticky-info");
                        }
                    });
                } else {
                    $(".mobile-head-plan").css("margin-top", "0px");
                    $(".mobile-plan").removeClass("has-sticky-info");
                }
            }
        }
    }));

    /* NICE SELECT LIBRARY*/
    !function(e){e.fn.niceSelect=function(t){function s(t){t.after(e("<div></div>").addClass("nice-select").addClass(t.attr("class")||"").addClass(t.attr("disabled")?"disabled":"").attr("tabindex",t.attr("disabled")?null:"0").html('<span class="current"></span><ul class="list"></ul>'));var s=t.next(),n=t.find("option"),i=t.find("option:selected");s.find(".current").html(i.data("display")||i.text()),n.each(function(t){var n=e(this),i=n.data("display");s.find("ul").append(e("<li></li>").attr("data-value",n.val()).attr("data-display",i||null).addClass("option"+(n.is(":selected")?" selected":"")+(n.is(":disabled")?" disabled":"")).html(n.text()))})}if("string"==typeof t)return"update"==t?this.each(function(){var t=e(this),n=e(this).next(".nice-select"),i=n.hasClass("open");n.length&&(n.remove(),s(t),i&&t.next().trigger("click"))}):"destroy"==t?(this.each(function(){var t=e(this),s=e(this).next(".nice-select");s.length&&(s.remove(),t.css("display",""))}),0==e(".nice-select").length&&e(document).off(".nice_select")):console.log('Method "'+t+'" does not exist.'),this;this.hide(),this.each(function(){var t=e(this);t.next().hasClass("nice-select")||s(t)}),e(document).off(".nice_select"),e(document).on("click.nice_select",".nice-select",function(t){var s=e(this);e(".nice-select").not(s).removeClass("open"),s.toggleClass("open"),s.hasClass("open")?(s.find(".option"),s.find(".focus").removeClass("focus"),s.find(".selected").addClass("focus")):s.focus()}),e(document).on("click.nice_select",function(t){0===e(t.target).closest(".nice-select").length&&e(".nice-select").removeClass("open").find(".option")}),e(document).on("click.nice_select",".nice-select .option:not(.disabled)",function(t){var s=e(this),n=s.closest(".nice-select");n.find(".selected").removeClass("selected"),s.addClass("selected");var i=s.data("display")||s.text();n.find(".current").text(i),n.prev("select").val(s.data("value")).trigger("change")}),e(document).on("keydown.nice_select",".nice-select",function(t){var s=e(this),n=e(s.find(".focus")||s.find(".list .option.selected"));if(32==t.keyCode||13==t.keyCode)return s.hasClass("open")?n.trigger("click"):s.trigger("click"),!1;if(40==t.keyCode){if(s.hasClass("open")){var i=n.nextAll(".option:not(.disabled)").first();i.length>0&&(s.find(".focus").removeClass("focus"),i.addClass("focus"))}else s.trigger("click");return!1}if(38==t.keyCode){if(s.hasClass("open")){var l=n.prevAll(".option:not(.disabled)").first();l.length>0&&(s.find(".focus").removeClass("focus"),l.addClass("focus"))}else s.trigger("click");return!1}if(27==t.keyCode)s.hasClass("open")&&s.trigger("click");else if(9==t.keyCode&&s.hasClass("open"))return!1});var n=document.createElement("a").style;return n.cssText="pointer-events:auto","auto"!==n.pointerEvents&&e("html").addClass("no-csspointerevents"),this}}(jQuery);
</script>
