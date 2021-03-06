<?php
namespace EllipseSynergie\ApiResponse;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Manager;
use League\Fractal\Cursor\Cursor;

/**
 * Class Response
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package EllipseSynergie\ApiResponse
 * @author Maxime Beaudoin <maxime.beaudoin@ellipse-synergie.com>
 * @author Phil Sturgeon <email@philsturgeon.co.uk>
 */
abstract class AbstractResponse
{

    const CODE_WRONG_ARGS = 'GEN-WRONG-ARGS';

    const CODE_NOT_FOUND = 'GEN-NOT-FOUND';

    const CODE_INTERNAL_ERROR = 'GEN-INTERNAL-ERROR';

    const CODE_UNAUTHORIZED = 'GEN-UNAUTHORIZED';

    const CODE_FORBIDDEN = 'GEN-FORBIDDEN';

    const CODE_GONE = 'GEN-GONE';

    const CODE_METHOD_NOT_ALLOWED = 'GEN-METHOD-NOT-ALLOWED';

    const CODE_UNWILLING_TO_PROCESS = 'GEN-UNWILLING-TO-PROCESS';

    /**
     * HTTP Status code
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Fractal manager
     *
     * @var \League\Fractal\Manager
     */
    protected $fractal;

    /**
     * @param \League\Fractal\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->fractal = $manager;
    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for status code
     *
     * @param int $statusCode
     * @return \EllipseSynergie\ApiResponse\Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Implement this !!!
     * This method return the final response output
     *
     * @param array $array
     * @param array $headers
     */
    abstract public function withArray(array $array, array $headers = array());

    /**
     * Response for one item
     *
     * @param mixed $item
     * @param mixed $callback
     * @return mixed
     */
    public function withItem($item, $callback)
    {
        $resource = new Item($item, $callback);

        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Response for collection of items
     *
     * @param mixed $item
     * @param mixed $callback
     * @param \League\Fractal\Cursor\Cursor $cursor
     * @return mixed
     */
    public function withCollection($collection, $callback, Cursor $cursor = null)
    {
        $resource = new Collection($collection, $callback);

        if (!is_null($cursor)) {
            $resource->setCursor($cursor);
        }

        $rootScope = $this->fractal->createData($resource);

        return $this->withArray($rootScope->toArray());
    }

    /**
     * Response for errors
     *
     * @param string $message
     * @param string $errorCode
     * @return mixed
     */
    protected function withError($message, $errorCode)
    {
        return $this->withArray([
            'error' => [
                'code' => $errorCode,
                'http_code' => $this->statusCode,
                'message' => $message
            ]
        ]);
    }

    /**
     * Generates a response with a 403 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->withError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Generates a response with a 500 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->withError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a response with a 404 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->withError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Generates a response with a 401 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->withError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Generates a response with a 400 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->withError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * Generates a response with a 410 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorGone($message = 'Resource No Longer Available')
    {
        return $this->setStatusCode(410)->withError($message, self::CODE_GONE);
    }

    /**
     * Generates a response with a 405 HTTP header and a given message.
     *
     * @param string $message
     * @return mixed
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        return $this->setStatusCode(405)->withError($message, self::CODE_METHOD_NOT_ALLOWED);
    }

    /**
     * Generates a Response with a 431 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorUnwillingToProcess($message = 'Server is unwilling to process the request')
    {
        return $this->setStatusCode(431)->withError($message, self::CODE_UNWILLING_TO_PROCESS);
    }
}