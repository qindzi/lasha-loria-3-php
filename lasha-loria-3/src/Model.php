<?php

namespace StephaneCoinon\Mailtrap;

class Model
{

    protected static $collectionClosure = null;

    protected $attributes;


    protected $model = null;

    public static function boot($client)
    {
        static::$client = $client;
    }

    public static function returnArraysAsLaravelCollections()
    {
        static::$collectionClosure = function ($objects) {
            return collect($objects);
        };
    }


    public static function returnArrays()
    {
        static::$collectionClosure = null;
    }

    public static function getClient()
    {
        return static::$client;
    }


    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function apiUrl($url)
    {
        return 'api/v1/' . $url;
    }


    public function getRaw($uri, $parameters = [], $headers = [])
    {
        return static::$client->get($uri, $parameters, $headers);
    }

    public function get($uri, $parameters = [], $headers = [])
    {
        $response = $this->cast($this->getRaw($uri, $parameters, $headers));

        // Reset model class for next request
        $this->model = null;

        return $response;
    }

    public function patch($uri, $parameters = [], $headers = [])
    {
        $response = $this->cast(static::$client->patch($uri, $parameters, $headers));
        // Reset model class for next request
        $this->model = null;

        return $response;
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    public function cast($data)
    {
        // Cannot cast null
        if (is_null($data)) {
            return null;
        }

        // Cast an array of objects
        if (is_array($data)) {
            // Cast the objects returned by the API into models
            $array = array_map(function ($object) {
                return $this->cast($object);
            }, $data);
            // Return the "collection" of models
            return $this->collect($array);
        }

        // Do we cast to our own model or a different one?
        $model = is_null($this->model) ? static::class : $this->model;

        // Cast a single object
        $instance = new $model((array) $data);

        return $instance;
    }


    public function collect(array $models)
    {
        $closure = static::$collectionClosure;

        // No collection closure defined so return array of models as-is
        if (is_null($closure)) {
            return $models;
        }

        // Call the closure to transform the array into the type of collection
        // defined
        return $closure($models);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }


    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
