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

use Kazist\Controller\BaseController;

class TicketsController extends BaseController {

    public function detailAction($id = '', $slug = '') {

        $this->data_arr['comments'] = $this->model->getComments();

        return parent::detailAction($id, $slug);
    }

}
