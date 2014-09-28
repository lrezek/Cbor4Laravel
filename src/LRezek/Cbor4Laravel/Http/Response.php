<?php namespace LRezek\Cbor4Laravel\Http;

use ArrayObject;
use Exception;
use Illuminate\Support\Contracts\JsonableInterface;
use LRezek\Cbor4Laravel\Support\CborableInterface;
use Illuminate\Support\Contracts\RenderableInterface;
use Illuminate\Http\ResponseTrait;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use LRezek\Cbor4Laravel\Util\CBOREncoder;

class Response extends SymfonyResponse
{
    //Add header and cookie functions
	use ResponseTrait;

	/** @var mixed The original content of the response. */
	public $original;

    /** @var string The type of encoding to do. */
    public $format;

    /**
     * Constructs the response with the specified content, status, headers, and encoding type.
     * @param mixed|string $content The response content.
     * @param int $status The response status.
     * @param array $headers The response headers.
     * @throws Exception Thrown if the type is invalid.
     */
    public function __construct($content, $status, $headers)
    {
        //Default format
        $this->format = 'json';

        parent::__construct($content, $status, $headers);
    }

	/**
	 * Set the content on the response.
	 *
	 * @param  mixed  $content
	 * @return $this
	 */
	public function setContent($content)
	{
        //Save original content
		$this->original = $content;

		// If the content is "JSONable" we will set the appropriate header and convert
		// the content to JSON. This is useful when returning something like models
		// from routes that will be automatically transformed to their JSON form.
		if($this->shouldBeJson($content))
		{
			$this->headers->set('Content-Type', 'application/json');

			$content = $this->morphToJson($content);
		}

        //If the content is "CBORable" convert it to cbor.
        else if($this->shouldBeCbor($content))
        {
            $this->headers->set('Content-Type', 'application/cbor');

            $content = $this->morphToCbor($content);
        }

		// If this content implements the "RenderableInterface", then we will call the
		// render method on the object so we will avoid any "__toString" exceptions
		// that might be thrown and have their errors obscured by PHP's handling.
		else if($content instanceof RenderableInterface)
		{
			$content = $content->render();
		}

		return parent::setContent($content);
	}

	/**
	 * Morph the given content into JSON.
	 *
	 * @param  mixed   $content
	 * @return string
	 */
	protected function morphToJson($content)
	{
		if($content instanceof JsonableInterface)
        {
            return $content->toJson();
        }

		return json_encode($content);
	}

    /**
     * Morph the given content into CBOR.
     *
     * @param  mixed   $content
     * @return string
     */
    protected function morphToCbor($content)
    {
        if($content instanceof CborableInterface)
        {
            return $content->toCbor();
        }

        return CBOREncoder::encode($content);
    }

	/**
	 * Determine if the given content should be turned into JSON.
	 *
	 * @param  mixed  $content
	 * @return bool
	 */
	protected function shouldBeJson($content)
	{
        //It should be json'd if the type is json and if implements JSONableInterface, ArrayObject, or is an array.
        if($this->format == 'json')
        {
            return $content instanceof JsonableInterface ||
                   $content instanceof ArrayObject ||
                   is_array($content);
        }

        return false;
	}

    /**
     * Determine if the given content should be turned into CBOR.
     *
     * @param  mixed  $content
     * @return bool
     */
    protected function shouldBeCbor($content)
    {
        //It should be cbor'd if the type is cbor and if implements CBORableInterface, ArrayObject, or is an array.
        if($this->format == 'cbor')
        {
            return $content instanceof CborableInterface ||
                   $content instanceof ArrayObject ||
                   is_array($content);
        }

        return false;
    }

	/**
	 * Get the original response content.
	 *
	 * @return mixed
	 */
	public function getOriginalContent()
	{
		return $this->original;
	}

    /**
     * Changes the format of the response.
     *
     * @param string $format The format to use cbor|json
     * @return Response $this This response for chainability.
     * @throws Exception If the requested format is invalid.
     */
    public function format($format)
    {
        $format = strtolower($format);

        //If the format is invalid
        if( (strcmp($format,'json') != 0) && (strcmp($format,'cbor') != 0))
        {
            throw new Exception("$format is not a valid encoding format for a response.");
        }

        $this->format = $format;

        //Allow command chaining
        return $this;
    }

}
