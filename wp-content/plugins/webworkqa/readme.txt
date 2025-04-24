=== WeBWorK Q&A ===
Contributors: boonebgorges
Tags: webwork, math, homework, q&a
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 7.0

WeBWorK Q&A creates a community forum where users can ask and answer questions about WeBWorK problems.

== Description ==

[WeBWorK](https://webwork.maa.org) is a free and open source online STEM assessment platform sponsored by the Mathematical Association of America (MAA) and used by more than a thousand colleges, universities, and high schools across the U.S. and around the world. The WeBWorK Open Problem Library provides access to more than 50,000 assessment/homework problems created and freely shared by the members of the WeBWorK community for others to curate, remix, and use with their students.

WeBWorK provides an option for students to email their professor if they have a question about a WeBWorK problem. However, this one-on-one communication model leaves students isolated from their peers and can result in the instructor answering the same question dozens of times. WeBWorK Q&A helps students learn from one another by sharing their questions in a community space. Instead of working on assignments alone or interacting only with their instructor, students using WeBWorK Q&A can become part of a community of learners, working together on the same material and offering mutual support. Questions can be answered by faculty, other students, tutors, and so on, enabling faster response times.

Features:

* Ask questions anonymously
* Use LaTeX in questions and responses
* Include images in questions and responses
* Browse and filter questions
* Like helpful answers
* Subscribe to or unsubscribe from email notifications about a question
* Accessible, responsive recommended theme

Check out the [documentation site](https://openlab.citytech.cuny.edu/webwork-qa-plugin/) for more details.

== Credits ==

WeBWorK Q&A was created by a team based at [City Tech](http://www.citytech.cuny.edu/) (New York City College of Technology, City University of New York) as part of [Opening Gateways](https://openlab.citytech.cuny.edu/openinggateways/), a City Tech initiative funded by the U.S. Department of Education’s Developing Hispanic-Serving Institutions program* to support student success in mathematics courses that serve as gateways to STEM disciplines.

WeBWorK Q&A was developed in partnership with [The OpenLab at City Tech](https://openlab.citytech.cuny.edu/), an open platform for teaching, learning, and collaboration; anyone can create an OpenLab using the free and open source software [Commons In A Box OpenLab](https://wordpress.org/plugins/commons-in-a-box/).

_*Disclaimer: materials developed do not necessarily represent the policy of the Department of Education, and you should not assume endorsement by the Federal Government._

== Installation ==

1. Install WeBWorK. (See the [WeBWorK installation manual](https://webwork.maa.org/wiki/Category:Installation_Manuals) for more information.)
2. Install and activate the WeBWorK Q&A plugin on your WordPress site.
3. (Optional but recommended) Install and activate the WeBWorK Theme, available at [https://github.com/openlab-at-city-tech/webwork-theme](https://github.com/openlab-at-city-tech/webwork-theme). Note that webwork-theme is a child theme of [Hemingway](https://wordpress.org/themes/hemingway/), which must also be installed for webwork-theme to work.
4. Configure WeBWorK for connection with WeBWork Q&A site, as per [our documentation](https://openlab.citytech.cuny.edu/webwork-qa-plugin/installation/#webwork_configuration).
5. Create a page on your WordPress site where you want the Q&A functionality to appear, and insert the `[webwork]` shortcode into the page. For example, on the [demo site](https://openlab.citytech.cuny.edu/examplewebworkqasite/) there is a page called ‘WeBWorK Q&A’ where the `[webwork]` shortcode has been added.
6. If you are using the WeBWorK Q&A theme or another theme with a ‘Full Width Template’, it’s recommended that you use it for the `[webwork]` page. WeBWorK Q&A is not currently compatible with sidebars.
7. At Dashboard > Settings > Reading, change ‘Your homepage displays’ to ‘A static page’, and select the newly-created `[webwork]` page from the corresponding dropdown.

== Frequently Asked Questions ==

= Where do I get support? =

Please use the support forum to get help with the WeBWorK Q&A plugin and offer feedback or suggestions.

= Can I use WeBWorK Q&A with any WordPress theme? =

Yes, but the Q&A site will work better with the style and layout of certain themes than with others.

We recommend using the WeBWork Theme, a theme developed by the WeBWorK Q&A team. Download the theme at [https://github.com/openlab-at-city-tech/webwork-theme](https://github.com/openlab-at-city-tech/webwork-theme).

= Where can I find more information? =

Visit our [plugin site](https://openlab.citytech.cuny.edu/webwork-qa-plugin/) for documentation and more information about the project. Visit [our example site](https://openlab.citytech.cuny.edu/examplewebworkqasite/) to see what the plugin looks like in action.

== Screenshots ==

1. Homepage of a site running the WeBWorK Q&A plugin and `webwork-theme`.
2. 'Ask a Question' interface.
3. Browsing responses to a question.

== Changelog ==

= 1.0.0 =

* Initial release.
