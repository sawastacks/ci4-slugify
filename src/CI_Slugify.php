<?php

namespace Mberecall\CI_Libraries;

use CodeIgniter\Model;

class CI_Slugify
{
    protected $model;
    protected $slugField = 'slug';

    public function __construct(Model $model)
    {
        helper('text');
        $this->model = $model;
    }

    public function setField(string $fieldName): void
    {
        $this->slugField = $fieldName;
    }

    public function getSlug(array $data, string $field): array
    {
        if (!isset($data['data'][$field])) {
            return $data;
        }

        $currentId = $data['id'][0] ?? -1;

        $slug = \url_title($data['data'][$field], '-', true);
        $entry = $this->model->where($this->slugField, $slug)->withDeleted()->first();

        while (null !== $entry && array_key_exists('id', $entry) && $entry['id'] != $currentId) {
            $slug = \increment_string($slug, '-', 2);
            $entry = $this->model->where($this->slugField, $slug)->withDeleted()->first();
        }

        $data['data'][$this->slugField] = $slug;

        return $data;
    }
}
