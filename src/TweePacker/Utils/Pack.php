<?php
namespace TweePacker\Utils;
use ArrayObject;

class Pack extends ArrayObject
{
    public function toArray()
    {
        return $this->getArrayCopy();
    }
}