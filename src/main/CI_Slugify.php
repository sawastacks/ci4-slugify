<?php

namespace Mberecall\Sluggable;

use CodeIgniter\Model;



class CI_Slugify
{
    protected $model;
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

    public function setField(string $fieldName): void
    {
        $this->slugField = $fieldName;
    }

    public function getSlug(array $data, string $field, string $separator = '-'): array
    {
        if (!isset($data['data'][$field])) {
            return $data;
        }

        $currentId = $data['id'][0] ?? -1;

        $clearStr = self::latinToPlain($data['data'][$field]);

        // $slug = \url_title($data['data'][$field], '-', true);
        $slug = \url_title($clearStr, $separator , true);

        


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
     * @param string $string
     * @return string
     */
    public static function latinToPlain($string)
    {
        return str_replace(self::$latin, self::$plain, $string);
    }
}
