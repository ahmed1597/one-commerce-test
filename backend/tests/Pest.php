<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(TestCase::class)->in('Feature');

uses(RefreshDatabase::class)->in('Feature');  

expect()->extend('toBeOne', fn () => $this->toBe(1));

function something() { /* ... */ }