# Google Docs Shortcode #

Google Docs Shortcode is a small plugin for WordPress that allows you to use a [shortcode](https://codex.wordpress.org/Shortcode) to easily embed a Google Doc into your blog posts or pages.

**Recommended:** Install the [Shortcake](https://wordpress.org/plugins/shortcode-ui/) plugin to get instant Google Doc previews in the WordPress editor.

This plugin was developed for the [CUNY Academic Commons](http://commons.gc.cuny.edu).  Licensed under the GPLv2 or later.

***

### How to Use

#### Embedding a document, slide or sheet
1. First, you'll need to find the public URL of your Google Doc file. Follow the [guide](#sharing-a-google-drive-file-and-getting-the-link) below, then move on to step 2.
2. Now navigate to your WordPress dashboard and open up the post or page where you want to embed your document. On a new line, type the following shortcode and paste in the link you copied, above:

  <pre>[gdoc link="THE LINK YOU COPIED" height="800"]</pre>
    
You can customize the shortcode by using [some custom parameters mentioned below](#other-shortcode-parameters).

#### Embedding an audio file
Follow the steps above, but change the shortcode to:

  <pre>[gdoc link="THE LINK YOU COPIED" type="audio"]</pre>
  
Notice the `type` is set to `audio`.

When you embed an audio file, we will use the browser's native HTML5 audio player.

#### Embedding any other file

For every other file type (image, PDF, Microsoft Office, etc.), follow the steps above, but change the shortcode to:

  <pre>[gdoc link="THE LINK YOU COPIED" type="other"]</pre>
  
Notice the `type` is set to `other`.

When you embed a miscellaneous file, we will use Google's preview embed viewer to display the file.

#### Embedding a form
1. Login to Google Drive and locate your form.  Open it.
2. Next, navigate to **View > Live form.**  This should take you to the public version of the form.  Copy the URL from your browser's address bar.
3. Follow step 2 from the "Embedding a document, slide or sheet" section above.

***

### Sharing a Google Drive file and getting the link
1. Let's start by [logging in to your Google Drive](https://drive.google.com). Next, find the item you want to embed.<br><br>
2. Now, right-click on the file and click on **Share**:<br>
    ![Right-click and Share](https://cloud.githubusercontent.com/assets/505921/10205871/9af2fab0-6778-11e5-937e-eab1d30fc2c1.png)<br><br>
3. You should now be on the **Share with Others** window.  If the "Get Shareable link" is not green, click on it:<br>
    ![Share with Others window](https://cloud.githubusercontent.com/assets/505921/10206017/8cf0b6b8-6779-11e5-8b2f-444e112a0c59.png)<br>
    This will make your Google Drive file shareable to anyone with the link, which will allow us to embed the file in your post.<br><br>
4. Now, copy the link from the text field highlighted below:<br>
    ![Copy share link](https://cloud.githubusercontent.com/assets/505921/10206018/8cf2af54-6779-11e5-98b8-a75f7c76c205.png)

### Alternate Instructions on iOS Devices
Note: On iOS, users are required to interact with Google via the Apps, rather than through the browser. 
1. Open the Google Document and select the ellipsis in the top right corner:
    ![img_0002](https://user-images.githubusercontent.com/480667/48864926-13f05a00-ed9c-11e8-95ce-087ac5d220b0.jpg)
2. Select **Share and Export** in the resulting panel:

    ![img_0003](https://user-images.githubusercontent.com/480667/48864965-24a0d000-ed9c-11e8-8de4-336bbf93c7cb.jpg)
3. Select **Copy Link** and the link will be copied to your clipboard:

    ![img_0004](https://user-images.githubusercontent.com/480667/48865000-31bdbf00-ed9c-11e8-91d5-dcf4848cbf4a.jpg)
    
4. That link requires **/edit?usp=sharing** to be manually appended to the end of the URL in order for the shortcode to display the content correctly. 
***

### Other shortcode parameters

Here are some other custom parameters you can use with the shortcode:

* "width" - By default, this tries to use your theme's content width. If this doesn't exist, the width is "100%". Fill in this value to enter a custom width.

* "height" - Enter in a custom height for your Google Doc if desired. Defaults to "300". Avoid percentages.

* "seamless" - This parameter is only applicable to Documents. If you enter "0", this will show the Google Docs header and footer.  Default value is "1", which means that no Google Docs header or footer will be shown.  **Note:** This option only works if your document was [published to the web](https://support.google.com/docs/answer/37579?hl=en) and not link-shared.

* "size" - This parameter is only applicable to Slides.  You can enter in "small", "medium" or "large" to use the presentation preset sizes. Dimensions for these presets are: small (480x299), medium (960x559), large (1440x839). To set a custom width and height, use the "width" and "height" parameters listed above instead.

* "type" - This parameter is only applicable to non-Google Doc types.  If the file you want to embed is an audio file, set the type to `audio`.  Otherwise, set the type to `other` for every other file type (image, PDF, Microsoft Office, etc.) and we will attempt to embed the file using Google's preview embed viewer.

***

### Thanks

* [Hamilton-Wentworth District School Board Commons](http://commons.hwdsb.on.ca) - for sponsoring [Shortcake](https://wordpress.org/plugins/shortcode-ui/) and embedding non-Google Doc types.
* Scott Voth - for testing and writing a version of this documentation on the CUNY Academic Commons codex.
* Christopher Stein - for noting a bug about using older slides with the plugin.
