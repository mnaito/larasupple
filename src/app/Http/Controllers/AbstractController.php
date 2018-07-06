<?php

namespace Mits430\Larasupple\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class AbstractController extends AbstractTemplatedController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
