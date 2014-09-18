<?php namespace LRezek\Cbor4Laravel\Support;

interface CborableInterface
{
    /**
     * Convert the object to its CBOR representation.
     *
     * @return string
     */
    public function toCbor();
}

?> 