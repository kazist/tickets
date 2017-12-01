<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

namespace Tickets\Tickets\Comments\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Email\Email;

/**
 * Description of MarketplaceModel
 *
 * @author sbc
 */
class CommentsModel extends BaseModel {

    public function save($form = '') {

        $factory = new KazistFactory();

        $id = parent::save($form);

        if ($id) {

            $this->saveImage($id);

            $comment = parent::getRecord($id);

            if ($comment->is_support) {
                $this->sendEmailToOwner($form, $comment, $id);
            } else {
                $this->sendEmailToDepartment($form, $comment, $id);
            }
        }

        return $id;
    }

    public function saveImage($comment_id) {

        $factory = new KazistFactory();

        $media_ids = $factory->uploadMedia('form.attachment', 'tickets.tickets.comments', $comment_id);

        $media_id = $media_ids[0];

        if ($media_id) {

            $data = new \stdClass();
            $data->id = $comment_id;
            $data->attachment = $media_id;

            $factory->saveRecord('#__tickets_tickets_comments', $data);
        }
    }

    public function sendEmailToOwner($form, $comment, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        $ticket = $factory->getRecord('#__tickets_tickets', 'tt', array('id=:id'), array('id' => $comment->ticket_id));

        if ($id && !$form['id']) {

            $comment = ($comment != '') ? $comment : parent::getRecord($id);

            $member_query = $factory->getQueryBuilder('#__users_users', 'uu', array('id=:id'), array('id' => $ticket->created_by));
            $members = $member_query->loadObjectList();

            foreach ($members as $member) {

                $parameters = array();
                $parameters['user'] = $member;
                $parameters['comment'] = $comment;

                $email->sendDefinedLayoutEmail('tickets.tickets.owner.comment', $member->email, $parameters);
            }
        }
    }

    public function sendEmailToDepartment($form, $comment, $id) {

        $email = new Email();
        $factory = new KazistFactory();

        $ticket = $factory->getRecord('#__tickets_tickets', 'tt', array('id=:id'), array('id' => $comment->ticket_id));

        if ($id && !$form['id']) {

            $comment = ($comment != '') ? $comment : parent::getRecord($id);

            $member_query = $factory->getQueryBuilder('#__tickets_teams', 'tt');
            $member_query->andWhere('tt.department_id=' . (int) $ticket->department_id . ' OR tt.department_id = 0 OR tt.department_id IS NULL  OR tt.department_id = \'\'');
            $member_query->andWhere('tt.published=1');
            $members = $member_query->loadObjectList();

            foreach ($members as $member) {

                $parameters = array();
                $parameters['user'] = $member;
                $parameters['comment'] = $comment;

                $email->sendDefinedLayoutEmail('tickets.tickets.support.comment', $member->email, $parameters);
            }
        }
    }

}
