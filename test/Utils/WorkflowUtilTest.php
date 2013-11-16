<?php

namespace Utils;

use \PHPUnit_Framework_TestCase;
use \DateTime;
use \DateTimeZone;

require_once 'src/Utils/WorkflowUtil.php';

class WorkflowUtilTest extends PHPUnit_Framework_TestCase
{
	public function testNormalize()
	{
		$this->assertEquals( "Zürich", WorkflowUtil::normalize( json_decode( '"Z\u0075\u0308rich"' ) ) );
		$this->assertEquals( "übermorgen", WorkflowUtil::normalize( json_decode( '"\u0075\u0308bermorgen"' ) ) );
	}
}