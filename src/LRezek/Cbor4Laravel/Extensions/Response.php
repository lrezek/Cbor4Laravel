<?php

namespace LRezek\Cbor4Laravel\Extensions;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Facades\Response as LaravelResponse;

class Response extends LaravelResponse
{
    /**
     * Creates a new CBOR response.
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return CborResponse
     */
    public static function cbor($data = array(), $status = 200, array $headers = array())
    {
        if($data instanceof ArrayableInterface)
        {
            $data = $data->toArray();
        }

        return new CborResponse($data, $status, $headers);
    }
}

?>
