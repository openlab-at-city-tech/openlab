<div class="wrap">
<?php screen_icon(); $dir = network_site_url('/');?>
<h2>wp-api administration</h2>
<table class="widefat">
<thead>
    <tr>
        <th width=100>Method</th>
        <th width=200>Description</th>
        <th>arguments</th>
    </tr>
</thead>
<tfoot>
    <tr>
    	<th width=100>Method</th>
    	<th width=200>Description</th>
    	<th>arguments</th>
    </tr>
</tfoot>
<tbody>
   <!-- posts -->	
   <tr>
   
     <td><code><a href="#" id="i_p">get_posts</a></code></td>
     
     <td id="p">Call this method to get the recent posts/pages or specify the post/page id to get the specified post information.</td>
     
     <td id="p2">
	 argument: <b>dev</b> (optional)<br>
	 value: <b>1</b> &gt;&gt; pretty output in browser<br>
	 value: <b>0</b> &gt;&gt; default json format to use in your program<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_posts&dev=1" target="_blank"><?php echo $dir ?>?wpapi=get_posts&<b>dev=1</b></a>
	 <hr>
	 
	 argument: <b>id</b> (optional)<br>
	 value: <b>post or page id</b> &gt;&gt; show information about the specified post<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_posts&dev=1&id=1" target="_blank"><?php echo $dir ?>?wpapi=get_posts&dev=1&<b>id=1</b></a>
	 <hr>
	 
	 argument: <b>count</b> (optional)<br>
	 value: <b>number of items per page</b> &gt;&gt; specify the number of post items to be returned.<br><br>
	 
	 argument: <b>page</b> (optional)<br>
	 value: <b>page number</b> &gt;&gt; if <i>count</i> is set, use the <i>page</i> argument to move forward through the rest of the items.<br><br>
	 
	 example: <a href="<?php echo $dir?>?wpapi=get_posts&dev=1&count=2&page=1" target="_blank"><?php echo $dir ?>?wpapi=get_posts&dev=1&<b>count=2</b>&<b>page=1</b></a>
	 <hr>
	 
	 argument: <b>comment</b> (optional)<br>
	 value: <b>1</b> &gt;&gt; Return comments if available<br>
	 value: <b>0</b> &gt;&gt; Don't return comments<br><br>
	 
	 argument: <b>content</b> (optional)<br>
	 value: <b>1</b> &gt;&gt; Return the content<br>
	 value: <b>0</b> &gt;&gt; Don't return the content<br><br>
	 
	 example: <a href="<?php echo $dir?>?wpapi=get_posts&dev=1&comment=1&content=1" target="_blank"><?php echo $dir ?>?wpapi=get_posts&dev=1&<b>comment=1</b>&<b>content=1</b></a>
     <hr>
     argument: <b>type</b> (optional)<br>
	 value: <b>posts type</b> &gt;&gt; Put post and get only posts, put page and get only pages <br>
	 example: <a href="<?php echo $dir?>?wpapi=get_posts&dev=1&type=page" target="_blank"><?php echo $dir ?>?wpapi=get_posts&dev=1&<b>type=page</b></a>
	 </td>
    </tr>
   <!-- tags -->
   <tr>
   	 
   	 <td><code><a href="#" id="i_t">get_tags</a></code></td>
   	 
   	 <td id="t">Call this method to retrieve all tag information</td>
   	 
   	 <td id="t2">
   	 argument: <b>dev</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>count</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>page</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_tags&dev=1" target="_blank"><?php echo $dir ?>?wpapi=get_tags&dev=1</a>
	 <hr>
	 argument: <b>id</b> (optional)<br>
	 value: <b>tag id</b> &gt;&gt; return information about the specified tag<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_tags&dev=1&id=1" target="_blank"><?php echo $dir ?>?wpapi=get_tags&dev=1&<b>id=1</b></a>
	 <hr>
	 argument: <b>cat</b> (optional)<br>
	 value: <b>1</b> &gt;&gt; returns those tags with taxonomy of category<br>
	 value: <b>0</b> &gt;&gt; does not return tags with taxonomy of category<br><br>
	 
	 argument: <b>tag</b> (optional)<br>
	 value: <b>1</b> &gt;&gt; returns normal tags<br>
	 value: <b>0</b> &gt;&gt; does not return normal tags<br><br>
	 
	 example: <a href="<?php echo $dir?>?wpapi=get_tags&dev=1&cat=1&tag=1" target="_blank"><?php echo $dir ?>?wpapi=get_tags&dev=1&<b>cat=1</b>&<b>tag=1</b></a>
   	 </td>
   	 
   </tr>
   <!-- author -->
   <tr>
     
     <td><code><a href="#" id="i_a">get_author</a></code></td>
     
     <td id="a">Call this method to retrieve information about authors registered in the wordpress blog</td>
     
     <td id="a2">
   	 argument: <b>dev</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>count</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>page</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_author&dev=1" target="_blank"><?php echo $dir ?>?wpapi=get_author&dev=1</a>
	 <hr>
	 argument: <b>id</b> (optional)<br>
	 value: <b>author id</b> &gt;&gt; return information about the specified author<br>
	 example: <a href="<?php echo $dir?>?wpapi=get_author&dev=1&id=1" target="_blank"><?php echo $dir ?>?wpapi=get_author&dev=1&<b>id=1</b></a>
	 </td>
     
   </tr>
   <!-- gravatar -->
   <tr>
     
     <td><code><a href="#" id="i_g">gravatar</a></code></td>
     
     <td id="g">Call this method to connect to the gravatar api and retrieve the gravatar based on the email address you specify.</td>
     
     
     <td id="g2">
     argument: <b>dev</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 example: <a href="<?php echo $dir?>?wpapi=gravatar&dev=1" target="_blank"><?php echo $dir ?>?wpapi=gravatar&dev=1</a>
	 <hr>
	 argument: <b>email</b> (required)<br>
	 value: <b>standard email address</b> &gt;&gt; the email address to found its gravatar<br><br>
	 
	 argument: <b>size</b> (optional)<br>
	 value: <b>size of the gravatar in pixel</b> &gt;&gt; size of the returned gravatar image, default value is 100 which means the returned gravatar image will be 100X100 pixel<br><br>
	 example: <a href="<?php echo $dir?>?wpapi=gravatar&dev=1&email=email@site.com&size=200" target="_blank"><?php echo $dir ?>?wpapi=gravatar&dev=1&<b>email=email@site.com</b>&<b>size=200</b></a>
	 </td>
     
   </tr>
   <!-- search -->
   <tr>
     
     <td><code><a href="#" id="i_s">search</a></code></td>
     
     <td id="s">Call this method and pass <i>keyword</i> argument to return the search results.</td>
     
     
     <td id="s2">
     argument: <b>dev</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>count</b> (optional) Read more about it in <i>get_posts</i> method<br>
   	 argument: <b>page</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 argument: <b>comment</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 argument: <b>content</b> (optional) Read more about it in <i>get_posts</i> method<br>
     argument: <b>type</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 example: <a href="<?php echo $dir?>?wpapi=search&dev=1&keyword=post&count=2&page=1&content=1&comment=1&type=post" target="_blank"><?php echo $dir ?>?wpapi=search&dev=1&keyword=post&<b>count=2</b>&<b>page=1</b>&<b>content=1</b>&<b>comment=1</b>&<b>type=post</b></a>
	 <hr>
	 argument: <b>keyword</b> (required)<br>
	 value: <b>search string</b> &gt;&gt; the keyword to be searched.<br>
	 example: <a href="<?php echo $dir?>?wpapi=search&dev=1&keyword=something" target="_blank"><?php echo $dir ?>?wpapi=search&dev=1&<b>keyword=something</b></a>
     </td>
     
   </tr>
   <!-- comment -->
   <tr>
     
     <td><code><a href="#" id="i_c">comment</a></code></td>
     
     <td id="c">Call this method to submit a comment on a post.</td>
     
     <td id="c2">
     argument: <b>dev</b> (optional) Read more about it in <i>get_posts</i> method<br>
	 example: <a href="<?php echo $dir?>?wpapi=comment&dev=1" target="_blank"><?php echo $dir ?>?wpapi=comment&dev=1</a>
	 <hr>
	 argument: <b>name</b> (required)<br>
	 value: <b>author name</b> &gt;&gt; name of the person who's leaving the comment<br><br>
	 
	 argument: <b>email</b> (required)<br>
	 value: <b>author email address</b> &gt;&gt; email address of the person who's leaving the comment<br><br>
	 
	 argument: <b>content</b> (required)<br>
	 value: <b>comment content</b> &gt;&gt; content of the comment<br><br>
	 
	 argument: <b>post_id</b> (required)<br>
	 value: <b>post id</b> &gt;&gt; id of the post which you are submitting your comment to<br><br>
	 
	 example: <a href="<?php echo $dir?>?wpapi=comment&dev=1&name=Hadi&email=email@site.com&content=some content to comment&post_id=1" target="_blank"><?php echo $dir ?>?wpapi=comment&dev=1&<b>name=Hadi</b>&<b>email=email@site.com</b>&<b>content=some content to comment</b>&<b>post_id=1</b></a>
     <hr>
	 argument: <b>website</b> (optional)<br>
	 value: <b>www.your-site.com</b> &gt;&gt; Optionally you can pass author's website<br><br>
	 
	 argument: <b>parent</b> (optional)<br>
	 value: <b>reply to id</b> &gt;&gt; <i>parent</i> is the id of the comment which this will be a reply to. default value is <i>0</i><br><br>
	 
	 example: <a href="<?php echo $dir?>?wpapi=comment&dev=1&name=Hadi&email=email@site.com&content=some content to comment&post_id=1&website=www.your-site.com&parent=0" target="_blank"><?php echo $dir ?>?wpapi=comment&dev=1&name=Hadi&email=email@site.com&content=some content to comment&post_id=1&<b>website=www.your-site.com</b>&<b>parent=0</b></a>
     </td>
     
   </tr>
</tbody>
</table>
</div>
