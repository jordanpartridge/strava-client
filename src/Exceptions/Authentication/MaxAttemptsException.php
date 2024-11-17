<?php

namespace JordanPartridge\StravaClient\Exceptions\Authentication;

use Illuminate\Auth\Access\AuthorizationException;

class MaxAttemptsException extends AuthorizationException {}
