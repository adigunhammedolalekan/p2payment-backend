<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 5:07 AM
 */

function success($data) {
    return array('status' => true, 'data' => $data);
}

function error($message) {
    return array('status' => false, 'message' => $message);
}
?>