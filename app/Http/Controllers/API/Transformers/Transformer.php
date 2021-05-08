<?php namespace App\Http\Controllers\API\Transformers;

abstract class Transformer {
    public function __construct() { }	

	public abstract function transform($item);
}