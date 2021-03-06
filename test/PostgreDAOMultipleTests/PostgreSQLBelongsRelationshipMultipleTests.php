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


include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserGroupDAO/PostgreSQLUserGroupDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/PostgreSQLBelongsDAO.php');

class PostgreSQLBelongsRelationshipMultipleTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $auxDao;
    protected $testObjects;
    protected $auxDao2;
    protected $testObjects2;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLUserDAO();

        $this->testObjects[0] = new UserVO();
        $this->testObjects[0]->setLogin("bender");
        $this->testObjects[0]->setId(-1);

        $this->testObjects[1] = new UserVO();
        $this->testObjects[1]->setLogin("flexo");
        $this->testObjects[1]->setId(-1);

        $this->testObjects[2] = new UserVO();
        $this->testObjects[2]->setLogin("roberto");
        $this->testObjects[2]->setId(-1);

        $this->auxDao->create($this->testObjects[0]);

        $this->auxDao2 = new PostgreSQLUserGroupDAO();

        $this->testObjects2[0] = new UserGroupVO();
        $this->testObjects2[0]->setName("Deliverers");
        $this->testObjects2[0]->setId(-1);

        $this->testObjects2[1] = new UserGroupVO();
        $this->testObjects2[1]->setName("Cleaning");
        $this->testObjects2[1]->setId(-1);

        $this->testObjects2[2] = new UserGroupVO();
        $this->testObjects2[2]->setName("Burocrats");
        $this->testObjects2[2]->setId(-1);

        $this->auxDao2->create($this->testObjects2[0]);

        $this->dao = new PostgreSQLBelongsDAO();

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject1)
            foreach($this->testObjects2 as $testObject2)
                $this->dao->delete($testObject1->getId(), $testObject2->getId());

        foreach($this->testObjects as $testObject)
            $this->auxDao->delete($testObject);

        foreach($this->testObjects2 as $testObject)
            $this->auxDao2->delete($testObject);

    }

    public function testCreate()
    {

        $this->assertEquals($this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 1);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testCreateId1Invalid()
    {

        $this->dao->create("*", $this->testObjects2[0]->getId());

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testCreateId2Invalid()
    {

        $this->dao->create($this->testObjects[0]->getId(), "*");

    }

    public function testDelete()
    {

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());

        $this->assertEquals($this->dao->delete($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 1);

     }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testDeleteId1Invalid()
    {

        $this->dao->delete("*", $this->testObjects2[0]->getId());

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testDeleteId2Invalid()
    {

        $this->dao->delete($this->testObjects[0]->getId(), "*");

    }

    public function testDeleteNonExistent()
    {

        $this->assertEquals($this->dao->delete($this->testObjects[0]->getId(), $this->testObjects2[0]->getId()), 0);

    }

    public function testGetByUserId()
    {

        $this->auxDao2->create($this->testObjects2[1]);
        $this->auxDao2->create($this->testObjects2[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[1]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[2]->getId());

        $read = $this->dao->getByUserId($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects2);

    }

    public function testGetByUserLogin()
    {

        $this->auxDao2->create($this->testObjects2[1]);
        $this->auxDao2->create($this->testObjects2[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[1]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[2]->getId());

        $read = $this->dao->getByUserLogin($this->testObjects[0]->getLogin());

        $this->assertEquals($read, $this->testObjects2);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidUserId()
    {

        $this->dao->getByUserId("*");

    }

    public function testGetByUserIdFromUserDAO()
    {

        $this->auxDao2->create($this->testObjects2[1]);
        $this->auxDao2->create($this->testObjects2[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[1]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[2]->getId());

        $read = $this->auxDao->getGroups($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects2);

    }

    public function testGetByUserLoginFromUserDAO()
    {

        $this->auxDao2->create($this->testObjects2[1]);
        $this->auxDao2->create($this->testObjects2[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[1]->getId());
        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[2]->getId());

        $read = $this->auxDao->getGroupsByLogin($this->testObjects[0]->getLogin());

        $this->assertEquals($read, $this->testObjects2);

    }

    public function testGetByUserGroupId()
    {

        $this->auxDao->create($this->testObjects[1]);
        $this->auxDao->create($this->testObjects[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[1]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[2]->getId(), $this->testObjects2[0]->getId());

        $read = $this->dao->getByUserGroupId($this->testObjects2[0]->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByUserGroupName()
    {

        $this->auxDao->create($this->testObjects[1]);
        $this->auxDao->create($this->testObjects[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[1]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[2]->getId(), $this->testObjects2[0]->getId());

        $read = $this->dao->getByUserGroupName($this->testObjects2[0]->getName());

        $this->assertEquals($read, $this->testObjects);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByInvalidUserGroupId()
    {

        $this->dao->getByUserGroupId("*");

    }

    public function testGetByUserGroupIdFromUserGroupDAO()
    {

        $this->auxDao->create($this->testObjects[1]);
        $this->auxDao->create($this->testObjects[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[1]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[2]->getId(), $this->testObjects2[0]->getId());

        $read = $this->auxDao2->getUsers($this->testObjects2[0]->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByUserGroupNameFromUserGroupDAO()
    {

        $this->auxDao->create($this->testObjects[1]);
        $this->auxDao->create($this->testObjects[2]);

        $this->dao->create($this->testObjects[0]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[1]->getId(), $this->testObjects2[0]->getId());
        $this->dao->create($this->testObjects[2]->getId(), $this->testObjects2[0]->getId());

        $read = $this->auxDao2->getUsersByUserGroupName($this->testObjects2[0]->getName());

        $this->assertEquals($read, $this->testObjects);

    }

}
?>
