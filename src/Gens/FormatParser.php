<?php
/**
 * FormatParser{
 * 配置文件，数据库字段定义的格式解析
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan
 * @license MIT
 */
namespace Dbfiller\Gens;
use Exception;

class FormatParser{
    const FLAG_UNIQ=     '#';
    const FLAG_NORM=     '%';

    const FLAG_STR=      's';
    const FLAG_FL=       'f';
    const FLAG_UFL=      'p';
    const FLAG_INT=      'd';
    const FLAG_UINT=     'u';
    
    const DB_BIT=        'bit';
    const DB_INT=        'int';
    const DB_TINYINT=    'tinyint';
    const DB_SMALLINT=   'smallint';
    const DB_BIGINT=     'bigint';
    const DB_MEDIUMINT=  'mediumint';
    const DB_TINYBLOB=   'tinyblob';
    const DB_BLOB=       'blob';
    const DB_LONGBLOB=   'longblob';
    const DB_MEDIUMBLOB= 'mediumblob';
    const DB_BINARY=     'binary';
    const DB_VARBINARY=  'varbinary';
    const DB_TEXT=       'text';
    const DB_LONGTEXT=   'longtext';
    const DB_MEDIUMTEXT= 'mediumtext';
    const DB_VARCHAR=    'varchar';
    const DB_CHAR=       'char';
    const DB_ENUM=       'enum';
    const DB_SET=        'set';
    const DB_DATETIME=   'datetime';
    const DB_TIME=       'time';
    const DB_DATE=       'date';
    const DB_YEAR=       'year';
    const DB_TIMESTAMP=  'timestamp';
    const DB_FLOAT=      'float';
    const DB_DOUBLE=     'double';
    const DB_DECIMAL=    'decimal';

    const DATA_SOURCE=   'datasource';


    public  $param;
    public  $unsigned;
    public  $name;
    public  $unique;
    public  $type;
    private $flag;
    public  $table;
    public  $field;
    public  $datasource;

    /**
     * formatPattern 
     * 解析配置文件中的Format定义
     * @static
     * @access public
     * @return void
     */
    public static function formatPattern()
    {
        return  "/(".FormatParser::FLAG_UNIQ."|".FormatParser::FLAG_NORM.
                ")([0-9]+)(\-[0-9]+)?([".
                FormatParser::FLAG_UINT.
                FormatParser::FLAG_STR.
                FormatParser::FLAG_FL.
                FormatParser::FLAG_INT.
                FormatParser::FLAG_UFL."]{1})/i";
    }
    /**
     * typePattern 
     * 解析数据库定义的column type定义
     * @static
     * @access public
     * @return void
     */
    public static function typePattern()
    {
        return '/([a-z]+)(\([^\)]+\))?( unsigned)?/i';
    }

    /**
     * initWithField 
     * 使用字段的定义初始化解析器
     * @param mixed $field 
     * @param mixed $matches 
     * @access public
     * @return void
     */
    public function initWithField($field,$matches)
    {
        $this->type=$matches[1];
        $this->param='';
        $this->unsigned=false;
        if(isset($matches[2])){
            $this->param=trim($matches[2],",()");
        }
        if( 
            (isset($matches[3]) && 0==strcasecmp(trim($matches[3]),'unsigned')) ||
            (isset($matches[4]) && 0==strcasecmp(trim($matches[4]),'unsigned')) 
        ){
            $this->unsigned=true;
        }
        $this->name=$field->Field;
    }

    /**
     * initWithFormat 
     * 使用Format定义初始化
     * @param mixed $name 
     * @param mixed $matches 
     * @access public
     * @return void
     */
    public function initWithFormat($name,$matches)
    {
        if($matches[1]==FormatParser::FLAG_UNIQ){
            $this->unique=true;
        }
        if($matches[1]==FormatParser::FLAG_NORM){
            $this->unique=false;
        }
        if($matches[3]){
            $this->param=sprintf("%d,%d",$matches[2],abs($matches[3]));
        }
        else{
            $this->param=sprintf("%d,%d",$matches[2],abs($matches[2]));
        }
        $this->name=$name;
        $this->flag=$matches[4];
        $this->unsigned=true;
        switch($this->flag){
        case FormatParser::FLAG_STR:
            $this->type='char';
            break;
        case FormatParser::FLAG_INT:
            $this->unsigned=false;
            $this->type='int';
        case FormatParser::FLAG_UINT:
            $this->type='int';
            $this->unsigned=true;
            break;
        case FormatParser::FLAG_FL:
            $this->unsigned=false;
            $this->type='float';
            break;
        case FormatParser::FLAG_UFL:
            $this->unsigned=true;
            $this->type='float';
        } 
    }

    /**
     * initWithDatasource 
     * 解析数据中的数据源
     * @param mixed $from 
     * @param mixed $clouser 
     * @access public
     * @return void
     */
    public function initWithDatasource($name,$from,$clouser)
    {
        switch($from[0]){
            case FormatParser::FLAG_NORM:
            $this->unique=false;
            break;
            case FormatParser::FLAG_UNIQ:
            $this->unique=true;
            break;
        } 
        $this->datasource=$clouser;
        $this->type='datasource';
        $this->table=$from[1];
        $this->field=$from[2];
        $this->name=$name;
    }
}

