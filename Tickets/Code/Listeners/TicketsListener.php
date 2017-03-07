<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

namespace Tickets\Tickets\Code\Listeners;

defined('KAZIST') or exit('Not Kazist Framework');

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kazist\KazistFactory;
use Kazist\Event\CRUDEvent;
use Kazist\Service\System\System;
use Kazist\Service\Database\Query;
use Kazist\Service\Email\Email;

class TicketsListener implements EventSubscriberInterface {

    public $container = '';

    public function onCommentAferSave(CRUDEvent $event) {

        global $sc;
        $this->container = $sc;

        $system = new System();
        $comment = $event->getRecord();

        $subset_id = $comment->getSubsetId();

        $required_subset_id = $system->getSubsetId('tickets/tickets');

        if ($required_subset_id == $subset_id) {
            $this->sendEmailToTicketOwner($comment);
        }
    }

    public function sendEmailToTicketOwner($comment) {

        $email = new Email();
        $factory = new KazistFactory();

        $comment_id = $comment->getId();
        $ticket_id = $comment->getRecordId();

        $query = $factory->getQueryBuilder('#__tickets_tickets', 'tt', array('ticket_id=:ticket_id'), array('ticket_id' => $ticket_id));
        $ticket = $query->loadObject();

        $query = $factory->getQueryBuilder('#__notification_comments', 'nc', array('id=:id'), array('id' => $comment_id));
        $new_comment = $query->loadObject();

        $parameters = array('ticket' => $ticket, 'comment' => $new_comment);
        $email->sendDefinedLayoutEmail('tickets.tickets.owner.comment.added', $new_comment->email, $parameters);

        $this->sendEmailToAdmin($ticket, $new_comment);
    }

    public function sendEmailToAdmin($ticket, $comment) {

        $email = new Email();
        $factory = new KazistFactory();

        $member_query = $factory->getQueryBuilder('#__users_users', 'uu', array('is_admin=1'));
        $members = $member_query->loadObjectList();

        foreach ($members as $member) {
            $parameters = array('ticket' => $ticket, 'comment' => $comment);
            $email->sendDefinedLayoutEmail('tickets.tickets.admin.comment.added', $member->email, $parameters);
        }
    }

    public static function getSubscribedEvents() {
        return array(
            'notification.comments.after.save' => 'onCommentAferSave',
        );
    }

}
