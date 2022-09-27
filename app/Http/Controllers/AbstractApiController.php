<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class AbstractApiController extends Controller
{
    /**
     * The HTTP response headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The HTTP response meta data.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * The HTTP response data.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * The HTTP response status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * The status in string
     * @var string
     */
    protected $status = '';

    /**
     * The message
     *
     * @var string
     */
    protected $message = '';

    /**
     * Set the response headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    protected function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the response meta data.
     *
     * @param array $meta
     *
     * @return $this
     */
    protected function setMetaData(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Set the response meta data.
     *
     * @param array $data
     *
     * @return $this
     */
    protected function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the response status code.
     *
     * @param int $statusCode
     *
     * @return $this
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    protected function setStatus($status)
    {
        $this->status = $status;
    }

    protected function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Respond with a no content response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContent()
    {
        return $this->setStatusCode(204)->respond();
    }

    /**
     * Build the response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond()
    {
        $response = [
            'status'  => $this->status,
            'message' => $this->message,
        ];

        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        $response['data'] = $this->data;

        if ($this->data instanceof Arrayable) {
            $response['data'] = $this->data->toArray();
        }

        return response()->json($response, $this->statusCode, $this->headers);
    }
}
