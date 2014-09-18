<?php namespace LRezek\Cbor4Laravel\Http;

use ArrayObject;
use Illuminate\Support\Contracts\JsonableInterface;
use LRezek\Cbor4Laravel\Support\CborableInterface;
use Illuminate\Support\Contracts\RenderableInterface;
use Illuminate\Http\ResponseTrait;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    //Add header and cookie functions
	use ResponseTrait;

    //TODO: When is it cbor, and when is it JSON?

	/** @var mixed The original content of the response. */
	public $original;

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

        return cbor_encode($content);
    }

	/**
	 * Determine if the given content should be turned into JSON.
	 *
	 * @param  mixed  $content
	 * @return bool
	 */
	protected function shouldBeJson($content)
	{
		return $content instanceof JsonableInterface ||
			   $content instanceof ArrayObject ||
			   is_array($content);
	}

    /**
     * Determine if the given content should be turned into CBOR.
     *
     * @param  mixed  $content
     * @return bool
     */
    protected function shouldBeCbor($content)
    {
        return $content instanceof CborableInterface ||
               $content instanceof ArrayObject ||
               is_array($content);
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

}
