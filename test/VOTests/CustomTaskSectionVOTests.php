<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

class CustomTaskSectionVOTests extends PHPUnit_Framework_TestCase
{

    protected $VO;

    protected function setUp()
    {

        $this->VO = new CustomTaskSectionVO();

    }

    public function testNew()
    {

        $this->assertNotNull($this->VO);

    }

    public function testIdField()
    {

        $this->VO->setId(1);

        $this->assertEquals($this->VO->getId(), 1);

        $this->VO->setId(2);

        $this->assertEquals($this->VO->getId(), 2);

    }

    public function testNameField()
    {

        $this->VO->setName('Mars');

        $this->assertEquals($this->VO->getName(), 'Mars');

        $this->VO->setName('Omicron Persei');

        $this->assertEquals($this->VO->getName(), 'Omicron Persei');

    }

    public function testEstHoursField()
    {

        $this->VO->setEstHours(15.75);

        $this->assertEquals($this->VO->getEstHours(), 15.75);

        $this->VO->setEstHours(7.66);

        $this->assertEquals($this->VO->getEstHours(), 7.66);

    }

    public function testRiskField()
    {

        $this->VO->setRisk(1);

        $this->assertEquals($this->VO->getRisk(), 1);

        $this->VO->setRisk(3);

        $this->assertEquals($this->VO->getRisk(), 3);

    }

    public function testSectionIdField()
    {

        $this->VO->setSectionId(2);

        $this->assertEquals($this->VO->getSectionId(), 2);

        $this->VO->setSectionId(45);

        $this->assertEquals($this->VO->getSectionId(), 45);

    }

    public function testReviewerField()
    {

        $user = new UserVO();
        $user->setLogin('Hermes');

        $this->VO->setReviewer($user);

        $this->assertEquals($this->VO->getReviewer(), $user);

        $user->setLogin('Leela');

        $this->VO->setReviewer($user);

        $this->assertEquals($this->VO->getReviewer(), $user);

    }

    public function testDeveloperField()
    {

        $user = new UserVO();
        $user->setLogin('Hermes');

        $this->VO->setDeveloper($user);

        $this->assertEquals($this->VO->getDeveloper(), $user);

        $user->setLogin('Leela');

        $this->VO->setDeveloper($user);

        $this->assertEquals($this->VO->getDeveloper(), $user);

    }

    public function testToDoField()
    {

        $this->VO->setToDo(25.5);

        $this->assertEquals($this->VO->getToDo(), 25.5);

        $this->VO->setToDo(5);

        $this->assertEquals($this->VO->getToDo(), 5);

    }

    public function testSpentField()
    {

        $this->VO->setSpent(15.5);

        $this->assertEquals($this->VO->getSpent(), 15.5);

        $this->VO->setSpent(2);

        $this->assertEquals($this->VO->getSpent(), 2);

    }

}
?>
