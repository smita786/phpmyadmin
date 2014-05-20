<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Functions for displaying user preferences pages
 *
 * @package PhpMyAdmin
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * get all columns of given database from central columns list 
 * 
 * @param string $db selected databse
 * 
 * @return array associative array of table name and corresponding columns from the columns list
 */
function get_columns_list($db)
{
    $db = $_POST['db'];
    $pmadb = $GLOBALS['cfg']['Server']['pmadb'];
    // echo $db;
    $GLOBALS['dbi']->selectDb($pmadb);
    $central_list_table = $GLOBALS['cfg']['Server']['central_columns'];
    //get current values of $db from central column list
    $query = 'SELECT * FROM ' . PMA_Util::backquote($central_list_table) . ' '
            . 'WHERE db_name = \'' . $db . '\';';
    $has_list = (array) json_decode($GLOBALS['dbi']->fetchValue($query, 0, 1));
    return $has_list;
}
/**
 * Sync unique columns of given tables with central columns list @todo eliminate duplicates
 * 
 * @param array $table_select selected tables list
 * 
 * @return true|PMA_Message
 */
//functions will be put in libraries/central_columns.lib.php may be
function sync_unique_columns($table_select) 
{
    $db = $_POST['db'];
    $pmadb = $GLOBALS['cfg']['Server']['pmadb'];
    $central_list_table = $GLOBALS['cfg']['Server']['central_columns'];
    $GLOBALS['dbi']->selectDb($db);
    $has_list = get_columns_list($db);
    $sync = $has_list;
    $sync_tmp = array();
    foreach ($table_select as $table) {
        $fields = (array) $GLOBALS['dbi']->getColumns($db, $table, null, true);
        foreach ($fields as $field => $def) {
            $sync_tmp[$field] = $def['Type'] . " " . $def['key'] . " " . $def['collation'] . " " . $def["Null"] . " " . $def['Extra'];
            //echo $field . " " . $def['Type'] . " " . $def['key'] . " " . $def['collation'] . " " . $def["Null"] . " " . $def['Extra'] . "<br>";
        }
        $sync[$table] = $sync_tmp;
        //echo '<br><br>';
    }
    $GLOBALS['dbi']->selectDb($pmadb);
    if (!$has_list) {
        $query = 'INSERT INTO ' . PMA_Util::backquote($central_list_table) . ' '
                . 'VALUES ( \'' . $db . '\' , \'' . PMA_Util::sqlAddSlashes(json_encode($sync)) . '\' );';
        
    } else {
        $query = 'UPDATE ' . PMA_Util::backquote($central_list_table) . ' '
                . 'SET column_list = \'' . PMA_Util::sqlAddSlashes(json_encode($sync)) . '\' '
                . 'WHERE db_name = \'' . $db . '\';';
    }
    if (!$GLOBALS['dbi']->tryQuery($query, $GLOBALS['controllink'])) {
        $message = PMA_Message::error(__('Could not sync columns!'));
        $message->addMessage('<br /><br />');
        $message->addMessage(
            PMA_Message::rawError(
                $GLOBALS['dbi']->getError($GLOBALS['controllink'])
            )
        );
        return $message;
    }
    return true;
}
/**
 * remove all columns of given tables from central columns list
 * 
 * @param array $table_select selectd list of tables
 * 
 * @return true|PMA_Message
 */
function delete_coulumns_from_list($table_select) 
{
    $db = $_POST['db'];
    $pmadb = $GLOBALS['cfg']['Server']['pmadb'];
    $central_list_table = $GLOBALS['cfg']['Server']['central_columns'];
    $GLOBALS['dbi']->selectDb($db);
    $has_list = get_columns_list($db);
    $sync = $has_list;
    foreach ($table_select as $table) 
    {
        unset($sync[$table]);
    }
    $GLOBALS['dbi']->selectDb($pmadb);
    if (!$has_list) {
        $query = 'INSERT INTO ' . PMA_Util::backquote($central_list_table) . ' '
                . 'VALUES ( \'' . $db . '\' , \'' . PMA_Util::sqlAddSlashes(json_encode($sync)) . '\' );';
        
    } else if(!$sync) {
        $query = 'DELETE FROM ' . PMA_Util::backquote($central_list_table) . ' '
                . 'WHERE db_name = \'' . $db . '\';';
    } else {
        $query = 'UPDATE ' . PMA_Util::backquote($central_list_table) . ' '
                . 'SET column_list = \'' . PMA_Util::sqlAddSlashes(json_encode($sync)) . '\' '
                . 'WHERE db_name = \'' . $db . '\';';
    }
    if (!$GLOBALS['dbi']->tryQuery($query, $GLOBALS['controllink'])) {
        $message = PMA_Message::error(__('Could not remove columns!'));
        $message->addMessage('<br /><br />');
        $message->addMessage(
            PMA_Message::rawError(
                $GLOBALS['dbi']->getError($GLOBALS['controllink'])
            )
        );
        return $message;
    }
    return true;
}
?>