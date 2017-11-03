<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of TicketsController
 *
 * @author sbc
 */

namespace Tickets\Tickets\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;
use Kazist\Controller\BaseController;

class TicketsController extends BaseController {

    public function indexAction($offset = 0, $limit = 10) {

        $factory = new KazistFactory();

        $tickets = $this->model->getRecords();

        $user = $factory->getUser();

        if ($user->id && count($tickets)) {
            return parent::indexAction($offset, $limit);
        } else {
            $msg = "Please Add Your Ticket.";
            $factory->enqueueMessage($msg, 'info');
            return $this->redirectToRoute('tickets.tickets.add');
        }
    }

    public function saveAction($form_data = '') {

        $has_error = false;
        $factory = new KazistFactory();

        $session = $this->container->get('session');
        $session_form = $session->get('session_form');

        if (empty($form_data)) {
            $form_data = $this->request->get('form');
        }

        $session->set('session_form', $form_data);

        if ($form_data['title'] == '') {
            $msg = "Title is a required field.";
            $factory->enqueueMessage($msg, 'error');
            $has_error = true;
        }

        if ($form_data['description'] == '') {
            $msg = "Description is a required field.";
            $factory->enqueueMessage($msg, 'error');
            $has_error = true;
        }

        if ($form_data['email'] == '') {
            $msg = "Email is a required field.";
            $factory->enqueueMessage($msg, 'error');
            $has_error = true;
        }

        if ($form_data['username'] == '') {
            $msg = "Username is a required field.";
            $factory->enqueueMessage($msg, 'error');
            $has_error = true;
        }

        if ($has_error) {
            return $this->redirectToRoute('tickets.tickets.add');
        } else {

            $session->remove('session_form');

            return parent::saveAction($form_data);
        }
    }

}
