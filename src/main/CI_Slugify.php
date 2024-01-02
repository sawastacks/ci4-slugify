<?php

namespace Mberecall\Sluggable;

/**
 * A simple unique slugs generator for Codeigniter 4
 * Copyright (c) 2023 - present
 * author: MB'DUSENGE Callixte - irebe.library.rw@gmail.com
 * web : github.com/mberecall
 * Initial version created on: 23/09/2023
 * MIT license: https://github.com/mberecall/ci4-slugify/blob/master/LICENSE
 */

use CodeIgniter\Model;



class CI_Slugify
{
    /** @var string */
    protected $model;

    /** @var string */
    protected $slugField = 'slug';

    /** @var array */
    protected static $latin = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ç', 'ü', 'à', 'è', 'ì', 'ò', 'ù', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ç', 'Ü', 'À', 'È', 'Ì', 'Ò', 'Ù');

    /** @var array */
    protected static $plain = array('a', 'e', 'i', 'o', 'u', 'n', 'c', 'u', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'C', 'U', 'A', 'E', 'I', 'O', 'U');

    public function __construct(Model $model)
    {
        helper('text');
        $this->model = $model;
    }

    /**
     * Return the name of table field.
     *
     * @param string $fieldName Name of the table field
     * @return object
     */
    public function field(string $fieldName): void
    {
        $this->slugField = $fieldName;
    }

    /**
     * Return the final result of generating unique slug
     * 
     * @param array $data Parameters
     * @param string $source Name of the source field on table
     * @param string $separator Symbol needed to be divider. eg: '-', '_'
     * @return string
     */

    public function getSlug(array $data, string $source, string $separator = '-'): array
    {
        if (!isset($data['data'][$source])) {
            return $data;
        }

        $currentId = $data['id'][0] ?? -1;

        $clearStr = self::latinToPlain($data['data'][$source]);

        $slug = self::slugifiying($clearStr, $separator, true);

        $entry = $this->model->where($this->slugField, $slug)->withDeleted()->first();

        while (null !== $entry && array_key_exists('id', $entry) && $entry['id'] != $currentId) {
            $slug = \increment_string($slug, $separator, 2);
            // $slug = \increment_string($slug, '-', 2);
            $entry = $this->model->where($this->slugField, $slug)->withDeleted()->first();
        }

        $data['data'][$this->slugField] = $slug;

        return $data;
    }

    /**
     * Change the latin characters to plain characters
     * 
     * @param string $string Given title or String value
     * @param string $separator Divider symbol. eg: '-' or '_'
     * @param bool $lowercaseEnabled Allow generated slug to be in lowercase
     * @return string
     */
    public static function slugifiying(string $string, string $separator, bool $lowercaseEnabled = true)
    {
        return \url_title($string, $separator, $lowercaseEnabled);
    }

     /**
     * Change the latin characters to plain characters
     * 
     * @param string $string
     * @return string
     */
    public static function latinToPlain($string)
    {
        return str_replace(self::$latin, self::$plain, $string);
    }
}
