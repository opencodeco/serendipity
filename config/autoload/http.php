<?php

declare(strict_types=1);

use Serendipity\Presentation\Output\Accepted;
use Serendipity\Presentation\Output\AlreadyReported;
use Serendipity\Presentation\Output\Created;
use Serendipity\Presentation\Output\Error\BadGateway;
use Serendipity\Presentation\Output\Error\GatewayTimeout;
use Serendipity\Presentation\Output\Error\InsufficientStorage;
use Serendipity\Presentation\Output\Error\InternalServerError;
use Serendipity\Presentation\Output\Error\LoopDetected;
use Serendipity\Presentation\Output\Error\NetworkAuthenticationRequired;
use Serendipity\Presentation\Output\Error\NotImplemented;
use Serendipity\Presentation\Output\Error\ProtocolVersionNotSupported;
use Serendipity\Presentation\Output\Error\ServiceUnavailable;
use Serendipity\Presentation\Output\Error\VariantAlsoNegotiates;
use Serendipity\Presentation\Output\Fail\BadRequest;
use Serendipity\Presentation\Output\Fail\Conflict;
use Serendipity\Presentation\Output\Fail\ExpectationFailed;
use Serendipity\Presentation\Output\Fail\FailedDependency;
use Serendipity\Presentation\Output\Fail\Forbidden;
use Serendipity\Presentation\Output\Fail\Gone;
use Serendipity\Presentation\Output\Fail\LengthRequired;
use Serendipity\Presentation\Output\Fail\Locked;
use Serendipity\Presentation\Output\Fail\MethodNotAllowed;
use Serendipity\Presentation\Output\Fail\Misdirected;
use Serendipity\Presentation\Output\Fail\NotAcceptable;
use Serendipity\Presentation\Output\Fail\NotFound;
use Serendipity\Presentation\Output\Fail\PayloadTooLarge;
use Serendipity\Presentation\Output\Fail\PaymentRequired;
use Serendipity\Presentation\Output\Fail\PreconditionFailed;
use Serendipity\Presentation\Output\Fail\PreconditionRequired;
use Serendipity\Presentation\Output\Fail\PropertiesAreTooLarge;
use Serendipity\Presentation\Output\Fail\ProxyAuthenticationRequired;
use Serendipity\Presentation\Output\Fail\RangeNotSatisfiable;
use Serendipity\Presentation\Output\Fail\RequestTimeout;
use Serendipity\Presentation\Output\Fail\TooEarly;
use Serendipity\Presentation\Output\Fail\TooMany;
use Serendipity\Presentation\Output\Fail\Unauthorized;
use Serendipity\Presentation\Output\Fail\UnavailableForLegalReasons;
use Serendipity\Presentation\Output\Fail\UnprocessableEntity;
use Serendipity\Presentation\Output\Fail\UnsupportedMediaType;
use Serendipity\Presentation\Output\Fail\UpdateRequired;
use Serendipity\Presentation\Output\ImUsed;
use Serendipity\Presentation\Output\MultiStatus;
use Serendipity\Presentation\Output\NoContent;
use Serendipity\Presentation\Output\NonAuthoritative;
use Serendipity\Presentation\Output\Ok;
use Serendipity\Presentation\Output\PartialContent;
use Serendipity\Presentation\Output\ResetContent;

use function Hyperf\Support\env;

return [
    'hosts' => [
        'mockoon' => [
            'base_uri' => env('MOCKOON_URL', 'http://mockoon:3000/api/v1'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ],
    ],
    'result' => [
        ########################
        ## 2xx Success Output ##
        ########################
        # This is the standard response for successful HTTP requests.
        OK::class => ['status' => 200],
        # The request succeeded and a new resource was created. This is usually the response after POST or PUT
        #   requests.
        Created::class => ['status' => 201],
        # The request was accepted but is still in progress. It’s used for cases where another server handles the
        # request or for batch processing.
        Accepted::class => ['status' => 202],
        # The data returned isn’t from the origin server. Instead, it’s a modified version collected from a third
        #   party.
        NonAuthoritative::class => ['status' => 203],
        # The request was successfully processed, but there is no content. The headers may be useful.
        NoContent::class => ['status' => 204],
        # The server fulfilled the request but asked the user to reset the document.
        ResetContent::class => ['status' => 205],
        # The server is delivering part of the resource. This response is used when the client sends a Range header to
        #   request only part of a resource.
        PartialContent::class => ['status' => 206],
        # Provides the statuses of multiple resources, depending on how many sub-requests were made.
        MultiStatus::class => ['status' => 207],
        # The members of a DAV:propstat element have already been listed and won’t be included again.
        AlreadyReported::class => ['status' => 208],
        # The server completed a GET request. And the response indicates one or more instance-manipulation results.
        ImUsed::class => ['status' => 226],
        ########################
        ### 4xx Client Error ###
        ########################
        # The server can’t or won’t process the request due to a client error. For example, invalid request message
        #   framing, deceptive request routing, size too large, etc.
        BadRequest::class => ['status' => 400],
        # The user doesn’t have valid authentication credentials to get the requested resource.
        Unauthorized::class => ['status' => 401],
        # Reserved for future use; it was initially intended for digital payment systems. It’s very rarely used, and
        #   no standard convention regulates it.
        PaymentRequired::class => ['status' => 402],
        # The client doesn’t have access rights to the content. For example, it may require a password. Unlike the
        #   401 HTTP error code, the server does know the client’s identity.
        Forbidden::class => ['status' => 403],
        # The server can’t find the requested resource, and no redirection has been set. 404 errors can harm your SEO
        #   efforts.
        NotFound::class => ['status' => 404],
        # The server supports the request method, but the target resource doesn’t.
        MethodNotAllowed::class => ['status' => 405],
        # The server doesn’t find any content that satisfies the criteria given by the user according to the Accept
        #   headers requested.
        NotAcceptable::class => ['status' => 406],
        # This is similar to a 401, but a proxy must authenticate the client to continue.
        ProxyAuthenticationRequired::class => ['status' => 407],
        # The server timed out waiting because the client didn’t produce a request within the allotted time.
        RequestTimeout::class => ['status' => 408],
        # The server can’t fulfill the request because there’s a conflict with the resource. It’ll display information
        #   about the problem so the client can fix it and resubmit.
        Conflict::class => ['status' => 409],
        # The content requested has been permanently deleted from the server and will not be reinstated.
        Gone::class => ['status' => 410],
        # The server rejects the request because it requires a defined Content-Length header field.
        LengthRequired::class => ['status' => 411],
        # The client indicates preconditions in the header fields that the server fails to meet.
        PreconditionFailed::class => ['status' => 412],
        # The client’s request is larger than the server’s defined limits, and the server refuses to process it.
        PayloadTooLarge::class => ['status' => 413],
        # The request uses a media format the server does not support.
        UnsupportedMediaType::class => ['status' => 415],
        # The server can’t fulfill the value indicated in the request’s Range header field.
        RangeNotSatisfiable::class => ['status' => 416],
        # The server can’t meet the requirements indicated by the Expect request header field.
        ExpectationFailed::class => ['status' => 417],
        # The client sends a request to a server that can’t produce a response.
        Misdirected::class => ['status' => 421],
        # The client correctly sends the request, but the server can’t process it because of semantic errors or similar
        #   issues.
        UnprocessableEntity::class => ['status' => 422],
        # The requested method’s resource is locked and inaccessible.
        Locked::class => ['status' => 423],
        # The request failed because a request the initial request depended on also failed.
        FailedDependency::class => ['status' => 424],
        # The server is unwilling to process a request that might be replayed.
        TooEarly::class => ['status' => 425],
        # The server refuses to process the request using the current protocol unless the client upgrades to a
        #   different protocol.
        UpdateRequired::class => ['status' => 426],
        # The server needs the request to be conditional.
        PreconditionRequired::class => ['status' => 428],
        # The user sends too many requests in a certain amount of time.
        TooMany::class => ['status' => 429],
        # The server can’t process the request because the header fields are too large.
        PropertiesAreTooLarge::class => ['status' => 431],
        # The user requests a resource the server can’t legally provide.
        UnavailableForLegalReasons::class => ['status' => 451],
        ########################
        ### 5xx Server Error ###
        ########################
        # The server has encountered an unexpected error and cannot complete the request.
        InternalServerError::class => ['status' => 500],
        # The server can’t fulfill the request or doesn’t recognize the request method.
        NotImplemented::class => ['status' => 501],
        # The server acts as a gateway and gets an invalid response from an inbound host.
        BadGateway::class => ['status' => 502],
        # The server is unable to process the request. This often occurs when a server is overloaded or down for
        #   maintenance.
        ServiceUnavailable::class => ['status' => 503],
        # The server was acting as a gateway or proxy and timed out, waiting for a response.
        GatewayTimeout::class => ['status' => 504],
        # The server doesn’t support the HTTP version in the request.
        ProtocolVersionNotSupported::class => ['status' => 505],
        # The server has an internal configuration error.
        VariantAlsoNegotiates::class => ['status' => 506],
        # The server doesn’t have enough storage to process the request successfully.
        InsufficientStorage::class => ['status' => 507],
        # The server detects an infinite loop while processing the request.
        LoopDetected::class => ['status' => 508],
        # The client must be authenticated to access the network. The error should include a link where the user can
        #   submit credentials.
        NetworkAuthenticationRequired::class => ['status' => 511],
    ],
];
