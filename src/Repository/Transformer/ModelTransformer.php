<?php namespace Salah3id\Domains\Repository\Transformer;

use League\Fractal\TransformerAbstract;
use Salah3id\Domains\Repository\Contracts\Transformable;

/**
 * Class ModelTransformer
 * @package Salah3id\Domains\Repository\Transformer
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ModelTransformer extends TransformerAbstract
{
    public function transform(Transformable $model)
    {
        return $model->transform();
    }
}
