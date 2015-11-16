<p class="clear"></p>
<br/>
<h3>Export Glossary Terms</h3>
<form method="post">
    <input type="submit" value="Export to CSV" name="cmtt_doExport" class="button button-primary"/>
</form>
<br/><br/><br/><br/>
<h3>Import Glossary Terms from File</h3>
<p>
    If the term already exists in the database, only content is updated. Otherwise, new term is added.
</p>
<p>
    <strong>Important!!</strong> File should be UTF-8 encoded and, if you use MS Excel, please remember that by default it can't save proper CSV format (comma-delimited) - see <a href="http://support.microsoft.com/kb/291296" target="_blank" rel="nofollow">Microsoft Knowledge Base Article</a></p>

<?php if( isset($_GET['msg']) && $_GET['msg'] == 'imported' ): ?>
    <div id="message" class="updated below-h2">File <?php
        if( $_GET['itemsnumber'] == 0 ) echo 'import failed';
        else 'succesfully imported';
        ?> (<?php echo $_GET['itemsnumber']; ?> items read from file)</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="importCSV" />
    <input type="submit" value="Import from CSV" name="cmtt_doImport" class="button button-primary"/>
</form><br />
Format example:<br />
<pre>
Id,Title,Excerpt,Description,Synonyms,Variations,Categories,Tags,Meta
100,"Example Term","Example term excerpt text","Description, if multiline then uses&lt;br&gt;HTML element","synonym1,synonym2","variation1,variation2","categoryID1,categoryID2","tagID1,tagID2"
101,"Another",,"Excerpt can be empty",,
</pre>