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


/** File for GetProjectUsersAction
 *
 *  This file just contains {@link GetProjectUsersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');


/** Get Project Users Action
 *
 *  This action is used for retrieving all Users related to a Project through relationship UserProject.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectUsersAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Users we want to retieve.
     *
     * @var int
     */
    private $projectId;

    /** GetProjectUsersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $projectId the id of the Project whose Users we want to retieve.
     */
    public function __construct($projectId) {
        $this->projectId=$projectId;
        $this->preActionParameter="GET_PROJECT_USERS_PREACTION";
        $this->postActionParameter="GET_PROJECT_USERS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Users from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

        return $dao->getUsersProject($this->projectId);

    }

}


/*//Test code;

$action= new GetProjectUsersAction(4);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
