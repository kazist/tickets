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
            $msg="Please Add Your Ticket.";
            $factory->enqueueMessage($msg,'info');
          return  $this->redirectToRoute('tickets.tickets.add');
        }
    }

}
