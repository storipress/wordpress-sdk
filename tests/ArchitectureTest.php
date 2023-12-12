<?php

use Storipress\WordPress\Exceptions\WordPressException;
use Storipress\WordPress\Objects\WordPressObject;
use Storipress\WordPress\Requests\Request;

test('There are no debugging statements remaining in our code.')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not()
    ->toBeUsed();

test('Strict typing must be enforced in the code.')
    ->expect('Storipress\WordPress')
    ->toUseStrictTypes();

test('The code should not utilize the "final" keyword.')
    ->expect('Storipress\WordPress')
    ->not()
    ->toBeFinal();

test('All Request classes should extend "Request".')
    ->expect('Storipress\WordPress\Requests')
    ->classes()
    ->toExtend(Request::class);

test('All Object classes should extend "WordPressObject".')
    ->expect('Storipress\WordPress\Objects')
    ->classes()
    ->toExtend(WordPressObject::class);

test('All Exception classes should extend "Exception".')
    ->expect('Storipress\WordPress\Exceptions')
    ->classes()
    ->toExtend(WordPressException::class);
