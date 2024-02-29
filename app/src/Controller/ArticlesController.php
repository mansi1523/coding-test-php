<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class ArticlesController extends AppController
{
    /**
     * @param \Cake\Event\EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $articles = $this->paginate($this->Articles, ['order' => ['created_at' => 'DESC']])->toArray();
        $artIDs = [];

        if (count($articles) <= 0) {
            $this->set(compact('articles'));
            $this->viewBuilder()->setOption('serialize', ['articles']);
            return;
        } else {
            foreach ($articles as $article) {
                $artIDs[] = $article->id;
            }
        }

        // Get the ArticleLikes count for each article
        $likesCounts = $this->Articles->ArticleLikes->find()
            ->select(['art_id', 'likes_count' => 'COUNT(*)'])
            ->where(['art_id IN' => $artIDs])
            ->group('art_id')
            ->toArray();

        foreach ($articles as $article) {
            $article->likes_count = $likesCounts[$article->id]->likes_count ?? 0;
        }

        $this->set(compact('articles'));
        $this->viewBuilder()->setOption('serialize', ['articles']);
    }
    
    /**
     * add function
     *
     * @return Response|null
     */
    public function add() {
        $article = $this->Articles->newEmptyEntity();

        $this->request->allowMethod('post');
        $article = $this->Articles->patchEntity($article, $this->request->getData());

        if ($article->getErrors()) {
            $result = [
                'message' => 'The article could not be saved.',
                'errors' => $article->getErrors(),
            ];
            $this->set(compact('result'));
            $this->viewBuilder()->setOption('serialize', 'result');
            return;
        }

        $result = [];
        try {
            $article->user_id = $this->Authentication->getIdentityData('id');
            $this->Articles->save($article);
            $result = [
                'message' => 'The article has been saved!',
                'article' => $article,
            ];
        } catch (\Exception $e) {
            $result = [
                'message' => 'The article could not be saved.',
                'errors' => $e->getMessage(),
            ];
        }

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
    
    /**
     * `view function`
     * Get an Article
     * @param [type] $id
     * @return void
     */
    public function view($id = null)
    {
        $article = $this->Articles->get($id);

        // Get the like count for the article
        $likesCount = $this->Articles->ArticleLikes->find()
            ->select(['likes_count' => 'COUNT(*)'])
            ->where(['art_id' => $article->id])
            ->first();
        $article->likes_count = $likesCount->likes_count ?? 0;

        $this->set(compact('article'));
        $this->viewBuilder()->setOption('serialize', 'article');
    }
    
    /**
     * update function
     *
     * @param [type] $id
     * @return void
     */
    public function update($id = null)
    {
        $artObj = $this->Articles->get($id);

        if ($this->request->is(['post', 'put', 'patch'])) {
            // Check if User is Authorized to Update
            $this->isAuthorized($artObj->user_id);

            $artObj = $this->Articles->patchEntity($artObj, $this->request->getData());
            if ($artObj->getErrors()) {
                $result = [
                    'message' => 'The article could not be saved.',
                    'status' => 'error',
                    'errors' => $artObj->getErrors(),
                ];
                $this->set(compact('result'));
                $this->viewBuilder()->setOption('serialize', 'result');
                return;
            }

            $result = [];
            try {
                $this->Articles->save($artObj);
                $result = [
                    'message' => 'The article has been saved!',
                    'status' => 'success',
                    'article' => $artObj,
                ];
            } catch (\Exception $e) {
                $result = [
                    'message' => 'The article could not be saved.',
                    'status' => 'error',
                    'errors' => $e->getMessage(),
                ];
            }
            $this->set(compact('result'));
            $this->viewBuilder()->setOption('serialize', 'result');
        }
    }
    
    /**
     * delete function
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $artObj = $this->Articles->get($id);

        // Check if User is Authorized to Delete
        $this->isAuthorized($artObj->user_id);

        $result = [];
        try {
            $this->Articles->delete($artObj);
            $result = [
                'message' => 'The article has been deleted!',
                'status' => 'success',
            ];
        } catch (\Exception $e) {
            $result = [
                'message' => 'The article could not be deleted.',
                'status' => 'error',
                'errors' => $e->getMessage(),
            ];
        }

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', 'result');
    }
    
    public function isAuthorized($artUserID)
    {
        $uID = $this->Authentication->getIdentityData('id');

        if ($uID != $artUserID) throw new UnauthorizedException('You are Unauthorized!');

        return true;
    }
}
