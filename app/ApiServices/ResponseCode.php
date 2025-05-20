<?php

namespace App\ApiServices;

/*
 * 200 OK           for successful GET requests
 * 201 Created      for successful POST requests
 * 204 No Content   for successful DELETE requests
 * 400 Bad Request  for validation errors
 * 404 Not Found    for missing resources
 */
class ResponseCode
{
    // SUCCESSFUL RESPONSES
    const OK                = 200;
    const CREATED           = 201;
    const NO_CONTENT        = 204;

    // CLIENT ERROR RESPONSES
    const UNAUTHORIZED      = 401;
    const FORBIDDEN         = 403;
    const BAD_REQUEST       = 400;
    const NOT_FOUND         = 404;
    const VALIDATION_ERROR  = 422;

    // SERVER ERROR RESPONSE
    const INTERNAL_SERVER_ERROR = 500;
}
