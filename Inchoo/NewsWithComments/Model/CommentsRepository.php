<?php

namespace Inchoo\NewsWithComments\Model;

use Inchoo\NewsWithComments\Api\CommentsRepositoryInterface;
use Inchoo\NewsWithComments\Api\Data\CommentsInterface;
use Inchoo\NewsWithComments\Api\Data\CommentsSearchResultsInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CommentsRepository implements CommentsRepositoryInterface
{
    /**
     * @var \Inchoo\NewsWithComments\Api\Data\CommentsInterfaceFactory
     */
    private $commentsModelFactory;
    /**
     * @var ResourceModel\Comments
     */
    private $commentsResource;
    /**
     * @var ResourceModel\Comments\CollectionFactory
     */
    private $commentsCollectionFactory;
    /**
     * @var \Inchoo\NewsWithComments\Api\Data\CommentsSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        \Inchoo\NewsWithComments\Api\Data\CommentsInterfaceFactory $commentsModelFactory,
        \Inchoo\NewsWithComments\Model\ResourceModel\Comments $commentsResource,
        \Inchoo\NewsWithComments\Model\ResourceModel\Comments\CollectionFactory $commentsCollectionFactory,
        \Inchoo\NewsWithComments\Api\Data\CommentsSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->commentsModelFactory = $commentsModelFactory;
        $this->commentsResource = $commentsResource;
        $this->commentsCollectionFactory = $commentsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Retrieve comments.
     *
     * @param int $commentsId
     * @return \Inchoo\NewsWithComments\Api\Data\CommentsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($commentsId)
    {
        $comments = $this->commentsModelFactory->create();
        $this->commentsResource->load($comments, $commentsId);
        if (!$comments->getId()) {
            throw new NoSuchEntityException(__('Comment with id "%1" does not exist.', $commentsId));
        }
        return $comments;
    }

    /**
     * Save news.
     *
     * @param \Inchoo\NewsWithComments\Api\Data\CommentsInterface $comments
     * @return \Inchoo\NewsWithComments\Api\Data\CommentsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CommentsInterface $comments)
    {
        try {
            $this->commentsResource->save($comments);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $comments;
    }

    /**
     * Delete news.
     *
     * @param \Inchoo\NewsWithComments\Api\Data\CommentsInterface $comments
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CommentsInterface $comments)
    {
        try {
            $this->commentsResource->delete($comments);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    /**
     * Retrieve news matching the specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Inchoo\NewsWithComments\Api\Data\CommentsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Inchoo\NewsWithComments\Model\ResourceModel\Comments\Collection $collection */
        $collection = $this->commentsCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CommentsSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param $foreignKey
     * @return mixed
     */
    public function getByForeignKey($foreignKey)
    {
        $comment = $this->commentsModelFactory->create();
        $this->commentsResource->load($comment, $foreignKey, 'main_id');
        if ($comment->getForeignKey() != $foreignKey) {
            return false;
        }
        return $comment;
    }
}
