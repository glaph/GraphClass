<?php

namespace GraphClass\Input;

use GraphClass\Resolver\Resolvable;

interface Input extends Resolvable, \Iterator, \Countable, \ArrayAccess {
}
