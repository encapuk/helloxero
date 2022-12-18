# HelloXero
This Magento 2 module helps to export selected orders to XERO CSV-formated file.

This will create a CSV file in your Downloads folder. After downloading the data, you can import the file into Xero.


Magento 2 Xero Integration Extension allows you to integrate your Magento 2 Online Store with Xero Accounting Application. The extension makes it easy to pulling order data to a CSV format, which can be imported to XERO.


*** How to install ***

1. Copy files to your app folder
2. Run bin/magento setup:upgrade  &&  bin/magento ca:cl


*** Usage ***

1. Go to your Sales/Orders page in Magento 2
2. Select orders you wish to transfer to XERO
3. Select "XERO CSV" in the MassAction menu 
4. A .csv fille will be downloaded to your Downloads folder
5. Open the file in your CSV editing app
6. Insert Invoice numbers according to your XERO invoice nubering sequence in collumn K (*InvoiceNumber)

*** Tweaking ***

Edit "Controller/Adminhtml/Export/Csv.php" line 102-138 to adjust static parameters such as Currency, Account Code and Tax Type

The current logic sets "20% (VAT on Income)" for all orders, except if Shipping Description contains "Int" (i.e. International) or customer group is 5 (i.e. the group has non-vatable customes) - in which case the tax code is "No VAT" 

The extension is Free and open source (GPL)
Any questions or suggestions please email to hello@encap.uk
