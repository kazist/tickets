<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

namespace Tickets\Tickets\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;
use Search\Indexes\Code\Classes\ContentIndexing;
use Kazist\Service\Database\Query;

/**
 * Description of MarketplaceModel
 *
 * @author sbc
 */
class TicketsModel extends BaseModel {

    public function appendSearchQuery($query) {

        $document = $this->container->get('document');

        $factory = new KazistFactory();
        $user = $factory->getUser();

        $query = parent:: appendSearchQuery($query);

        if (!empty($this->search)) {
            $search = $this->search;
        } else {
            $search = $document->search;
        }

        $createdrange_arr = explode(' - ', $search['createdrange']);

        if ($search['createdrange'] <> '' && !empty($createdrange_arr)) {
            $search['date_created']['start'] = date('Y-m-d 00:00:00', strtotime($createdrange_arr[0]));
            $search['date_created']['end'] = date('Y-m-d 23:59:59', strtotime($createdrange_arr[1]));
        }

        if (!WEB_IS_ADMIN) {


            if ($user->id) {
                $query->where('tt.created_by=' . $user->id);
            }

            $query->orderBy('tt.id ', 'DESC');

            return $query;
        }
    }

    public function getCategoriesJson() {

        $categories_arr = array();

        $factory = new KazistFactory();

        $query = $factory->getQueryBuilder('tickets_categories', 'tc');
        $query->andWhere('tc.published=1');
        $categories = $query->loadObjectList();

        foreach ($categories as $key => $category) {
            $categories_arr[] = array('value' => $category->id, 'text' => $category->title);
        }

        return $categories_arr;
    }

    public function getDepartmentsJson() {

        $departments_arr = array();

        $factory = new KazistFactory();

        $query = $factory->getQueryBuilder('tickets_departments', 'td');
        $query->andWhere('td.published=1');
        $departments = $query->loadObjectList();

        foreach ($departments as $key => $department) {
            $departments_arr[] = array('value' => $department->id, 'text' => $department->title);
        }

        return $departments_arr;
    }

    public function getTeamsJson() {

        $teams_arr = array();

        $factory = new KazistFactory();

        $query = $factory->getQueryBuilder('tickets_teams', 'tt');
        $teams = $query->loadObjectList();

        foreach ($teams as $key => $team) {
            $teams_arr[] = array('value' => $team->id, 'text' => $team->name);
        }

        return $teams_arr;
    }

    public function getComments($ticket_id = '') {

        $factory = new KazistFactory();

        if ($ticket_id == '') {
            $ticket_id = $this->request->get('id');
        }

        $query = $factory->getQueryBuilder('tickets_tickets_comments', 'ttc', array('ttc.ticket_id=:ticket_id'), array('ticket_id' => $ticket_id));
        $query->addSelect('uu.name, uu.username, uu.email');
        $query->addOrderBy('ttc.id','ASC');
        $comments = $query->loadObjectList();

        return $comments;
    }

    public function save($form = '') {

        $factory = new KazistFactory();

        $id = parent::save($form);

        if ($id) {

            $this->saveImage($id);

            $ticket = parent::getRecord($id);

            if ($factory->getSetting('tickets_tickets_admin_send_email')) {
                $this->sendEmailToAdmin($form, $ticket, $id);
            }

            $this->sendEmailToDepartment($form, $ticket, $id);
            $this->sendEmailToOwner($form, $ticket, $id);
        }

        return $id;
    }

    public function saveImage($ticket_id) {

        $factory = new KazistFactory();

        $media_ids = $factory->uploadMedia('form.attachment', 'tickets.tickets', $ticket_id);

        $media_id = $media_ids[0];

        if ($media_id) {

            $data = new \stdClass();
            $data->id = $ticket_id;
            $data->attachment = $media_id;

            $factory->saveRecord('#__tickets_tickets', $data);
        }
    }

    public function sendEmailToAdmin($form, $ticket, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        if ($id && !$form['id']) {

            $ticket = ($ticket != '') ? $ticket : parent::getRecord($id);

            $member_query = $factory->getQueryBuilder('#__users_users', 'uu', array('is_admin=1'));
            $members = $member_query->loadObjectList();

            foreach ($members as $member) {

                $parameters = array();
                $parameters['user'] = $member;
                $parameters['ticket'] = $ticket;

                $email->sendDefinedLayoutEmail('tickets.tickets.admin.added', $member->email, $parameters);
            }
        }
    }

    public function sendEmailToOwner($form, $ticket, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        if ($id && !$form['id']) {

            $ticket = ($ticket != '') ? $ticket : parent::getRecord($id);

            $parameters = array();
            $parameters['ticket'] = $ticket;

            $email->sendDefinedLayoutEmail('tickets.tickets.owner.added', $ticket->email, $parameters);
        }
    }

    public function sendEmailToDepartment($form, $ticket, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        if ($id && !$form['id']) {

            $ticket = ($ticket != '') ? $ticket : parent::getRecord($id);

            $member_query = $factory->getQueryBuilder('#__tickets_teams', 'tt');
            $member_query->andWhere('tt.department_id=' . (int) $ticket->department_id . ' OR tt.department_id = 0 OR tt.department_id IS NULL  OR tt.department_id = \'\'');
            $member_query->andWhere('tt.published=1');
            $members = $member_query->loadObjectList();

            foreach ($members as $member) {

                $parameters = array();
                $parameters['user'] = $member;
                $parameters['ticket'] = $ticket;

                $email->sendDefinedLayoutEmail('tickets.tickets.department.team.added', $member->email, $parameters);
            }
        }
    }

}
