<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2020 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

spl_autoload_register(function ($class) {
$class=str_replace("TCPDF","Tcpdf",$class);
//echo"<HR> xxx ";
    if (strpos($class, 'setasign\Fpdi\\') === 0) {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 14)) . '.php';
        $fullpath = __DIR__ . DIRECTORY_SEPARATOR . $filename;

//echo"$class - $fullpath";
        if (file_exists($fullpath)) {
//echo" OK!!!";
            /** @noinspection PhpIncludeInspection */
            require_once $fullpath;
        }
//echo"yyy <HR>";
    }
});
