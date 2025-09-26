<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Format timestamp to dd/mm/yyyy hh:mm format
 * 
 * @param int $timestamp Unix timestamp
 * @return string Formatted date string
 */
function local_createtable_format_datetime($timestamp) {
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Format timestamp to dd/mm/yyyy format (date only)
 * 
 * @param int $timestamp Unix timestamp
 * @return string Formatted date string
 */
function local_createtable_format_date($timestamp) {
    return date('d/m/Y', $timestamp);
} 
