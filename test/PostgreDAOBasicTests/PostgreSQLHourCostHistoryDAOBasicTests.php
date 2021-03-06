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


include_once(PHPREPORT_ROOT . '/model/vo/HourCostHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/HourCostHistoryDAO/PostgreSQLHourCostHistoryDAO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');

class PostgreSQLHourCostHistoryDAOBasicTests extends PHPUnit_Framework_TestCase
{

    protected $dao;
    protected $testObjects;
    protected $auxDao;
    protected $auxObject;

    protected function setUp()
    {

        $this->auxDao = new PostgreSQLUserDAO();

        $this->auxObject = new UserVO();
        $this->auxObject->setLogin("bender");
        $this->auxObject->setPassword("kiss my metal shiny ass");

        $this->auxDao->create($this->auxObject);

        $this->dao = new PostgreSQLHourCostHistoryDAO();

        $this->testObjects[0] = new HourCostHistoryVO();
        $this->testObjects[0]->setId(-1);
        $this->testObjects[0]->setInitDate(date_create("1999-12-31"));
        $this->testObjects[0]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[0]->setUserId($this->auxObject->getId());
        $this->testObjects[0]->setHourCost(2);

    }

    protected function tearDown()
    {
        foreach($this->testObjects as $testObject)
            $this->dao->delete($testObject);

        $this->auxDao->delete($this->auxObject);

    }

    public function testCreate()
    {

        $this->assertEquals($this->dao->create($this->testObjects[0]), 1);

    }

    public function testDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->assertEquals($this->dao->delete($this->testObjects[0]), 1);

    }

    public function testIdCreate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));

        $this->dao->create($this->testObjects[1]);

        $this->assertGreaterThan($this->testObjects[0]->getId(), $this->testObjects[1]->getId());

    }

    public function testGetById()
    {

        $this->dao->create($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testGetByIdNonExistent()
    {

        $read = $this->dao->getById(0);

        $this->assertNull($read);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByIdInvalid()
    {

        $read = $this->dao->getById("zoidberg");

    }

    public function testGetAll()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[1] = clone $this->testObjects[0];

        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));

        $this->dao->create($this->testObjects[1]);

        $this->testObjects[2] = clone $this->testObjects[0];

        $this->testObjects[2]->setInitDate(date_create("2001-12-31"));

        $this->dao->create($this->testObjects[2]);

        $this->assertEquals($this->testObjects, $this->dao->getAll());

    }

    public function testGetByUserId()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $this->testObjects[2] = new HourCostHistoryVO();
        $this->testObjects[2]->setId(-1);
        $this->testObjects[2]->setInitDate(date_create("2001-12-31"));
        $this->testObjects[2]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setHourCost(5);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByUserId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByUserIdVoid()
    {

        $read = $this->dao->getByUserId($this->auxObject->getId());

        $this->assertEquals(sizeof($read), 0);

    }

    public function testGetCurrentByUserId()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $this->testObjects[2] = new HourCostHistoryVO();
        $this->testObjects[2]->setId(-1);
        $this->testObjects[2]->setInitDate(date_create("2001-12-31"));
        $this->testObjects[2]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setHourCost(5);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getCurrentByUserId($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects[1]);

    }

    public function testGetByIntervals1()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $this->testObjects[2] = new HourCostHistoryVO();
        $this->testObjects[2]->setId(-1);
        $this->testObjects[2]->setInitDate(date_create("2001-12-31"));
        $this->testObjects[2]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setHourCost(3);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByIntervals(date_create("1999-11-31"), date_create("3000-12-31"));

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByIntervals2()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2199-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $auxObject = new UserVO();
        $auxObject->setLogin("flexo");
        $auxObject->setPassword("kiss my metal shiny ass");

        $this->auxDao->create($auxObject);

        $testObject = new HourCostHistoryVO();
        $testObject->setId(-1);
        $testObject->setInitDate(date_create("2949-12-31"));
        $testObject->setEndDate(date_create("2999-12-31"));
        $testObject->setUserId($auxObject->getId());
        $testObject->setHourCost(3);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($testObject);

        $read = $this->dao->getByIntervals(date_create("2000-11-31"), date_create("2900-12-31"));

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($testObject);

        $this->auxDao->delete($auxObject);

    }

    public function testGetByIntervals3()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $this->testObjects[2] = new HourCostHistoryVO();
        $this->testObjects[2]->setId(-1);
        $this->testObjects[2]->setInitDate(date_create("3000-12-31"));
        $this->testObjects[2]->setEndDate(date_create("3999-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setHourCost(3);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->dao->getByIntervals(date_create("2999-12-31"), date_create("3000-12-31"));

        $this->assertEquals($read, $this->testObjects);

    }

    public function testGetByIntervalsUserId()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2199-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $auxObject = new UserVO();
        $auxObject->setLogin("flexo");
        $auxObject->setPassword("kiss my metal shiny ass");

        $this->auxDao->create($auxObject);

        $testObject = new HourCostHistoryVO();
        $testObject->setId(-1);
        $testObject->setInitDate(date_create("2949-12-31"));
        $testObject->setEndDate(date_create("2999-12-31"));
        $testObject->setUserId($auxObject->getId());
        $testObject->setHourCost(3);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($testObject);

        $read = $this->dao->getByIntervals(date_create("2000-11-31"), date_create("3000-12-31"), $this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

        $this->dao->delete($testObject);

        $this->auxDao->delete($auxObject);

    }

    /**
      * @expectedException SQLIncorrectTypeException
      */
    public function testGetByIntervalsUserIdInvalid()
    {

        $this->dao->getByIntervals(date_create("2000-11-31"), date_create("3000-12-31"), "zoidberg");

    }

    public function testGetByUserIdFromUser()
    {

        $this->testObjects[1] = new HourCostHistoryVO();
        $this->testObjects[1]->setId(-1);
        $this->testObjects[1]->setInitDate(date_create("2000-12-31"));
        $this->testObjects[1]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[1]->setUserId($this->auxObject->getId());
        $this->testObjects[1]->setHourCost(3);

        $this->testObjects[2] = new HourCostHistoryVO();
        $this->testObjects[2]->setId(-1);
        $this->testObjects[2]->setInitDate(date_create("2001-12-31"));
        $this->testObjects[2]->setEndDate(date_create("2999-12-31"));
        $this->testObjects[2]->setUserId($this->auxObject->getId());
        $this->testObjects[2]->setHourCost(5);

        $this->dao->create($this->testObjects[0]);
        $this->dao->create($this->testObjects[1]);
        $this->dao->create($this->testObjects[2]);

        $read = $this->auxDao->getHourCostHistory($this->auxObject->getId());

        $this->assertEquals($read, $this->testObjects);

    }

    public function testDeleteNonExistent()
    {

        $this->assertEquals($this->dao->delete($this->testObjects[0]), 0);

    }

    public function testGetByIdAfterDelete()
    {

        $this->dao->create($this->testObjects[0]);

        $this->dao->delete($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertNull($read);

    }

    public function testUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setEndDate(NULL);

        $this->assertEquals($this->dao->update($this->testObjects[0]), 1);

    }

    public function testGetByIdAfterUpdate()
    {

        $this->dao->create($this->testObjects[0]);

        $this->testObjects[0]->setEndDate(NULL);

        $this->dao->update($this->testObjects[0]);

        $read = $this->dao->getById($this->testObjects[0]->getId());

        $this->assertEquals($read, $this->testObjects[0]);

    }

    public function testUpdateNonExistent()
    {

        $this->assertEquals($this->dao->update($this->testObjects[0]), 0);

    }

}
?>
