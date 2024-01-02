<?php

namespace Mberecall\CI_Slugify;

/**
 * A simple unique slugs generator for Codeigniter 4
 * Copyright (c) 2023 - present
 * author: MB'DUSENGE Callixte
 * web : github.com/mberecall
 * Initial version created on: 23/09/2023
 * MIT license: https://github.com/mberecall/ci4-slugify/blob/master/LICENSE
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

        if ($id) $params["$prm_ky !="] = $id;

        if (self::$table) $_table_model = $db->table(self::$table);

        if ( self::$model && is_string(self::$model) ) $_table_model = new self::$model;

        $sid = ( self::$updateTo && is_int(self::$updateTo) ) ? self::$updateTo : '';
         
        return self::setSlug($_table_model, $params, $slug, $field, $_separator,$sid);
        
    }

    /**
     * Return the result of generated slug if the similar
     * founded in table
     *
     * @param object $table_model Model object or table name
     * @param array $params Parameters
     * @param string $slug Generated slug
     * @param string $field Table field
     * @param string $separator Symbol needed to be used eg: '-' or '_'
     * @return string
     */

     private static function setSlug(object $table_model, array $params, string $slug, string $field, string $separator, $id = '')
    {
        return self::check_uri($slug, $id, $count = 0, $separator, $table_model, $params, $field);
    }

    private static function check_uri($slug, $id = FALSE, $count = 0, $separator, $table_model, $params, $field)
	{ 
		$new_slug = ($count > 0) ? $slug . $separator . $count : $slug;
        $pk = self::$primaryKey;
 
        $query = $table_model->where($field,$new_slug);

		if( $id != null && is_int($id) ){
            $query->where($pk . ' !=', $id);
		}

        if( $query->countAllResults() > 0 ){
            return self::check_uri($slug, $id, ++$count, $separator, $table_model, $params, $field);
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

     
    /**
     * sid
     *
     * @param  mixed $id
     * @return void Return spacific column id
     */
    public static function sid($id){
        static::$updateTo = $id;
        return static::getself();
    }   
}