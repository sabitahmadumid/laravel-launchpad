<?php

it('can initialize launchpad service provider', function () {
    expect(app('laravel-launchpad'))->not()->toBeNull();
});
