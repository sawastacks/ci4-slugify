<p align="center">

<img src="https://img.shields.io/packagist/v/mberecall/ci4-slugify" alt="Latest Stable Version">  <img alt="GitHub code size in bytes" src="https://img.shields.io/github/languages/code-size/mberecall/ci4-slugify"> <a href="https://packagist.org/packages/mberecall/ci4-slugify"><img src="https://img.shields.io/packagist/dt/mberecall/ci4-slugify" alt="Total Downloads"></a>  [![Package License](https://img.shields.io/badge/License-MIT-brightgreen.svg)](LICENSE) [![Buy me a coffee](https://img.shields.io/static/v1.svg?label=Buy%20me%20a%20coffee&message=💓&color=434b57&logo=buy%20me%20a%20coffee&logoColor=white&labelColor=13C3FF)](https://www.buymeacoffee.com/mberecall)


</p>

Contents
-------- 
* [What is a slug?](#background-what-is-a-slug)
* [Installation](#installation)
* [Usage](#usage )
  

# Simple Slugify Library for Codeigniter 4

This library provides a way of generating a unique slugs.

<p align="centerx">  
 <a href="https://www.buymeacoffee.com/mberecall" target="_blank">
   <img src="img/bmc.png" alt="drawing" style="width:200px;"/>
 </a>
</p>

## Background: What is a slug?
A slug is a simplified version of a string, typically URL-friendly. The act of "slugging" 
a string usually involves converting it to one case, and removing any non-URL-friendly 
characters (spaces, accented letters, ampersands, etc.). The resulting string can 
then be used as an identifier for a particular resource.

For example, if you have a blog with posts, you could refer to each post via the ID:

    http://yourdomain.com/post/1
    http://yourdomain.com/post/2

... but that's not particularly friendly (especially for 
[SEO](http://en.wikipedia.org/wiki/Search_engine_optimization)). You probably would 
prefer to use the post's title in the URL, but that becomes a problem if your post 
is titled "André & François took 55% of the whole space in REAC Company", because this is pretty ugly too:

  http://yourdomain.com/post/Andr%C3%A9%20&%20Fran%C3%A7ois%20took%2055%%20of%20the%20whole%20space%20in%20REAC%20Company

The solution is to create a slug from the title and use that instead. You might want 
to use Codeigniter's built-in `\url_title('This is the title','-', true)` helper function to convert that title/string into slug as follow:

    http://yourdomain.com/post/andre-francois-took-55-of-the-whole-space-in-reac-company


A URL like that will make users happier (it's readable, easier to type, etc.).

For more information, you might want to read 
[this](http://en.wikipedia.org/wiki/Slug_(web_publishing)#Slug) description on Wikipedia.

Slugs tend to be unique as well. So if you write another post with the same title, 
you'd want to distinguish between them somehow, typically with an incremental counter 
added to the end of the slug:

    http://yourdomain.com/post/andre-francois-took-55-of-the-whole-space-in-reac-company
    http://yourdomain.com/post/andre-francois-took-55-of-the-whole-space-in-reac-company-1
    http://yourdomain.com/post/andre-francois-took-55-of-the-whole-space-in-reac-company-2

This keeps the URLs unique.

## Installation

This package can be installed through `composer require`. Before install this, make sure that your are working with PHP >= 7.2 in your system.
Just run the following command in your cmd or terminal:

> Install the package via Composer:

```bash
composer require mberecall/ci4-slugify
```


## Usage

After package installed in your Codeigniter project, you have now many ways you will use to generate a unique slugs for your blog posts or products. Just follow the following guides below that will teach the way you will use SlugService Class inside your controller.


### SlugService Class 
All the logic to generate slugs is handled
by the `Mberecall\CI_Slugify\SlugService` class.

Generally, you don't need to access this class directly, although there is one 
static method that can be used to generate a slug for a given string without actually
creating or saving an associated model. All you need to do, is to use the below strategies in your controller in order to generate a unique slugs.

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use \Mberecall\CI_Slugify\SlugService;

class TestController extends BaseController
{
    public function index(){
        $post = new Post();
        $title = 'André & François won mathematics competion';
        $slug = SlugService::table('posts')->make($title); //When you use table name
        $slug = SlugService::model(Post::class)->make($title); //When you use model object
        //Result for $slug: andre-francois-won-mathematics-competion-3
        $post->save([
            'title'=>$title,
            'slug'=>$slug 
        ]);
    }   
}
```

Below are possible examples you can use to generate unique slug in your controller using SlugService class:

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use \Mberecall\CI_Slugify\SlugService;

class TestController extends BaseController
{
    public function index(){

        $post = new Post();
        $title = 'André & François won mathematics competion';

        /** Default way. Both lines will return same result */
        $slug = SlugService::table('posts')->make($title);
        $slug = SlugService::model(Post::class)->make($title);

        /** When you specify slug field name. defaul is 'slug' */
        $slug = SlugService::table('posts')->make($title,'post_slug');
        $slug = SlugService::model(Post::class)->make($title,'post_slug');

        /** When you specify divider/separator for generated slug. default is '-' */
        $slug = SlugService::table('posts','id')->make($title);
        $slug = SlugService::model(Post::class,'id')->make($title);
    
       /** When you specify divider/separator for generated slug. default is '-' */
        $slug = SlugService::table('posts')->separator('_')->make($title);
        $slug = SlugService::model(Post::class)->separator('_')->make($title);
            
        //Result for $slug: 1: andre-francois-won-mathematics-competion
        //Result for $slug: 2: andre_francois_won_mathematics_competion   
    }   
}

```

### Updating record

when you are not inserting new record, but you're updating any existing record, you'll need to add an extra function on chain which is  `sid()`. Below is an example of how you can generate new slug of selected record.
```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use \Mberecall\CI_Slugify\SlugService;

class TestController extends BaseController
{
    public function update_post(){
        $id = $request->getVar('post_id');
        $post = new Post();
        $title = 'André & François won mathematics competion';

        $slug = SlugService::model(Post::class)->sid($id)->make($title);
  
    }

}

```

## Class reference

### `\Mberecall\CI_Slugify\SlugService`

#### `table()` 

**Arguments**
- This method has two arguments. First argument will be the name of table which is required. Where the second argument is the primary key of the modal table. eg: `model('products','pid')`. Default value of the second argument which is primary key is 'id'. This means that we suppose that the products table has id column/field in structure.

#### `model()` 

**Arguments**
- This method has two arguments. First argument will be the instance of the model object which is required. Where the second argument is the primary key of the modal table. eg: `model(Product::class,'pid')`. Default value of the second argument which is primary key is 'id'. This means that we suppose that the products table has id column/field in structure.

#### `sid()` 

**Arguments**
- This method has one argument. This argument is required and must be an integer. eg: `sid(12)`. This number is the id of selected row.

#### `separator()` 

**Arguments**
- This method has one argument. Here, you can specify the devider that will be included in generated slug. eg: `separator('_')`. The default value is dash `'-'`.

#### `make()` 

**Arguments**
- This method has two arguments. First argument is the string value from title which is required. Second argument is the slug field name. You can specify the name of field name in this method. The default value of second argument is `'slug'`. eg: `make('Title Of Your Choice','slug')`

## Copyright and License

[ci4-slugify](https://github.com/mberecall/ci4-slugify)
was written by [MB'DUSENGE Callixte (mberecall)](https://github.com/mberecall) and is released under the 
[MIT License](LICENSE.md).

Copyright (c) 2023 MB'DUSENGE Callixte🇷🇼
