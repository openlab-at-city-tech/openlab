=== Easy Table ===
Contributors: takien
Donate link: http://takien.com/donate
Tags: table,csv,csv-to-table,post,excel,csv file,widget,tablesorter
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy Table is WordPress plugin to create table in post, page, or widget in easy way using CSV format. This can also display table from CSV file.

== Description ==

Easy Table is a WordPress plugin that allow you to insert table in easy way. Why it's easy? Because you don't need to write any complicated HTML syntax. Note that this plugin is not a graphical user interface table generator, so you can simply type your table data directly in your post while you writing. No need to switch to another window nor click any toolbar button.

Easy Table using standard CSV format to generate table data, it's easiest way to build a table. 

= Some Features =
* Easy to use, no advanced skill required
* Display table in post, page or even in widget
* Read data from CSV file and display the data in table
* Sortable table column (using tablesorter jQuery plugin)
* Fancy table design (using Twitter CSS bootstrap)
* WYSIWYG safe, I mean you can switch HTML/View tab in WordPress editor without breaking the table data.

= Known bugs and limitation =
* Enclosure will not work on first cell of a row
* Chinese characters (and others?) usually stripped down on first cell of a row
* Unable to create nested table

= Example usage =

* Basic table
`[table]
Year,Make,Model,Length
1997,Ford,E350,2.34
2000,Mercury,Cougar,2.38
[/table]`

* Table with additional parameter
`[table tablesorter="1" id="someid"]
Year,Make,Model,Length
1997,Ford,E350,2.34
2000,Mercury,Cougar,2.38
[/table]`

* Table with specific width
`[table width="500px"]
Year,Make,Model,Length
1997,Ford,E350,2.34
2000,Mercury,Cougar,2.38
[/table]`

Valid width value : auto, any number followed by % or px.
If width not set, it will use default width value ( can be changed via Plugin option )

* Table with colspan and other attribute in some cells
`[table]
no[attr style="width:20px"],head1,head2,head3
1,row1col1,row1col2,row1col3[attr class="someclass"]
2,row2col1,row2col2,row2col3
3,row3col1[attr colspan="2"],row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Table with initial sort order using table parameter, sort by first column descending
`[table sort="desc"]
no,head1,head2,head3
1,row1col1,row1col2,row1col3
2,row2col1,row2col2,row2col3
3,row3col1,row3col2,row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Table with initial sort order using table parameter, sort by first column descending, and second column ascending
`[table sort="desc,asc"]
no,head1,head2,head3
1,row1col1,row1col2,row1col3
2,row2col1,row2col2,row2col3
3,row3col1,row3col2,row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Table with initial sort order using cell attr, sort by second column descending
`[table]
no,head1[attr sort="desc"],head2,head3
1,row1col1,row1col2,row1col3
2,row2col1,row2col2,row2col3
3,row3col1,row3col2,row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Disable sort for third column using cell attr
`[table]
no,head1,head2[attr sort="false"],head3
1,row1col1,row1col2,row1col3
2,row2col1,row2col2,row2col3
3,row3col1,row3col2,row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Disable sort for third column using table parameter
`[table sort=",,false"]
no,head1,head2,head3
1,row1col1,row1col2,row1col3
2,row2col1,row2col2,row2col3
3,row3col1,row3col2,row3col3
4,row4col1,row4col2,row4col3
[/table]`

* Table with auto index, start from number 1 (since 0.9)
`[table ai="1"]
head1,head2,head3
row1col1,row1col2,row1col3
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with auto index, start from number 2 (since 0.9)
`[table ai="2"]
head1,head2,head3
row1col1,row1col2,row1col3
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with auto index, start from number 1, and titled No. (since 0.9)
`[table ai="1/No."]
head1,head2,head3
row1col1,row1col2,row1col3
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with auto index, start from number 1, titled No., and column width 50px (since 0.9)
`[table ai="1/No./50"]
head1,head2,head3
row1col1,row1col2,row1col3
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with new line in a cell (since 0.9)
any nl value would be replaced with new line while rendered.
nl could be one character or more. Be wise to use character here, make sure it's not very common character that may used in your data.

`[table nl="~~"]
head1,head2,head3
row1col1,row1col2,this~~should~~be~~in~~one cell
row2col1,row2col2,this~~
also~~
should~~
be~~
in~~
one~~
cell
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with custom row terminator (since 1.0)
Now you can use another character as new row, not only linebreak (\n or \n), eg. you want to use | as row terminator.

`[table terminator="|"]
head1,head2,head3|
row1col1,row1col2,this
should
be
in
one cell|
row2col1,row2col2,this
also
should
be
in
one
cell|
row3col1,row3col2,row3col3|
row4col1,row4col2,row4col3|
[/table]`

* Table with comma in cell using enclosure
`[table]
head1,head2,head3
row1col1,row1col2,"this, should, in, one cell, because, enclosured, with, doublequote"
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with comma in cell using escape (since 1.3)
`[table]
head1,head2,head3
row1col1,row1col2,this\, should\, in\, one cell\, because\, commas \,escaped \,with \,backslash
row2col1,row2col2,row2col3
row3col1,row3col2,row3col3
row4col1,row4col2,row4col3
[/table]`

* Table with no heading
`[table th="0"]some data here[/table]`

* Table with no heading
`[table th="0"]some data here[/table]`

* Table with footer/tfoot, by default tfoot automatically picked up from second row.
`[table tf="1"]some data here[/table]`

* Table with picked up from last row.
`[table tf="last"]some data here[/table]`

* Table from CSV file
`[table file="example.com/blog/wp-content/uploads/pricelist.csv"][/table]`

[Look confusing? Please click here](http://takien.com/plugins/easy-table).
Or check out our video tutorial here http://www.youtube.com/watch?v=Th0_qSleyDI

= Other notes =
* If read from file, the file URL must not contain space.

== Installation ==

There are many ways to install this plugin, e.g:

1. Upload compressed (zip) plugin using WordPress plugin uploader.
2. Directly install from WordPress.org directory
3. Upload manually uncompressed plugin file using FTP.

== Frequently Asked Questions ==

[See official plugin support here](http://takien.com/plugins/easy-table).

== Screenshots ==

1. Various table in a post
2. Easy Table options page
3. It's easy to display your uploaded CSV file as HTML table.
4. Easy Table in text widget

== Upgrade Notice ==

No

== Changelog ==

= 1.5.2 = 
* Fixed: Bug on 1.5/1.5.1, Easy Table does not work in WordPress prior to version 3.6

= 1.5.1 = 
* Fixed: Bug on 1.5, Easy Table does not work if TablePress is active even when custom shortcode is set.

= 1.5 = 
* Add table-responsive `div` wrap around table and responsive CSS.
* Suppress error message: 'Redefining already defined constructor...' on certain PHP version environment.
* Check against shortcode that may has been registered by another plugin.
* Increase `fgetcsv` limit from 2000 to 2000000 if $limit value not set. 

= 1.4 =
* Updated: TableSorter JavaScript library now updated to 2.10.8 from 2.0.5b ( hope it will solve many sorting problems )

= 1.3.1 =
* Fixed: Bug on version 1.3, fatal error on PHP prior to 5.3.0

= 1.3 =
* Fixed: `escape` is now working, you can use escape to skip `delimiter`s (also `terminator`s if they're not \r or \n) using escape (default escape character is backslash)

= 1.2 =
* Added: `align` parameter is now back. (Previously removed on version 1.1)

= 1.1.4 =
* Added new parameter 'fixlinebreak' to optionally convert newline to &lt;br /&gt; if terminator is not \r or \n

= 1.1.3 =
* Added: now you can use 'auto' for table width
* Table width now use inline style ( internally, not affected to the plugin usage )

= 1.1.2 =
* Fixed bug limit param doesn't work on version 1.1.1

= 1.1.1 =
* Fixed bug custom terminator doesn't work on version 1.1
* Removed align field on Option page

= 1.1 = 
* Removed: .htaccess from plugin directory (Fixed unloaded script on some servers)
* Use dedicated str_getcsv for Easy Table (Fixed incompatibility issue with AIOSP version 2.0)
* Removed: align attribute on table (Fixed text wrap issue)
* Added: new theme "minimal"

= 1.0 =
+ Encoding fix
* Added colalign param
* Added colwidth param
* Added style param
* Added limit param
* Added trim param
* Added terminator param
* Added nl2br if terminator is not \n nor \r
* Added is_search conditional option to load CSS/JS
* Improved admin UI, field description is now using tTooltip

= 0.9 =
* Fixed: Allow empty cell (was stripped on PHP prior to 5.3)
* Fixed: wp_remote_get() error if file URL was not found.
* Fixed: wrong application of wp_enqueue_script()
* Added: Allow script to be loaded in footer
* Removed: Redundant line on wp_enqueue_style
* Added: CSS for tfoot on cuscosky theme
* Added: New table parameter "nl", new line. eg. [table nl="~~"] See example here http://wordpress.org/extend/plugins/easy-table/
* Added: New table parameter "ai", auto index. add auto numbering in the begining of each row. See example here http://wordpress.org/extend/plugins/easy-table/

= 0.8 =
* Fixed: Csvfile option
* Fixed: Broken caption in Responsive theme
* Removed: Clearfix class from the table
* Changed: Now use wp_remote_get() instead of file_get_contents();
* Changed: Method name from get_easy_table_option() to option(), not affected to the usability
* Changed: .header class for thead changed to .easy-table-header to minimize conflict possibility with other CSS.
* Changed: path to jquery.tablesorter to /js and combined with jquery metadata
* Added: jquery.metadata.js to set additional sorting option
* Added: themes selector
* Added: css/easy-table.css for general basic styling
* Added: New sort parameter on table eg. [table sort="desc,asc"]
* Added: New sort attr on cell for default sorting, attr sort="desc",  attr sort="desc",  attr sort="false"
* Added: htaccess file to prevent directory listing on plugin path.
* Added: Compatibility with WordPress 3.4.1
* Improved: CSS for arrow up/down, arrow now only visible on mouseover or if column is sorted.

= 0.7 =
* Fixed: Enclosure in the first column does not work.
* Added: Compatibility with WordPress 3.4
* Fixed: Missing enclosure parameter in PHP < 5.3.0

= 0.6.1 =
* Fixed: Accidentally add unused character to the table

= 0.6 =
* Fixed: Missing tbody opening tag on some condition
* Fixed: Duplicate unit of width attribute

= 0.5 =
* Added: Ability to set attribute for each cell.
* Added: Support and About tab in plugin options page.
* Fixed: Table width attribute not work.
* Removed: Equalize the number of columns in each row.

= 0.4 =
* Fixed: Option value can't override default value if option value is empty (if checkbox is unchecked).
* Added: Optionally, tfoot now can be taken from last row. Example usage: [table tf="last"]somedata[/table]

= 0.3 =
* Improved: Option form now filled out with default value if there are no options saved in database and you don't need to save option to get the plugin to works.
* Added: Option to select where script and style should be loaded, eg. if only in single page.
* Added: tf parameter for tfoot, now you can set up tfoot for your table, tfoot picked up from 2nd row of the data, usage example [table tf="1"]data[/table]
* Added: Credit link to Twitter Bootstrap and tablesorter jQuery plugin.

= 0.2 =
* Fixed: Backward compatibility of str_getcsv that just not work in the version 0.1, now plugin should runs on PHP 5.2
* Fixed: Table now has 'table' class even when 'tablesorter' is not enabled.

= 0.1 =
* First release