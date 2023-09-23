<?php
namespace Mberecall\CI_Slugify;
/**
 * A simple unique slugs generator for Codeigniter 4
 * Copyright (c) 2023 - present
 * author: MB'DUSENGE Callixte - irebe.library.rw@gmail.com
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
     * Return the final result of generating unique slug
     *
     * @param string $string String value from title or other text
     * @param string $field field name of the given table
     * @param int $id ID of the row if you are updating
     * @return string
     */

    public static function make(string $string, string $field = 'slug', int $id = null)
    {

        if (!$string || $string == null) {
            throw new \Exception('Defining string on make() is required');
        }

        if (!$field || $field == null) {
            throw new \Exception('Defining field on make() is required');
        }

        if (!self::$model && !self::$table) {
            throw new \Exception(' "table()" or "model()" on chain is required');
        }

        if (self::$model && self::$table) {
            throw new \Exception('Only on function allowed. Choose "table()" or "model()"');
        }

        $_separator = ( self::$separator && is_string(self::$separator)) ? self::$separator : '-';
        $slug = self::latinToPlain($string);

        $db = \Config\Database::connect();
        $_table_model = null;
        $slug = url_title($slug, $_separator, true);
        $slug = strtolower($slug);
        $params = array();
        $params[$field] = $slug;
        $prm_ky = self::$primaryKey;

        if ($id) $params["$prm_ky !="] = $id;

        if (self::$table) $_table_model = $db->table(self::$table);

        if ( self::$model && is_string(self::$model)) $_table_model = new self::$model;

        return self::setSlug($_table_model, $params, $slug, $field, $_separator);
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

    public static function setSlug(object $table_model, array $params, string $slug, string $field, string $separator)
    {
        $i = 0;
        while ($table_model->where($params)->countAllResults()) {
            if (!preg_match('/-{1}[0-9]+$/', $slug))
                // $slug .= '-' . ++$i;
                $slug .= $separator . ++$i;
            else
                $slug = preg_replace('/[0-9]+$/', ++$i, $slug);

            $params[$field] = $slug;
        }
        return $slug;
    }

    /**
     * Defining the separator symbol 
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
    public static function latinToPlain(string $string)
    {
        return str_replace(self::$latin, self::$plain, $string);
    }
}
