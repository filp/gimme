<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme\Exception;

/**
 * Thrown when a dependency is referenced, but not resolvable,
 * and Gimme\Resolver is configured to throw exceptions in such
 * a situation.
 */
class UnknownDependencyException extends \Exception {}
