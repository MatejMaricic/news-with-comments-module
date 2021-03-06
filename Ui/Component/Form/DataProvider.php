<?php

namespace Inchoo\NewsWithComments\Ui\Component\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{

    /**
     * @param string                                                              $name
     * @param string                                                              $primaryFieldName
     * @param string                                                              $requestFieldName
     * @param \Inchoo\NewsWithComments\Model\ResourceModel\News\CollectionFactory $collectionFactory
     * @param array                                                               $meta
     * @param array                                                               $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Inchoo\NewsWithComments\Model\ResourceModel\News\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataObject = $this->getCollection()->getFirstItem();

        if ($dataObject->getId()) {
            $data[$dataObject->getId()] = $dataObject->toArray();
        }
        return $data;
    }
}
