<?php namespace LRezek\Cbor4Laravel\Facades;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Facades\Response as ResponseFacade;
use LRezek\Cbor4Laravel\Http\CborResponse;
use LRezek\Cbor4Laravel\Http\Response as CustomResponse;

class Response extends ResponseFacade
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

    /**
     * Return a new response from the application (a custom one they can do whatever with).
     *
     * @param  string  $content
     * @param  int     $status
     * @param  array   $headers
     * @return CustomResponse
     */
    public static function make($content = '', $status = 200, array $headers = array())
    {
        return new CustomResponse($content, $status, $headers);
    }
}

?>
