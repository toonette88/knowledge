<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

Request::setTrustedProxies(
    // trust *all* requests
    ['127.0.0.1', $request->server->get('REMOTE_ADDR')],
    Request::HEADER_X_FORWARDED_ALL
);

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
