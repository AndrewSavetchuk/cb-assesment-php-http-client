<?php

require_once 'config.php';

require_once 'app/Utils.php';
require_once 'app/HTTP_Client.php';

/**
 * GET request.
 */
function getRequest1()
{
  $response = HTTP_Client::get('https://google.com/');
  Utils::dumpResponse($response);
}

/**
 * GET request with parameters.
 */
function getRequest2()
{
  $response = HTTP_Client::get('https://postman-echo.com/get', ['foo' => 'bar', 'lorem' => 'ipsum']);
  Utils::dumpResponse($response);
}

/**
 * GET request with 404 error.
 */
function getRequest3()
{
  $response = HTTP_Client::get('https://google.com/404');
  Utils::dumpResponse($response);
}

/**
 * POST request with body.
 */
function postRequest1()
{
  $response = HTTP_Client::post('https://postman-echo.com/post', 'Just text string');
  Utils::dumpResponse($response);
}

/**
 * POST request with array params.
 */
function postRequest2()
{
  $response = HTTP_Client::post('https://postman-echo.com/post', ['foo' => 'bar', 'lorem' => 'ipsum']);
  Utils::dumpResponse($response);
}

/**
 * POST request with array as json.
 */
function postRequest3()
{
  $response = HTTP_Client::post(
    'https://postman-echo.com/post',
    ['foo' => 'bar', 'lorem' => 'ipsum'],
    ['content-type' => 'application/json']
  );
  Utils::dumpResponse($response);
}

/**
 * OPTIONS request.
 */
function optionsRequest1()
{
  $response = HTTP_Client::options('https://www.coredna.com/assessment-endpoint.php');
  Utils::dumpResponse($response);
}

/**
 * Submit assessment to Coderbyte.
 */
function submitAssessment()
{
  $tokenResponse = HTTP_Client::options('https://www.coredna.com/assessment-endpoint.php');

  $response = HTTP_Client::post(
    'https://www.coredna.com/assessment-endpoint.php',
    [
      'name' => 'empty',
      'email' => 'empty',
      'url' => 'empty',
    ],
    [
      'Authorization' => 'Bearer ' . $tokenResponse->getBody(),
      'content-type' => 'application/json',
    ]
  );

  Utils::dumpResponse($response);
}

/**
 * Entry point.
 */
try {
  optionsRequest1();
} catch (Exception $e) {
  Utils::dumpPretty($e);
}
