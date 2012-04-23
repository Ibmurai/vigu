<?php
/**
 * This file is part of the Vigu PHP error aggregation system.
 * @link https://github.com/Ibmurai/vigu
 *
 * @copyright Copyright 2012 Jens Riisom Schultz, Johannes Skov Frandsen
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
/**
 * Sets the default output mode to Twig.
 *
 * @author Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class SitePublicController extends FroodController {
	/**
	 * Initialization.
	 *
	 * @return void
	 */
	protected function initialize() {
		parent::initialize();

		$this->doOutputTwig();
	}
}
