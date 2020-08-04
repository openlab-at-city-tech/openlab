<?php

namespace Kunnu\Dropbox\Models;

class ExportInfo extends BaseModel {

   /**
     * Get the 'export_as' property of the file model.
     *
     * @return string
     */
    protected $export_as;

    public function __construct(array $data) {
        parent::__construct($data);
        $this->export_as = $this->getDataProperty('export_as');
    }

    /**
     * Get the 'export_as' property of the metadata.
     *
     * @return string
     */
    public function getExportAs() {
        return $this->export_as;
    }

}
