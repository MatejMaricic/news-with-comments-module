<?php

namespace Inchoo\NewsWithComments\Controller\Comment;

use Inchoo\NewsWithComments\Api\Data\CommentsInterface;
use Inchoo\NewsWithComments\Api\Data\NewsInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Save extends Action
{
    /**
     * @var \Inchoo\NewsWithComments\Api\Data\CommentsInterfaceFactory
     */
    private $commentsInterfaceFactory;
    /**
     * @var \Inchoo\NewsWithComments\Api\CommentsRepositoryInterface
     */
    private $commentsRepository;

    private $resultPageFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Inchoo\NewsWithComments\Api\Data\CommentsInterfaceFactory $commentsInterfaceFactory,
        \Inchoo\NewsWithComments\Api\CommentsRepositoryInterface $commentsRepository,
        \Magento\Framework\App\Request\Http\Proxy $request,
        \Magento\Customer\Model\Session\Proxy $session
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->commentsInterfaceFactory = $commentsInterfaceFactory;
        $this->commentsRepository = $commentsRepository;
        $this->request = $request;
        $this->session = $session;
    }

    public function execute()
    {
        $params = $this->request->getPost();
        if ($params[CommentsInterface::COMMENT_CONTENT] && $this->session->isLoggedIn()) {
            $status = $this->commentsRepository->saveComment($params);
            if ($status) {
                $this->messageManager->addSuccessMessage("Comment added, waiting admin approval");
                return $this->_redirect('news/index/index/', ['id'=>$params[NewsInterface::NEWS_ID]]);
            } else {
                $this->messageManager->addErrorMessage('Something Went Wrong');
                return $this->_redirect('/');
            }
        }
        $this->messageManager->addErrorMessage("missing required field");
        return $this->_redirect('news/index/index/', ['id'=>$params[NewsInterface::NEWS_ID]]);
    }
}
