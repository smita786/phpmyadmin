<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Manage Central List of Columns
 *
 * @package PhpMyAdmin
 */
/**
 * Gets some core libraries
 */
require_once 'libraries/common.inc.php';
require_once 'libraries/Util.class.php';
require_once 'libraries/central_columns.lib.php';
// when called by libraries/mult_submits.inc.php
if (!empty($_POST['selected_tbl']) && empty($table_select)) {
    $table_select = $_POST['selected_tbl'];
}
if (!empty($_POST['submit_mult']) 
    && $_POST['submit_mult'] != __('With selected:') 
    && (!empty($selected_db) 
    || !empty($_POST['selected_tbl']))
) {
    $submit_mult = $_POST['submit_mult'];
    if ($submit_mult == 'sync_unique_columns_central_list') {
        $centralColsError = sync_unique_columns($table_select);
    } else if ($submit_mult == 'delete_unique_columns_central_list') {
        $centralColsError = delete_coulumns_from_list($table_select);
    }
}


?>