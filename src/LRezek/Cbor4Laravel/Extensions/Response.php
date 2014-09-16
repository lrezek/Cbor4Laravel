<?php

napespace LRezek\Cbor4Laravel\Extensions;

use Illuminate\Support\Facades\Response as BaseResponse;

class Response extends BaseResponse 
{

    public static function doSomething()
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse(['message' => 'yay!']);
    }

}

?>
