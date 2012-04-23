<?php
/**
 * This file is part of the Vigu PHP error aggregation system.
 * @link https://github.com/Ibmurai/vigu
 *
 * @copyright Copyright 2012 Jens Riisom Schultz, Johannes Skov Frandsen
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

// Zaphod with Vigu configuration
require_once dirname(__FILE__) . '/../lib/zaphod/src/Zaphod.php';
require_once dirname(__FILE__) . '/../lib/frood/src/Frood/Configuration.php';
require_once dirname(__FILE__) . '/../Configuration.php';
Zaphod::run(new ViguConfiguration());
