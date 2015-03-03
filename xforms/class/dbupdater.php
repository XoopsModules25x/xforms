<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team, Marcan
 */

/**
 * XformsTable class
 *
 * Information about an individual table
 *
 * @package WfDownloads
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
if (!defined("XOOPS_ROOT_PATH")) {
    die("XOOPS root path not defined");
}

/**
 * Class XformsTable
 */
class XformsTable
{

    /**
     * @var string $_name name of the table
     */
    protected $name;

    /**
     * @var string $structure structure of the table
     */
    protected $structure;

    /**
     * @var array $data containing valued of each records to be added
     */
    protected $data;

    /**
     * @var array $alteredFields containing fields to be altered
     */
    protected $alteredFields;

    /**
     * @var array $newFields containing new fields to be added
     */
    protected $newFields;

    /**
     * @var array $dropedFields containing fields to be droped
     */
    protected $dropedFields;

    /**
     * @var array $flagForDrop flag table to drop it
     */
    protected $flagForDrop = false;

    /**
     * @var array $updatedFields containing fields which values will be updated
     */
    protected $updatedFields;

    /**
     * @var array $updatedFields containing fields which values will be updated
     */ //felix
    protected $updatedWhere;

    /**
     * Constructor
     *
     * @param string $name name of the table
     *
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->data = array();
    }

    /**
     * Return the table name, prefixed with site table prefix
     *
     * @return string table name
     *
     */
    public function name()
    {
        global $xoopsDB;

        return $xoopsDB->prefix($this->name);
    }

    /**
     * Set the table structure
     *
     * @param string $structure table structure
     *
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    /**
     * Return the table structure
     *
     * @return string table structure
     *
     */
    public function getStructure()
    {
        return sprintf($this->structure, $this->name());
    }

    /**
     * Add values of a record to be added
     *
     * @param string $data values of a record
     *
     */
    public function setData($data)
    {
        $this->data[] = $data;
    }

    /**
     * Get the data array
     *
     * @return array containing the records values to be added
     *
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Use to insert data in a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function addData()
    {
        global $xoopsDB;

        foreach ($this->getData() as $data) {
            $query = sprintf('INSERT INTO %s VALUES (%s)', $this->name(), $data);
            $ret   = $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_ADD_DATA_ERR, $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_ADD_DATA, $this->name()) . "</li>";
            }
        }

        return $ret;

    }

    /**
     * Add a field to be added
     *
     * @param string $name       name of the field
     * @param string $properties properties of the field
     *
     */
    public function addAlteredField($name, $properties)
    {
        $field['name']          = $name;
        $field['properties']    = $properties;
        $this->alteredFields[] = $field;
    }

    /**
     * Invert values 0 to 1 and 1 to 0
     *
     * @param string $name     name of the field
     * @param        $newValue
     * @param        $oldValue
     *
     * @internal param string $old old propertie
     * @internal param string $new new propertie
     */ //felix
    public function addUpdatedWhere($name, $newValue, $oldValue)
    {
        $field['name']         = $name;
        $field['value']        = $newValue;
        $field['where']        = $oldValue;
        $this->updatedWhere[] = $field;
    }

    /**
     * Add new field of a record to be added
     *
     * @param string $name       name of the field
     * @param string $properties properties of the field
     *
     */
    public function addNewField($name, $properties)
    {
        $field['name']       = $name;
        $field['properties'] = $properties;
        $this->newFields[]  = $field;
    }

    /**
     * Get fields that need to be altered
     *
     * @return array fields that need to be altered
     *
     */
    public function getAlteredFields()
    {
        return $this->alteredFields;
    }

    /**
     * Add field for which the value will be updated
     *
     * @param string $name  name of the field
     * @param string $value value to be set
     *
     */
    public function addUpdatedField($name, $value)
    {
        $field['name']          = $name;
        $field['value']         = $value;
        $this->updatedFields[] = $field;
    }

    /**
     * Get new fields to be added
     *
     * @return array fields to be added
     *
     */
    public function getNewFields()
    {
        return $this->newFields;
    }

    /**
     * Get fields which values need to be updated
     *
     * @return array fields which values need to be updated
     *
     */
    public function getUpdatedFields()
    {
        return $this->updatedFields;
    }

    /**
     * Get fields which values need to be updated
     *
     * @return array fields which values need to be updated
     *
     */ //felix
    public function getUpdatedWhere()
    {
        return $this->updatedWhere;
    }

    /**
     * Add values of a record to be added
     *
     * @param string $name name of the field
     *
     */
    public function addDropedField($name)
    {
        $this->dropedFields[] = $name;
    }

    /**
     * Get fields that need to be droped
     *
     * @return array fields that need to be droped
     *
     */
    public function getDropedFields()
    {
        return $this->dropedFields;
    }

    /**
     * Set the flag to drop the table
     *
     */
    public function setFlagForDrop()
    {
        $this->flagForDrop = true;
    }

    /**
     * Use to create a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function createTable()
    {
        global $xoopsDB;

        $query = $this->getStructure();

        $ret = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_CREATE_TABLE_ERR, $this->name()) . "</li>";
        } else {
            echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_CREATE_TABLE, $this->name()) . "</li>";
        }

        return $ret;
    }

    /**
     * Use to drop a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function dropTable()
    {
        global $xoopsDB;

        $query = sprintf("DROP TABLE %s", $this->name());
        $ret   = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_DROP_TABLE_ERR, $this->name()) . "</li>";

            return false;
        } else {
            echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_DROP_TABLE, $this->name()) . "</li>";

            return true;
        }
    }

    /**
     * Use to alter a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function alterTable()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getAlteredFields() as $alteredField) {
            $query = sprintf("ALTER TABLE `%s` CHANGE `%s` %s", $this->name(), $alteredField['name'], $alteredField['properties']);
            //echo $query;
            $ret = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_CHGFIELD_ERR, $alteredField['name'], $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_CHGFIELD, $alteredField['name'], $this->name()) . "</li>";
            }
        }

        return $ret;
    }

    /**
     * Use to add new fileds in the table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function addNewFields()
    {
        global $xoopsDB;

        $ret = true;
        foreach ($this->getNewFields() as $newField) {
            $query = sprintf("ALTER TABLE `%s` ADD `%s` %s", $this->name(), $newField['name'], $newField['properties']);
            //echo $query;
            $ret = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_NEWFIELD_ERR, $newField['name'], $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_NEWFIELD, $newField['name'], $this->name()) . "</li>";
            }
        }

        return $ret;
    }

    /**
     * Use to update fields values
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function updateFieldsValues()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getUpdatedFields() as $updatedField) {
            $query = sprintf("UPDATE %s SET %s = %s", $this->name(), $updatedField['name'], $updatedField['value']);
            $ret   = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_UPDATE_TABLE_ERR, $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_UPDATE_TABLE, $this->name()) . "</li>";
            }
        }

        return $ret;
    }
    /**
     * Use to update fields values
     *
     * @return bool true if success, false if an error occured
     *
     */ //felix
    public function updateWhereValues()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getUpdatedWhere() as $updatedWhere) {
            $query = sprintf("UPDATE %s SET %s = %s WHERE %s  %s", $this->name(), $updatedWhere['name'], $updatedWhere['value'], $updatedWhere['name'], $updatedWhere['where']);
            //echo $query."<br>";
            $ret = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_UPDATE_TABLE_ERR, $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_UPDATE_TABLE, $this->name()) . "</li>";
            }
        }

        return $ret;
    }

    /**
     * Use to drop fields
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function dropFields()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getdropedFields() as $dropedField) {
            $query = sprintf("ALTER TABLE %s DROP %s", $this->name(), $dropedField);

            $ret = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_DROPFIELD_ERR, $dropedField, $this->name()) . "</li>";
            } else {
                echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_DROPFIELD, $dropedField, $this->name()) . "</li>";
            }
        }

        return $ret;
    }
}

/**
 * xFormsDbupdater class
 *
 * Class performing the database update for the module
 *
 * @package xForms
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class XformsDbupdater
{

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * Use to execute a general query
     *
     * @param string $query   query that will be executed
     * @param string $goodmsg message displayed on success
     * @param string $badmsg  message displayed on error
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function runQuery($query, $goodmsg, $badmsg)
    {
        global $xoopsDB;
        $ret = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>$badmsg</li>";

            return false;
        } else {
            echo "<li class='ok'>$goodmsg</li>";

            return true;
        }
    }

    /**
     * Use to rename a table
     *
     * @param string $from name of the table to rename
     * @param string $to   new name of the renamed table
     *
     * @return bool true if success, false if an error occured
     */
    public function renameTable($from, $to)
    {
        global $xoopsDB;

        $from = $xoopsDB->prefix($from);
        $to   = $xoopsDB->prefix($to);

        $query = sprintf("ALTER TABLE %s RENAME %s", $from, $to);
        $ret   = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>" . sprintf(_AM_XFORMS_DB_MSG_RENAME_TABLE_ERR, $from) . "</li>";

            return false;
        } else {
            echo "<li class='ok'>" . sprintf(_AM_XFORMS_DB_MSG_RENAME_TABLE, $from, $to) . "</li>";

            return true;
        }
    }

    /**
     * Use to update a table
     *
     * @param object $table {@link xFormsTable} that will be updated
     *
     * @see xFormsTable
     *
     * @return bool true if success, false if an error occured
     */
    public function updateTable($table)
    {
        global $xoopsDB;

        $ret = true;
        echo "<ul>";

        // If table has a structure, create the table
        if ($table->getStructure()) {
            $ret = $table->createTable() && $ret;
        }

        // If table is flag for drop, drop it
        if ($table->flagForDrop) {
            $ret = $table->dropTable() && $ret;
        }

        // If table has data, insert it
        if ($table->getData()) {
            $ret = $table->addData() && $ret;
        }

        // If table has new fields to be added, add them
        if ($table->getNewFields()) {
            $ret = $table->addNewFields() && $ret;
        }

        // If table has altered field, alter the table
        if ($table->getAlteredFields()) {
            $ret = $table->alterTable() && $ret;
        }

        // If table has updated field values, update the table
        if ($table->getUpdatedFields()) {
            $ret = $table->updateFieldsValues($table) && $ret;
        }

        // If table has droped field, alter the table
        if ($table->getDropedFields()) {
            $ret = $table->dropFields($table) && $ret;
        }
        //felix
        // If table has updated field values, update the table
        if ($table->getUpdatedWhere()) {
            $ret = $table->UpdateWhereValues($table) && $ret;
        }

        echo "</ul>";

        return $ret;
    }
}
