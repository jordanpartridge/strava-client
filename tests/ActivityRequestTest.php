<?php

use JordanPartridge\StravaClient\Requests\ActivityRequest;

it('uses the correct endpoint', function () {
    $request = new ActivityRequest(1);
    expect($request->resolveEndpoint())->toBe('/activities/1');
});

it('will throw an exception if the id is not a positive integer', function () {
    $request = new ActivityRequest(-1);
})->throws(InvalidArgumentException::class);
