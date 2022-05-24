<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 7/24/2018
 * Time: 4:09 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use ReflectionClass;

class BaseModel extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * @param array $options
     * @return array
     * @throws \ReflectionException
     */
    public function transform($options = [])
    {
        $reflect = new ReflectionClass($this);
        $transformer = '\\App\\Transformers\\'.ucfirst($reflect->getShortName()).'Transformer';

        return class_exists($transformer) ? \App::make($transformer)->transform($this, $options) : $this->toArray();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

}
