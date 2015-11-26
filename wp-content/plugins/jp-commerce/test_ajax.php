<?php
/**
 * Created by PhpStorm.
 * User: jjpro
 * Date: 11/26/15
 * Time: 5:45 AM
 */

define( 'JC_LOG_DIR', '../../uploads/jc-logs/' );
include_once("includes/libs/Logger.php");

$logger->log_action($_POST["action"]);