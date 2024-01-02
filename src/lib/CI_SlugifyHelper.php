<?php

namespace Mberecall\CI_Slugify;

/**
 * A simple unique slugs generator for Codeigniter 4
 * Copyright (c) 2023 - present
 * author: MB'DUSENGE Callixte
 * web : github.com/mberecall
 * Initial version created on: 23/09/2023
 * MIT license: https://github.com/mberecall/ci4-slugify/blob/master/LICENSE
 * 
 */

class SlugService
{
    /** @var string */
    protected static $table;

     /** @var string */
    protected static $primaryKey;

    /** @var string */
    protected static $separator;

    /** @var string */
    protected static $model;

     /** @var string */
    protected static $onlyInstance;

     /** @var int */
     protected static $updateTo;

    /** @var array */
    protected static $latin = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ç', 'ü', 'à', 'è', 'ì', 'ò', 'ù', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ç', 'Ü', 'À', 'È', 'Ì', 'Ò', 'Ù');

    /** @var array */
    protected static $plain = array('a', 'e', 'i', 'o', 'u', 'n', 'c', 'u', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'C', 'U', 'A', 'E', 'I', 'O', 'U');

    public function __construct()
    {
        helper(['text', 'url']);
    }

    protected static function getself()
    {
        if (static::$onlyInstance === null) {
            static::$onlyInstance = new SlugService;
        }
        return static::$onlyInstance;
    }

    /**
     * Return the name of table on the chain.
     *
     * @param string $name Name of the table
     * @param string $primaryKey Primary key of the table
     * @return object
     */
    public static function table(string $name, string $primaryKey = 'id')
    {
        static::$table = $name;
        static::$primaryKey = $primaryKey;
        return static::getself();
    }

    /**
     * Return an object of the Model
     *
     * @param string $name instance of the model
     * @param string $primaryKey of the model table
     * @return object
     */
    
    public static function model(string $name, string $primaryKey = 'id')
    {
        static::$model = $name;
        static::$primaryKey = $primaryKey;
        return static::getself();
    }

     /**
     * Return final result of generating unique slug
     *
     * @param string $string String value from title or other text
     * @param string $field field name of the given table
     * @param int $id ID of the row if you are updating
     * @return string
     */

    public static function make(string $string, string $field = 'slug', int $id = null)
    {

        if ( !$string || $string == null ) {
            throw new \Exception('Defining string on make() is required');
        }

        if ( !$field || $field == null ) {
            throw new \Exception('Defining field on make() is required');
        }

        if ( !self::$model && !self::$table ) {
            throw new \Exception(' "table()" or "model()" method on chain is required');
        }

        if ( self::$model && self::$table ) {
            throw new \Exception('Only one function allowed on the chain. Choose "table()" or "model()"');
        }
        $db = \Config\Database::connect();
        $_separator = ( self::$separator && is_string(self::$separator) ) ? self::$separator : '-';
        $slug = self::latinToPlain($string);
       
        $_table_model = null;
        $slug = strtolower(url_title(convert_accented_characters($slug), $_separator));
		$slug =  reduce_multiples($slug,$_separator, TRUE);

        $params = array();
        $params[$field] = $slug;
        $prm_ky = self::$primaryKey;

        if (self::$table) $_table_model = $db->table(self::$table);

        if ( self::$model && is_string(self::$model) ) $_table_model = new self::$model;

        $sid = ( self::$updateTo && is_int(self::$updateTo) ) ? self::$updateTo : '';
     
        return self::check_slug($_table_model, $field, $slug, $params, $_separator, $sid);
    }

    private static function check_slug($model, $field, $slug, $params, $separator, $id, $count = 0){
        
        $new_slug = ($count > 0) ? $slug . $separator . $count : $slug;
        $pk = self::$primaryKey;
        $query = $model->where($field,$new_slug);

        if( $id != null && is_int($id) ){
            $query->where($pk . '!=', $id);
		}
        if( $query->countAllResults() > 0 ){
            return self::check_slug($model, $field, $slug, $params, $separator, $id, ++$count);
        }else{
            return $new_slug;
        }
    }

    /**
     * Defining the separator/divider symbol 
     * Example: '-', '_' .The default symbol is '-'. 
     * 
     * @param string $string
     * @return object
     */ 
    public static function separator(string $string)
    {
        static::$separator = $string;
        return static::getself();
    }

    /**
     * Change the latin characters to plain characters
     * 
     * @param string $string
     * @return string
     */ 
    private static function latinToPlain(string $string)
    {
        return str_replace(self::$latin, self::$plain, $string);
    }

     
    public static function sid(int $id){
        static::$updateTo = $id;
        return static::getself();
    }   
}