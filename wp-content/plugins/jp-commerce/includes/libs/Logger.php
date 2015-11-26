<?php

/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 11/6/15
 * Time: 3:05 AM
 */
class Logger {
    private $logfilepath;
    public $errno = 0;
    public $content;

    const LOGGER_ERR_FILE_NOT_FOUND = -1;
    const LOGGER_ERR_FILE_NOT_READABLE = -2;
    const LOGGER_ERR_ERROR_READING_FILE = -3;

    function __construct(){
        $this->logfilepath = JC_LOG_DIR . 'debug.txt';
        if(!file_exists($this->logfilepath)) $this->errno = -1; 		// file existence
        if (!is_readable($this->logfilepath)) $this->errno = -2;	// file readable
        $this->content = file_get_contents($this->logfilepath);
        if (!$this->content)														// error reading file
            $this->errno = -3;
    }

    /*
        Add a log message to the log file

        string|object, string|object -> 0 | -1
        Return 0 upon successful
        return -1 when failed to write to file
    */
    function log_action($action, $message = "") {
        $file_exists = file_exists($this->logfilepath);
        $fd = fopen($this->logfilepath, "a");
        if (!fwrite($fd, strftime("%F %T | " . print_r($action, true) . ": " . print_r($message, true) . "\n")))
            return -1;
        fclose($fd);
        return 0;
    }

    function clear_log() {
        if (false === file_put_contents($this->logfilepath, ""))
            $this->log_action("log", "clear failed");
        else
            $this->log_action("log", "cleared.");
    }
}

global $logger;
$logger = new Logger();