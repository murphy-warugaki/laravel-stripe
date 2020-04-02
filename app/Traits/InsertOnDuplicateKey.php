<?php
namespace App\Traits;

trait InsertOnDuplicateKey
{

    public static function insertOnDuplicateKey(array $data, array $updateColumns = null)
    {
        if (empty($data)) {
            return false;
        }

        if (!isset($data[0])) {
            $data = [$data];
        }

        $sql = static::buildInsertOnDuplicateSql($data, $updateColumns);

        $data = static::inLineArray($data);

        return self::getModelConnectionName()->affectingStatement($sql, $data);
    }

    public static function insertIgnore(array $data)
    {
        if (empty($data)) {
            return false;
        }

        // Case where $data is not an array of arrays.
        if (!isset($data[0])) {
            $data = [$data];
        }

        $sql = static::buildInsertIgnoreSql($data);

        $data = static::inLineArray($data);

        return self::getModelConnectionName()->affectingStatement($sql, $data);
    }

    public static function replace(array $data)
    {
        dd('error');
        if (empty($data)) {
            return false;
        }

        // Case where $data is not an array of arrays.
        if (!isset($data[0])) {
            $data = [$data];
        }

        $sql = static::buildReplaceSql($data);

        $data = static::inLineArray($data);

        return self::getModelConnectionName()->affectingStatement($sql, $data);
    }

    public static function getTableName()
    {
        $class = get_called_class();

        return (new $class())->getTable();
    }

    public static function getModelConnectionName()
    {
        $class = get_called_class();

        return (new $class())->getConnection();
    }

    public static function getTablePrefix()
    {
        return self::getModelConnectionName()->getTablePrefix();
    }

    public static function getPrimaryKey()
    {
        $class = get_called_class();

        return (new $class())->getKeyName();
    }

    protected static function buildQuestionMarks($data)
    {
        $row = self::getFirstRow($data);
        $questionMarks = array_fill(0, count($row), '?');

        $line = '(' . implode(',', $questionMarks) . ')';
        $lines = array_fill(0, count($data), $line);

        return implode(', ', $lines);
    }

    protected static function getFirstRow(array $data)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Empty data.');
        }

        list($first) = $data;

        if (!is_array($first)) {
            throw new \InvalidArgumentException('$data is not an array of array.');
        }

        return $first;
    }

    protected static function getColumnList(array $first)
    {
        if (empty($first)) {
            throw new \InvalidArgumentException('Empty array.');
        }

        return '`' . implode('`,`', array_keys($first)) . '`';
    }

    protected static function buildValuesList(array $updatedColumns)
    {
        $out = [];

        foreach ($updatedColumns as $key => $value) {
            if (is_numeric($key)) {
                $out[] = sprintf('`%s` = VALUES(`%s`)', $value, $value);
            } else {
                $out[] = sprintf('%s = %s', $key, $value);
            }
        }

        return implode(', ', $out);
    }

    protected static function inLineArray(array $data)
    {
        return call_user_func_array('array_merge', array_map('array_values', $data));
    }

    protected static function buildInsertOnDuplicateSql(array $data, array $updateColumns = null)
    {
        $first = static::getFirstRow($data);

        $sql  = 'INSERT INTO `' . static::getTablePrefix() . static::getTableName() . '`(' . static::getColumnList($first) . ') VALUES' . PHP_EOL;
        $sql .=  static::buildQuestionMarks($data) . PHP_EOL;
        $sql .= 'ON DUPLICATE KEY UPDATE ';

        if (empty($updateColumns)) {
            $sql .= static::buildValuesList(array_keys($first));
        } else {
            $sql .= static::buildValuesList($updateColumns);
        }

        return $sql;
    }

    protected static function buildInsertIgnoreSql(array $data)
    {
        $first = static::getFirstRow($data);

        $sql  = 'INSERT IGNORE INTO `' . static::getTablePrefix() . static::getTableName() . '`(' . static::getColumnList($first) . ') VALUES' . PHP_EOL;
        $sql .=  static::buildQuestionMarks($data);

        return $sql;
    }

    protected static function buildReplaceSql(array $data)
    {
        $first = static::getFirstRow($data);

        $sql  = 'REPLACE INTO `' . static::getTablePrefix() . static::getTableName() . '`(' . static::getColumnList($first) . ') VALUES' . PHP_EOL;
        $sql .=  static::buildQuestionMarks($data);

        return $sql;
    }
}