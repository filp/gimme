<?php
/**
 * Gimme - inject your services and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme\Exception;

/**
 * Thrown when a service is referenced, but not resolvable,
 * and Gimme\Resolver is configured to throw exceptions in such
 * a situation.
 */
class UnknownServiceException extends \Exception {}
