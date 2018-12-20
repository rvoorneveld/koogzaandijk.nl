<?php

class TeamController extends KZ_Controller_Action
{

    public function indexAction()
    {
        if (true === empty($team = $this->getParam('team'))) {
            $this->redirect(ROOT_URL);
            exit;
        }

        $this->view->assign(compact('team'));
    }

}
