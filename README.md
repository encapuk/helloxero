# HelloXero
This Magento 2 module helps to export selected orders to XERO CSV-formated file. It has been tested with Magento 2.4.x versions

Magento 2 Xero Integration Extension allows you to integrate your Magento 2 Online Store with Xero Accounting Application. The extension makes it easy pulling order data saving it to CSV format, which can be imported to XERO.


*** How to install ***

1. Copy files to your app/code folder
2. Execute the following code:
bin/magento maintenance:enable
bin/magento module:enable Encap_HelloXero
bin/magento setup:upgrade   
(for production mode only) bin/magento setup:di:compile 
(for production mode only) bin/magento setup:static-content:deploy 
bin/magento indexer:reindex
bin/magento ca:fl
bin/magento maintenance:disable

*** Usage ***

1. Go to your Sales/Orders page in Magento 2
2. Select orders you wish to transfer to XERO
3. Select "XERO CSV" in the MassAction menu 
4. A .csv fille will be downloaded to your Downloads folder
5. Open the file in your CSV editing app
6. Insert Invoice numbers according to your XERO invoice nubering sequence in collumn K (*InvoiceNumber)

*** Tweaking ***

Edit "Controller/Adminhtml/Export/Csv.php" line 102-138 to adjust static parameters such as Currency, Account Code and Tax Type

The current logic sets "20% (VAT on Income)" for all orders, except if Shipping Description contains "Int" (i.e. International) or customer group is 5 (e.g. the group with non-vatable customes) - in which case the tax code is "No VAT" 

The extension is Free and open source (GPL)
Any questions or suggestions please email to hello@encap.uk
