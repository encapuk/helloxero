<?php
/**
 * Created By : Mikhail Glushenko, Encap Systems, 2022
 */

namespace Encap\HelloXero\Controller\Adminhtml\Export;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
//use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

use Magento\Framework\App\Filesystem\DirectoryList;
//use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\File\Csv as vCsv;

class Csv extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{

    public function __construct(Context $context, Filter $filter,
                                                  CollectionFactory $collectionFactory,
                                                  CustomerFactory $customerFactory,
                                                  AddressFactory $addressFactory,
                                                  CountryFactory $countryFactory,
                                                  TimezoneInterface $timezone,
                                                  DirectoryList $directoryList,
                                                  FileFactory $fileFactory,
                                                  vCsv $csvProcessor)
    {

        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->countryFactory = $countryFactory;
        $this->timezone = $timezone;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->fileFactory = $fileFactory;
        parent::__construct($context, $filter);
    }

    protected function massAction(AbstractCollection $collection)
    {
       $orderIds = $collection->getAllIds(); // Get the selected orders

       if (empty($orderIds)) {
           $this->getMessageManager()->addErrorMessage(__('No orders found.'));
           return $this->_redirect('sales/order/index');
       }

       //var_dump($orderIds);
       $orderCollection = $this->collectionFactory->create();
       $orderCollection->addFieldToFilter('entity_id', ['in' => $orderIds]);

       try {

           $content[0] = array('*ContactName','EmailAddress','POAddressLine1','POAddressLine2','POAddressLine3','POAddressLine4','POCity','PORegion','POPostalCode','POCountry','*InvoiceNumber','Reference','*InvoiceDate','*DueDate','Total','InventoryItemCode','*Description','*Quantity','*UnitAmount','Discount','*AccountCode','*TaxType','TaxAmount','TrackingName1','TrackingOption1','TrackingName2','TrackingOption2','Currency','BrandingTheme');

           foreach ($orderCollection as $order) {
             $line = $this->getCSVdata($order);
             if ($line) $content[]=$line;
           }

       } catch (\Exception $e) {
           $message = "An unknown error occurred while changing selected orders.";
           $this->getMessageManager()->addErrorMessage(__($message . $e));
       }

       $this->downloadCSV($content);
       //return $this->_redirect('sales/order/index');
       //die("ok");
    }


    public function getCSVdata($order_model) {
          $status = $order_model->getStatus();  //check if not cancelled or closed
          $deny_statuses = array("Cancelled", "Closed"); //explode(',', Mage::getStoreConfig('xero/invoice/status'));
          //if (!in_array($status, $deny_statuses) && $status != null) { return false; }
          if ($status == "closed" || $status == "canceled") { return false; }
          //$this->messageManager->addSuccess(__($status));

          $customer = $this->customerFactory->create()->load($order_model->getCustomerId());

          $first_name = $order_model->getCustomerFirstname();
          $last_name = $order_model->getCustomerLastname();
          $address = $order_model->getBillingAddress();

          if (!$first_name)
          {
              $first_name = $address->getFirstname();
              $last_name = $address->getLastname();
          }
          $line = array(trim($first_name). ' ' .trim($last_name));

          if ((strpos($order_model->getShippingDescription(), 'Int') !== false)  ||  ($customer->getGroupId() == 5)){
              $tx_type = "No VAT";
          }
          else{
              $tx_type = "20% (VAT on Income)";
          }

          $date = $this->timezone->date(new \DateTime($order_model->getCreatedAt()));
          $country = $this->countryFactory->create()->loadByCode($address->getCountryId())->getName();

          array_push($line,
                     $address->getData('email'),
                     $address->getStreetLine(1),
                     $address->getStreetLine(2),
                     "", "",
                     $address->getData('city'),
                     $address->getData('region'),
                     $address->getData('postcode'),
                     $country,
                     "", //invoice number  i.e. INV-0001
                     $order_model->getIncrementId(),
                     $date->format('d/m/Y'),
                     $date->modify('+60 days')->format('d/m/Y'),
                     //(new DateTime($order_model->getCreatedAt()))->format('d/m/Y'),
                     //(new DateTime($order_model->getCreatedAt()))->modify($this->due_date)->format('d/m/Y'),
                     $order_model->getGrandTotal(),
                     $order_model->getAllItems()[0]->getSku(),
                     $order_model->getAllItems()[0]->getName(),
                     "1",
                     $order_model->getGrandTotal(),
                     "",
                     "200",
                     $tx_type,
                     "", //tax amount  i.e.  auto calculated
                     "","","","",
                     "GBP"
                     );

          return $line;

        }

        public function downloadCSV($content7) {

          $fileName = 'xero_file.csv';
          $filePath =  $this->directoryList->getPath(DirectoryList::MEDIA) . "/" . $fileName;

          $this->csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData($filePath, $content7);
          return $this->fileFactory->create(
              $fileName,
              [
                  'type'  => "filename",
                  'value' => $fileName,
                  'rm'    => true, // True => File will be removed from directory after download.
              ],
              DirectoryList::MEDIA,
              'text/csv',
              null
          );



          }



    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('RH_Helloworld::exportcsv');
    }
}
