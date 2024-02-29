<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ArticleLikesController class
 */
class ArticleLikesController extends AppController
{
    /**
     * like function
     *
     * @return void
     */
    public function like()
    {
        $this->request->allowMethod('post');
        $req = $this->request->getData();
        $artID = $req['art_id'];
        $uID = $this->Authentication->getIdentityData('id');

        $likeExists = $this->ArticleLikes->find()
            ->where([
                'user_id' => $uID,
                'art_id' => $artID
            ])
            ->first();

        // Check if already Liked the Article
        if ($likeExists) {
            $res = [
                'message' => 'You\'ve already liked this article!',
                'status' => 'error',
            ];
            $this->set(compact('res'));
            $this->setResponse($this->getResponse()->withStatus(400));
            $this->viewBuilder()->setOption('serialize', ['res']);
            return;
        }

        $likeObj = $this->ArticleLikes->newEmptyEntity();
        $likeObj = $this->ArticleLikes->patchEntity($likeObj, [
            'user_id' => $uID,
            'art_id' => $artID,
        ]);

        // Store Article Like
        if ($this->ArticleLikes->save($likeObj)) {
            $result = [
                'message' => 'The like has been saved!',
                'status' => 'success',
            ];

            $this->set(compact('result'));
            $this->viewBuilder()->setOption('serialize', ['result']);
        } else {
            $result = [
                'message' => 'The like could not be saved.',
                'status' => 'error',
                'errors' => $likeObj->getErrors(),
            ];

            $this->setResponse($this->getResponse()->withStatus(400));
            $this->set(compact('result'));
            $this->viewBuilder()->setOption('serialize', ['result']);
        }
    }
}
