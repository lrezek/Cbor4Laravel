<?php namespace LRezek\Cbor4Laravel\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\ResponseTrait;
use LRezek\Cbor4Laravel\Support\CborableInterface;
use LRezek\Cbor4Laravel\Util\CBOREncoder;

class CborResponse extends SymfonyResponse
{
    //Add header and cookie functions
    use ResponseTrait;

    /** @var String CBOR encoded data. */
    protected $data;

    /**
     * Constructor.
     *
     * @param mixed   $data    The response data
     * @param int     $status  The response status code
     * @param array   $headers An array of response headers
     */
    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        if($data == null)
        {
            $data = new \ArrayObject();
        }

        $this->setData($data);
    }

    /**
     * Factory method for chainability
     *
     * Example:
     *
     *     return Response::create($body, 200)
     *         ->setSharedMaxAge(300);
     *
     * @param mixed   $data The response content, see setContent()
     * @param int     $status  The response status code
     * @param array   $headers An array of response headers
     *
     * @return Response
     */
    public static function create($data = null, $status = 200, $headers = array())
    {
        return new static($data, $status, $headers);
    }

    /**
     * Sets data of the response.
     *
     * @param array $data
     * @return CborResponse
     */
    public function setData($data = array())
    {
        $this->data = $data instanceof CborableInterface ? $data->toCbor() : CBOREncoder::encode($data);

        return $this->update();
    }

    /**
     * Updates the content and headers according to the CBOR data.
     *
     * @return CborResponse
     */
    protected function update()
    {
        // Only set the header when there is none.
        if(!$this->headers->has('Content-Type'))
        {
            $this->headers->set('Content-Type', 'application/cbor');
        }

        return $this->setContent($this->data);
    }

    /**
     * Get the cbor decoded data from the response
     *
     * @return mixed
     */
    public function getData()
    {
        return CBOREncoder::decode($this->data);
    }

}
