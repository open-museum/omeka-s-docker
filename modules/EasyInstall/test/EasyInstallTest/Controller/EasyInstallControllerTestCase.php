<?php

namespace EasyInstallTest\Controller;

use OmekaTestHelper\Controller\OmekaControllerTestCase;

abstract class EasyInstallControllerTestCase extends OmekaControllerTestCase
{
    public function setUp()
    {
        $this->loginAsAdmin();
    }
}
