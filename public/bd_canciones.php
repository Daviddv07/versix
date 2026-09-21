<?php
declare(strict_types=1);
define('VERSIX_API', true);
require __DIR__ . '/../src/bootstrap.php';
require_user(); json_response(['songs' => catalogue()]);
