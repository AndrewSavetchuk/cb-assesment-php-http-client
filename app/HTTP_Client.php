<?php

require_once('app/HTTP_Response.php');

/**
 * Class HTTP_Client
 *
 * Lightweight HTTP client capable of the following:
 *
 * Send HTTP requests to the given URL using different methods, such as GET, POST, etc.
 * Send JSON payloads
 * Send custom HTTP headers
 * Throw an exception for erroneous HTTP response codes (e.g. 4xx, 5xx)
 */
class HTTP_Client
{
  /**
   * GET request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */

  public static function get($url, $body = null, $headers = [])
  {
    return self::send('GET', $url, $body, $headers);
  }

  /**
   * POST request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */
  public static function post($url, $body = null, $headers = [])
  {
    return self::send('POST', $url, $body, $headers);
  }

  /**
   * PUT request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */
  public static function put($url, $body = null, $headers = [])
  {
    return self::send('PUT', $url, $body, $headers);
  }

  /**
   * DELETE request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */
  public static function delete($url, $body = null, $headers = [])
  {
    return self::send('DELETE', $url, $body, $headers);
  }

  /**
   * HEAD request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */
  public static function head($url, $body = null, $headers = [])
  {
    return self::send('HEAD', $url, $body, $headers);
  }

  /**
   * OPTIONS request.
   *
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return mixed
   * @throws Exception
   */
  public static function options($url, $body = null, $headers = [])
  {
    return self::send('OPTIONS', $url, $body, $headers);
  }

  /**
   * Build structure for HTTP Request.
   *
   * @param string $method Method (GET, POST, etc.)
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return array Request data: 0 - url; 1 - request options
   */
  private static function buildRequest($method, $url, $body = null, $headers = [])
  {
    $content = '';

    $method = strtoupper($method);
    $headers = array_change_key_case($headers, CASE_LOWER);

    switch ($method) {
      case 'HEAD':
      case 'OPTIONS':
      case 'GET':
        if (is_array($body)) {
          if (strpos($url, '?') !== false) {
            $url .= '&';
          } else {
            $url .= '?';
          }

          $url .= urldecode(http_build_query($body));
        }
        break;
      case 'DELETE':
      case 'PUT':
      case 'POST':
        if (is_array($body)) {
          if (!empty($headers['content-type'])) {
            switch (trim($headers['content-type'])) {
              case 'application/x-www-form-urlencoded':
                $body = http_build_query($body);
                break;
              case 'application/json':
                $body = json_encode($body);
                break;
            }
          } else {
            $headers['content-type'] = 'application/x-www-form-urlencoded';
            $body = http_build_query($body);
          }
        } elseif (empty($headers['content-type'])) {
          $headers['content-type'] = 'application/x-www-form-urlencoded';
        }

        $content = $body;
        break;
    }

    $options = [
      'http' => [
        'method' => $method,
      ],
    ];

    if ($headers) {
      $options['http']['header'] = implode(
        "\r\n",
        array_map(
          function ($v, $k) {
            return sprintf("%s: %s", $k, $v);
          },
          $headers,
          array_keys($headers)
        )
      );
    }

    if ($content) {
      $options['http']['content'] = $content;
    }

    return [$url, $options];
  }

  /**
   * Sends HTTP request.
   *
   * @param string $method Method (GET, POST, etc.)
   * @param string $url Request URL
   * @param array $body Request body
   * @param array $headers Request headers
   * @return HTTP_Response
   * @throws Exception
   */
  public static function send($method, $url, $body = null, $headers = [])
  {
    [$url, $options] = self::buildRequest($method, $url, $body, $headers);

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
      $status_line = implode(',', $http_response_header);
      preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
      $status = $match[1];

      // If the status code not in 2xx or 3xx, throw an exception.
      if (strpos($status, '2') !== 0 && strpos($status, '3') !== 0) {
        throw new Exception("Unexpected response status: {$status} while fetching {$url}\n" . $status_line);
      }
    }

    return new HTTP_Response($result, $http_response_header);
  }
}
