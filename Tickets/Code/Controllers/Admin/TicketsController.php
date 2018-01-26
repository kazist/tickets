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

namespace Tickets\Tickets\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\KazistFactory;
use Kazist\Controller\BaseController;

class TicketsController extends BaseController {

    public function indexAction($offset = 0, $limit = 10) {
        
        $this->data_arr['categoryinput'] = $this->model->getCategoriesJson();
        $this->data_arr['departmentinput'] = $this->model->getDepartmentsJson();
        $this->data_arr['assigned_toinput'] = $this->model->getTeamsJson();
        $this->data_arr['show_action'] = false;

        return parent::indexAction($offset, $limit);
    }

    public function statusAction() {

        $id = $this->request->get('id');
        $action = $this->request->get('action');

        $factory = new KazistFactory();

        if ($action <> '') {
            $data = new \stdClass();
            $data->id = $id;
            $data->is_closed = ($action == 'close') ? 1 : 0;

            $factory->saveRecord('#__tickets_tickets', $data);
        }

        return $factory->redirectToRoute('admin.tickets.tickets.detail', array('id' => $id));
    }

    public function detailAction($id = '', $slug = '') {

        $factory = new KazistFactory();

        $this->data_arr['return_url'] = $factory->generateUrl('admin.tickets.tickets.detail', array('id' => $id));
        $this->data_arr['category_arr'] = $this->model->getCategoriesJson();
        $this->data_arr['department_arr'] = $this->model->getDepartmentsJson();
        $this->data_arr['team_arr'] = $this->model->getTeamsJson();
        $this->data_arr['comments'] = $this->model->getComments();

        return parent::detailAction($id, $slug);
    }

}
