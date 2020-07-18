<?php

namespace Kunnu\Dropbox\Models;

class AsyncJob extends BaseModel {

    /**
     * This response indicates that the processing is asynchronous. The string is an id that can be used to obtain the status of the asynchronous job.
     *
     * @var string
     */
    protected $tag;
    protected $async_job_id;

    public function __construct(array $data) {
        parent::__construct($data);
        $this->tag = $this->getDataProperty('.tag');
        $this->async_job_id = $this->getDataProperty('async_job_id');
    }

    /**
     * Get the 'tag' property of the metadata.
     *
     * @return string
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * Get the 'async_job_id' property of the metadata.
     *
     * @return string
     */
    public function getAsyncJobId() {
        return $this->async_job_id;
    }

}
