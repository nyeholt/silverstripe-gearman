<?php

/**
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
class GearmanTestHandler implements GearmanHandler {
	public function getName() {
		return "GearmanTest";
	}
	
	public function GearmanTest() {
		echo "Called GearmanTest()\n";
		print_r(func_get_args());
	}
}
