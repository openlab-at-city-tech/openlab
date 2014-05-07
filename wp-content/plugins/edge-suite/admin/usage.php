<div class="wrap">
  <h2>Edge Suite - Usage & Features</h2>

  <h3>Installation and usage</h3>
  <ol>
    <li>IMPORTANT: Backup your complete wordpress website, this module is in
      early development state!
    </li>
    <li>Install the Edge Suite plugin as any other wordpress plugin.</li>
    <li>Make sure /wp-content/uploads/edge_suite was created and is writable.
    </li>
    <li>Publish your project in Adobe Edge with option "Animate Deployment Package". It will generate a single OAM file.</li>
    <li>Go to <a href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">Manage</a>,
        select the oam file in the "publish/animate package" folder and upload it.
    </li>
    <li>Upload as many composition as you want.</li>

    <li>After uploading, the compositions can be placed in multiple ways on the website:</li>
    <ol>
        <li><strong>Shortcodes:</strong>
            <ul>
                <li>Take a look at the manage page drop down and remember the id of the composition you want to integrate. E.g. for "3 - My first edge compositions" the id is 3.</li>
                <li>Edit a page and include [edge_animation id="3"] where 3 is of course your composition id.</li>
                <li>Save the post, the composition shows up.</li>
                <li>You can also use [edge_animation id="3" left="auto"] to center the stage on the page.</li>
                <li>If you want to define a pixel base left an top offset of e.g. 10px from the left and 20px from the top, try [edge_animation id="3" left="10" top="20"]</li>
            </ul>
        </li>
        <li><strong>Template based:</strong>
            <ul>

                <li>Here you insert a PHP snippet in your theme files:</li>
                <li>Backup your complete theme folder.</li>
                <li>Find e.g. the header.php file in your theme.</li>
                <li>Insert the following snippet where the compositions should
                    appear:
      <pre>
        &lt;?php
          if(function_exists('edge_suite_view')){
            echo edge_suite_view();
          }
        ?&gt;
      </pre>
                    Placing the code within in a link tag (&lt;a href=""...) can cause
                    problems when the composition is interactive.
                    You might also want to remove code that places other header images e.g.
                    calls to header_image() or get_header_image() in
                    case the composition should be the only thing in the header.
                </li>


                <li><p>You now have three options to tell wordpress which composition to show
                    where the PHP snippet was placed.</p>
                    <ol>
                        <li>Default: A composition that should be shown on all pages can be
                            selected on the <a
                                    href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">settings
                                page</a> "Default composition".
                        </li>
                        <li>Homepage: A composition that is only meant to show up on the
                            homepage can also be selected there.
                        </li>
                        <li>Page/Post: In editing mode each post or a page has a composition
                            selection that, when chosen, will overwrite the default composition.
                        </li>
                    </ol>
                </li>
            </ul>
        </li>
    </ol>


  Please report any bugs to the <a
  href="http://wordpress.org/support/plugin/edge-suite">Edge Suite support
  queue</a>.<br>
  More resources on <a href="http://edgedocks.com/edge-suite">Edgedocks - Edge Suite</a>

  <h3>Features</h3>
  <ul style="list-style: disc; padding-left:25px">
    <li>Upload Adobe Edge compositions within one zipped archive</li>
    <li>Manage all compositions</li>
    <li>Easy placement of compositions on the website</li>
  </ul>

</div>
