<?php

/**
 * Class HTTP_Response
 *
 * Lightweight HTTP response capable of the following:
 *
 * Retrieve HTTP response payloads
 * Retrieve HTTP response headers
 * Throw an exception for any JSON conversion errors
 * Return all JSON payloads as associative arrays
 */
class HTTP_Response
{
  private $response;
  private $headers;

  public function __construct($response, $headers = [])
  {
    $this->response = $response;
    $this->headers = $headers;
  }

  /**
   * Returns body response in correct (string / JSON array) form.
   *
   * @return mixed[]/string
   * @throws Exception On json_decode errors
   */
  public function getBody()
  {
    // If the payload is in json, try to decode json.
    if (strpos(strtolower(implode(', ', $this->getHeaders())), 'application/json') !== false) {
      $result = json_decode($this->response, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        return $result;
      } else {
        throw new Exception("Error decoding JSON: " . json_last_error());
      }
    }

    return $this->response;
  }

  /**
   * Returns response header.
   *
   * @return array
   */
  public function getHeaders()
  {
    return $this->headers;
  }
}
