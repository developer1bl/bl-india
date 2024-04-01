<?php 

if (! function_exists('encode')) {
    function encode($string)
    {
        return base64_encode($string);
    }
}

if (! function_exists('decode')) {
    function decode($string)
    {
        return base64_decode($string);
    }
}
?>