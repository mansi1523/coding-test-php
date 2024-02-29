<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * UsersController class
 */
class UsersController extends AppController
{

    /**
     * beforeFilter function
     *
     * @param \Cake\Event\EventInterface $event
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login']);
    }
    
    /**
     * login function
     *
     * @return void
     */
    public function login()
    {
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $json = [
                'message' => 'You\'re Successfully Logged in!',
                'status' => 'success',
            ];
        } else {
            $this->response = $this->response->withStatus(401);
            $json = [
                'message' => 'Unauthorized!',
                'status' => 'error',
            ];
        }
        $this->set(compact('json'));
        $this->viewBuilder()->setOption('serialize', 'json');
    }

    /**
     * logout function
     *
     * @return void
     */
    public function logout()
    {
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            $result = [
                'message' => 'Successfully Logged out!',
                'status' => 'success',
            ];
            $this->set(compact('result'));
            $this->viewBuilder()->setOption('serialize', 'result');
            return;
        }
    }
}
