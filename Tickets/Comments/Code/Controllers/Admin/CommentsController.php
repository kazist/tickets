<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of CommentsController
 *
 * @author sbc
 */

namespace Tickets\Tickets\Comments\Code\Controllers\Admin;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;

class CommentsController extends BaseController {
    public function saveAction($form_data = '') {
       
        $form_data = $this->request->get('form');
        
        $this->model->save($form_data);
        
        return $this->redirectToRoute('admin.tickets.tickets.detail', array('id'=>$form_data['ticket_id']));
    }
}
