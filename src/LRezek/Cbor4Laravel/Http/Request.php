<?php namespace LRezek\Cbor4Laravel\Http;

use Illuminate\Http\Request as BaseRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use CBOR\CBOREncoder;

class Request extends BaseRequest
{
    /** @var ParameterBag The decoded CBOR content for the request. */
    protected $cbor;

    /**
     * Determine if the request is in CBOR.
     *
     * @return bool
     */
    public function isCbor()
    {
         return str_contains($this->header('CONTENT_TYPE'), '/cbor');
    }

    /**
     * Determine if the request is asking for CBOR in return.
     *
     * @return bool
     */
    public function wantsCbor()
    {
        $acceptable = $this->getAcceptableContentTypes();
 
        return isset($acceptable[0]) && ($acceptable[0] == 'application/cbor');
    }
    
    /**
     * Get the CBOR payload for the request.
     *
     * @param  string  $key The key to get the value for, in "dot notation" (ex: user.username = cbor["user"]["username"]).
     * @param  mixed   $default Default value if the key isn't found.
     * @return mixed
     */
    public function cbor($key = null, $default = null)
    {
        //If there is no message content, just return null
        if(is_null($this->getContent()))
        {
            return null;
        }
        
        //Don't decode twice!
        if(!isset($this->cbor))
        {
            $this->cbor = new ParameterBag((array) CBOREncoder::decode($this->getContent(), true));
        }
 
        //Return the whole array if no key was specified
        if(is_null($key))
        {
            return $this->cbor;
        }
 
        //Get key value in dot notation
        return array_get($this->cbor->all(), $key, $default);
    }

    /**
     * Get the input source for the request.
     *
     * @see \Illuminate\Http\Request::getInputSource()
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        //Decode cbor
        if($this->isCbor())
        {
            return $this->cbor();
        }
        
        //Fall back to Request implementation of getInputSource().
        return parent::getInputSource();
    }

}

?>
