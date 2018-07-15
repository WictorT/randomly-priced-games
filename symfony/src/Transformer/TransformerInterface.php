<?php
namespace App\Transformer;

interface TransformerInterface
{
    public function transform();
    public function reverseTransform();
}
