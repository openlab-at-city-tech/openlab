<?php

namespace RebelCode\Spotlight\Instagram;

use Throwable;
use Generator;
use Exception;

/** The class that manages error logging via WordPress debug log. */
class ErrorLog
{
    /**
     * @deprecated 1.7.2 This constant is no longer needed but remains for backward compatibility.
     * Avoid using it; logs are now written to WordPress debug.log.
     */
    private const FALLBACK_FILE = 'spotlight-error-log.txt';

    /**
     * @deprecated 1.7.2 This constant is no longer needed; WordPress manages log file sizes.
     * This will be removed in a future version.
     */
    private const MAX_SIZE = 1048576;

    /**
     * Gets the path to the WordPress debug log file.
     *
     * @since 1.7.2
     * @return string The path to the WordPress debug log file.
     */
    public static function getDebugLogPath(): string
    {
        return WP_CONTENT_DIR . '/debug.log';
    }

    /**
     * Logs a message to the WordPress debug log.
     *
     * @param string $message The message to log.
     */
    public static function message(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log(self::entryToString($message));
        }
    }

    /**
     * Logs an exception to the WordPress debug log.
     *
     * @param Throwable $exception The exception to log.
     */
    public static function exception(Throwable $exception): void
    {
        $message = sprintf(
            "Exception: %s in %s:%d\nStack trace:\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        self::message($message);
    }

    /**
     * Calls the function and logs any exceptions that are thrown.
     *
     * @param callable $fn The function to call.
     */
    public static function catch(callable $fn): void
    {
        try {
            $fn();
        } catch (Throwable $exception) {
            self::exception($exception);
        }
    }

    /** Gets the size of the WordPress debug log file. */
    public static function getSize(): int
    {
        $path = self::getDebugLogPath();
        return file_exists($path) ? (int) filesize($path) : 0;
    }

    /**
     * Gets the last modified time of the WordPress debug log file.
     *
     * @return string|null ISO 8601 date string, or null if the file does not exist.
     */
    public static function getLastModified(): ?string
    {
        $path = self::getDebugLogPath();
        return file_exists($path) ? date(DATE_ATOM, filemtime($path)) : null;
    }

    /** Reads the entire contents of the WordPress debug log file. */
    public static function read(): string
    {
        $path = self::getDebugLogPath();
        return file_exists($path) ? file_get_contents($path) : '';
    }

    /**
     * Reads the WordPress debug log file in chunks using a generator.
     *
     * @param int $chunkSize The size of each chunk to read, in bytes.
     */
    public static function readChunks(int $chunkSize): Generator
    {
        $path = self::getDebugLogPath();

        if (file_exists($path)) {
            $file = fopen($path, 'r');

            if ($file !== false) {
                try {
                    while (!feof($file)) {
                        yield fread($file, $chunkSize);
                    }
                } finally {
                    fclose($file);
                }
            }
        }
    }

    /**
     * Reads the WordPress debug log file as lines, using chunk-based reading.
     *
     * @param int $chunkSize The size of each chunk.
     */
    public static function readLines(int $chunkSize): Generator
    {
        $buffer = '';

        foreach (self::readChunks($chunkSize) as $chunk) {
            $lines = explode(PHP_EOL, $buffer . $chunk);
            $buffer = array_pop($lines);

            foreach ($lines as $line) {
                yield $line;
            }
        }
    }

    /**
     * Reads the WordPress debug log file as separate log entries.
     *
     * @return Generator<array{time: string,message: string}>
     */
    public static function readEntries(): Generator
    {
        $curr = null;

        foreach (self::readLines(128 * 1024) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (stripos($line, '[') === 0) {
                if ($curr !== null) {
                    yield $curr;
                }

                preg_match('/^\[(.*?)]\s?(.*?)$/', $line, $matches);

                if (is_array($matches) && count($matches) === 3) {
                    $curr = [
                        'time' => $matches[1],
                        'message' => $matches[2],
                    ];
                }
            } elseif ($curr !== null) {
                $curr['message'] .= PHP_EOL . $line;
            }
        }

        if ($curr !== null) {
            yield $curr;
        }
    }

    /** Deletes the WordPress debug log file. */
    public static function delete(): bool
    {
        try {
            $path = self::getPath();

            if (!file_exists($path)) {
                return true;
            }

            if (!is_writable($path)) {
                throw new Exception("File is not writable: $path");
            }

            if (!unlink($path)) {
                throw new Exception("Failed to delete log file: $path");
            }

            return true;
        } catch (Throwable $exception) {
            self::exception($exception);
            return false;
        }
    }

    /** Transforms an entry into a formatted log string. */
    protected static function entryToString(string $message, ?string $time = null): string
    {
        $time = $time ?? date(DATE_ATOM);
        return "[{$time}] [Spotlight WP] " . $message;
    }

    /**
     * Prepends the given text to the error log file.
     *
     * @deprecated 1.7.2 This method is no longer needed since logging is handled via WordPress debug.log.
     *
     * @param string $text The text to prepend.
     */
    protected static function prepend(string $text): void
    {
        _deprecated_function(
            __METHOD__,
            '1.7.2',
            'WordPress debug.log is now used for logging. This method is no longer necessary.'
        );

        error_log($text);
    }


    /**
     * Gets the path to the error log file.
     *
     * Since version 1.7.2, this method now returns the WordPress debug log path instead of a custom file.
     * This method is deprecated and no longer used.
     *
     * @since 1.0.0
     * @deprecated 1.7.2 Use getDebugLogPath instead.
     *
     * @param string $name (Deprecated) The name of the log file. This parameter is ignored.
     * @return string The path to the WordPress debug log file.
     */
    public static function getPath(string $name = 'spotlight-error-log.txt'): string
    {
        _deprecated_function(
            __METHOD__,
            '1.7.2',
            'WordPress debug.log is now used for logging. This method is no longer necessary use getDebugLogPath instead.'
        );

        $uploadDir = wp_upload_dir();

        if ($uploadDir['error'] || empty($uploadDir['basedir'])) {
            return static::FALLBACK_FILE;
        } else {
            return $uploadDir['basedir'] . '/' . $name;
        }
    }

}
