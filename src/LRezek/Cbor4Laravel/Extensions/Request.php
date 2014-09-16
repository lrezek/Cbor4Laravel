<?php

namespace LRezek\Cbor4Laravel\Extensions;

use \Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{

    public function doSomething()
    {
        echo 'Doing something!';
    }

}

?>