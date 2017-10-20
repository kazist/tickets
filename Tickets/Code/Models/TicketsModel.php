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

        $factory = new KazistFactory();

        $user = $factory->getUser();

        if (!WEB_IS_ADMIN) {

            $query = parent:: appendSearchQuery($query);

            if ($user->id) {
                $query->where('tt.created_by=' . $user->id);
            }

            $query->orderBy('tt.id ', 'DESC');

            return $query;
        }
    }

    public function save($form = '') {

        $factory = new KazistFactory();

        $id = parent::save($form);

        if ($id) {

            $ticket = parent::getRecord($id);

            if ($factory->getSetting('tickets_tickets_admin_send_email')) {
                $this->sendEmailToAdmin($form, $ticket, $id);
            }
            
            $this->sendEmailToDepartment($form, $ticket, $id);
        }

        return $id;
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

                $email->sendDefinedLayoutEmail('tickets.tickets.admin.added', $member->email, $parameters, null, 1);
            }
        }
    }

    public function sendEmailToDepartment($form, $ticket, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        if ($id && !$form['id']) {

            $ticket = ($ticket != '') ? $ticket : parent::getRecord($id);

            $member_query = $factory->getQueryBuilder('#__tickets_teams', 'tt', array('department_id=' . $ticket->department_id));
            $members = $member_query->loadObjectList();

            foreach ($members as $member) {

                $parameters = array();
                $parameters['user'] = $member;
                $parameters['ticket'] = $ticket;

                $email->sendDefinedLayoutEmail('tickets.tickets.department.team.added', $member->email, $parameters, null, 1);
            }
        }
    }

}
