<?php
 namespace Sabberworm\CSS\Parsing; class OutputException extends SourceException { public function __construct($sMessage, $iLineNo = 0) { parent::__construct($sMessage, $iLineNo); } }