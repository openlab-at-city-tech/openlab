# WeBWorK Q&A

Integration between WeBWorK's "Ask for Help" feature and WordPress.

## Expected data format

WeBWorK Q&A integrates with WeBWorK via the "Ask for Help" button that appears on individual WeBWorK questions. This button should be configured to send a POST request to `https://example.com/?webwork=1`, where `https://example.com/` is the URL of the WordPress site running WeBWorK Q&A. The WordPress plugin expects an array of the following data in the POST payload:

Key | Content
----|--------
`user` | User name of the WW user initiating the request. Example: `'jsmith123'`.
`problem_set` | A string describing the WW problem set. Example: `'Trigonometry_-_Inverse_Functions'`.
`problem_number` | The numeric ID of the WW problem within its set.
`pg_object` | The problem content, rendered in HTML and then base64-encoded. Ideally, this problem should contain only the markup for the problem, and not be a full HTML document (containing `<head>`, `<script>`, etc), though WeBWorK Q&A will attempt to strip extraneous elements. Where possible, static assets in the post content (such as images) should use absolute paths, as the WordPress plugin will attempt to grab these images and copy them to the WP site.
`courseId` | A string containing the WeBWorK course name. Used to populate the 'Select Course' sidebar filter. Example: `MAT 321 - Set Theory - Spring 2021`
`problemPath` | The unique identifier for the problem. Example: `'Library/ASU-topics/setDerivativeFunction/3-3-05.pg'`
`notifyAddresses` | The email address of the course instructor. This is the email address that will receive email notifications about relevant student activity in WordPress. Supports the "angle bracket" format, i.e. `'Professor Foo <foo@example.com>'`. Supports multiple addresses, separated by `;`.
`randomSeed` | The `randomSeed` property from WeBWorK.
`emailableURL` | URL of the student's version of the WW problem. Example: `'http://example.com/webwork/CourseID/problemSetName/problemNumber/?showOldAnswers=1&effectiveUser=student&displayMode=MathJax'`
`studentName` | The display name of the student from WW. Example: `'John Smith'`.
