<?php

namespace App\Models;

use App\Exceptions\CreateObjectException;
use App\Exceptions\ObjectNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class GenericModel extends Model
{
    use HasFactory;

    public $validation_rules = [];

    /**
     * Fetch object by ID with caching and authorization check.
     */
    public static function fetchByIdWithCacheAndAuth($id)
    {
        $class = get_called_class();
        $key = 'model_cache:' . $class . ':' . $id;

        // Use Redis caching to retrieve the object
        $object = Cache::remember($key, env('CACHE_EXPIRY', 600), function () use ($id) {
            return self::find($id);
        });

        if ($object === null) {
            throw new ObjectNotFoundException($class . ' object not found');
        }

        $trace = debug_backtrace();
        $policy = Gate::getPolicyFor($class);

        if ($policy && method_exists($policy, $trace[1]['function'])
            && !auth()->user()->can($trace[1]['function'], $object)) {
            throw new \Exception("Unauthorized access");
        }

        return $object;
    }

    /**
     * Update the model with conditions, invalidate the cache if successful.
     */
    public function updateWithConditions($data = [], $conditions = [])
    {
        $class = get_called_class();
        $id = $this->id;
        $key = 'model_cache:' . $class . ':' . $id;

        Cache::forget($key);

        $res = $this->where($conditions + ['id' => $this->id])->update($data);

        if ($res < 1) {
            throw new ObjectNotFoundException($class . ' object not found');
        }

        return $res;
    }

    /**
     * Delete the model and clear cache.
     */
    public function deleteWithCache()
    {
        self::removeCacheForIds([$this->id]);

        $res = $this->delete();

        if ($res < 1) {
            throw new ObjectNotFoundException(get_called_class() . ' object not found');
        }

        return $res;
    }

    /**
     * Remove cache entries for an array of IDs.
     */
    public static function removeCacheForIds($id_array)
    {
        $class = get_called_class();
        foreach ($id_array as $id) {
            $key = 'model_cache:' . $class . ':' . $id;
            Cache::forget($key);
        }
    }

    /**
     * Create a new instance with validation and caching.
     */
    public static function createNewWithValidation($parameters)
    {
        $class_name = get_called_class();
        $class = new $class_name();
        $validation_rules = $class->validation_rules;

        $validator = Validator::make($parameters, $validation_rules);

        if (!$validator->fails()) {
            $obj = $class::create($parameters);
            $key = 'model_cache:' . $class_name . ':' . $obj->id;
            Cache::put($key, $obj, env('CACHE_EXPIRY', 600));
            return $obj;
        } else {
            throw new CreateObjectException($validator->errors()->first());
        }
    }
}
