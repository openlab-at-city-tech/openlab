<?php namespace Kunnu\Dropbox\Exceptions;

use Exception;

/**
 * DropboxClientException
 */
class DropboxClientException extends Exception
{

    /**
     * Summary of the error
     *
     * @var string
     */
    protected $error_summmary;

    /**
     * Error tag
     *
     * @var string
     */
    protected $error;

    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $decoded_error = json_decode((string) $this->getMessage(), true);
        if (!empty($decoded_error)) {
            $this->error_summmary = isset($decoded_error['error_summary']) ? $decoded_error['error_summary'] : '';
            $this->error = isset($decoded_error['error']) ? var_export($decoded_error['error'],true) : '';
        }
    }

    public function getErrorSummary()
    {
        return $this->error_summmary;
    }

    public function getError()
    {
        return $this->error;
    }
}
