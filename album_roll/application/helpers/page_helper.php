<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("PAGED_LIST_SIZE", 4);

if ( ! function_exists('next_available'))
{
    function next_available($offset, $count)
    {
        return $offset + PAGED_LIST_SIZE < $count;
    }   
}

if ( ! function_exists('back_available'))
{
    function back_available($offset)
    {
        return $offset != 0;
    }   
}

if ( ! function_exists('current_page'))
{
    function current_page($offset)
    {
        return floor($offset / PAGED_LIST_SIZE) + 1;
    }   
}

if ( ! function_exists('pageable'))
{
    function pageable($count)
    {
        return $count > PAGED_LIST_SIZE;
    }   
}
?>