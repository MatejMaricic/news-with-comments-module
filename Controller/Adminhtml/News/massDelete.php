<?php

namespace Inchoo\NewsWithComments\Controller\Adminhtml\News;

use Magento\Backend\App\Action;

class massDelete extends Action
{
    /**
     * @var \Inchoo\NewsWithComments\Api\NewsRepositoryInterface
     */
    private $newsRepository;
    /**
     * @var \Inchoo\NewsWithComments\Model\ResourceModel\News\CollectionFactory
     */
    private $newsCollectionFactory;

    public function __construct(
        Action\Context $context,
        \Inchoo\NewsWithComments\Api\NewsRepositoryInterface $newsRepository,
        \Inchoo\NewsWithComments\Model\ResourceModel\News\CollectionFactory $newsCollectionFactory
    ) {
        parent::__construct($context);
        $this->newsRepository = $newsRepository;
        $this->newsCollectionFactory = $newsCollectionFactory;
    }

    public function execute()
    {
        if ($ids = $this->_request->getParam('selected')) {
            $collection = $this->newsCollectionFactory
                ->create()
                ->addFieldToFilter('news_id', $ids);

            foreach ($collection as $news) {
                try {
                    $this->newsRepository->delete($news);
                } catch (\Exception $exception) {
                    $this->messageManager->addErrorMessage('Could Not Delete News');
                    return $this->_redirect('news/news');
                }
            }
            $this->messageManager->addSuccessMessage('Selected News Deleted');
            return $this->_redirect('news/news');
        }

        return $this->_redirect('news/news');
    }
}