# ProductTags

## Development Journey & Learning Approach 
Previously I had some prior professional experience with Magento. Although that experience wasn't extensive in terms of mastering the full learning curve of the framework, it provided a useful foundation. In my opinion, developers primarily learn through the tasks they work on and the real-world challenges they face during development. The tasks I encountered while working on this module helped me revisit and reinforce my understanding of the Magento framework quite effectively. Below, I’ll briefly walk you through the full journey of the development process for this module: 

- **Installation:**  
  Although I had a custom script for Magento installation, I followed the recommended Docker-based approach this time. While setting things up, I faced a 403 error when trying to access the frontend. After several attempts, I realized it was due to an incorrect Nginx config path. With the help of Gemini 2.5, I identified and applied the fix using the following command:

  ```bash
  docker cp src/nginx.conf magento-app-1:/var/www/html/nginx.conf 
  ```
 
 - **Module Installation:** 
   After installing successfully I started developing the module and started creating core files of module.xml and registration.php. I was familier with module installation before. I learned installing module from Mageplaza at the very beggining of my Magento journey. But this time I faced something unusual. It was not getting listed for some reason. It was really frustating then after some queries to web and ai assistant I restarted the php fpm image which was not running. It resolved the issue. So the total module directory did not get listed  which made the module unable to install.

- **Schema Setup:**
    Previously I built schemas with InstallSchema process most of the times. Back then I was familier with db_schema but did not use it. I used db_schema for this module. Scrolled the devdocs and gained idea from it. Also visited few other sites and ended up using ai assistant to try out the db_schema process. Also generated db_schema_whitelist to track and manage declarative schema changes via the whitelist command. It went smoothly. And I think it is more better approach than using InstallSchema because we dont need another file like UpgradeSchema due to schema changes.

- **Product Tag Form Field:**
    After the schema setup my target was the form. So I googled "how to add field to product edit form in magento 2" and got a nice documentation of MazePlaza. According to the documentation I did almost everything. Due the process I got to know Parent module of this module should be Magento Catelog bacause we are basically extending the product_form. Rest of the field syntaxes are the same as the other. Now comes the important part. The plugin and observer. I had previous idea about observer. I used observer to save product tag after the product is saved. It basically listens to the catalog_product_save_after event. During this process it does few things. First it retrieves tag data from the product's strativ_tags field then it cleans up old tags by deleting existing entries for the product. I also added some custom validations and sanitizations for the tag field. The plugin I added it's main purpose was to handle the display of existing product tags when editing a product. It retrieves existing tags from the database for each product using TagRepositoryInterface.
    During the implementation of Plugin I faced a blocker. I could not fetch the tag data. Actually what happened I missed the part to add it in the di.xml. Actually these blockers help us reduce the mistake in the future. After adding these data was getting saved to the database properly as per requirement. And saved data was visible in the edit form.

-  **Show Tags in the Product Details Page:**
    In short the layout file catalog_product_view.xml(extended from Magento Catalog) creates an instance of the Tags block. The block then fetch tags for that product ID. Finally it returns an array of tag names. I was familier with the process and it was pretty straight forward process. Still there were few docs I explored to learn the proper way. And used AI Assistant to evaluate the process although it was pretty basic steps.

-  **Tag View Page:**
    Then I made the tags clickable. I used the code $block->getUrl() which generates the URL using Magento's URL generation system. Used the route url into it. Route url contains routename and folder structure of controller in lowercase. I know it from my previous experience. Naming convention of the layout file should be named according to the route url. In our case it was producttags_tag_view.xml. This layout file basically connects the Tag/View block with the tag/view template. In the block we query from the database and bring the matched product_id with the entity_id in the product table. Later it was refactored with the repository design pattern which I will explain later in this doc. Here I faced a blocker by a silly mistake(for hours). I matched the ids and it was okay. I dd()'d the query it was okay. Everything was fine but the products were not visible in the frontend. After hours of trying I figured out the quantity of the product was 0 and my logic was okay.

-  **Admin Grid for Product Tags**
    First I created menu.xml to show it under the Catalog->Inventory and linked to producttags/tag/index action. The index controller of adminhtml leads to producttags_tag_index.xml layout. Inside the layout the ui_component responsible for the grid is linked. Naming conventions of each xml files are important otherwise it will not work. And data providers responsible for the grid is linked inside the di.xml.

-  **Refactoring (Dependency Injection and RepositoryPattern)**
    My most favourite part. In this step I transfered most of my business logics from blocks to repository. Inside Api directory created segregated interfaces. inside the di.xml I linked the interface and repository. Then inside block used these interfaces to apply the logic. This is actually the cleanest way I could build the functionalities. Cause it serves Repository design pattern. I have separated interfaces because they serve different purpose. This codebase maintains Open/Closed Principle, Interface Segregation and Dependency Inversion from SOLID principle. Also tried to use in-code documentation in most the functions and kept the code as clean as possible.
  
-  **Validation and Tag Sanitization**
    Used some custom validation for the tag field and feedbacks for each cases. Used MessageManager->addWarningMessage for the feedbacks.

-  **Other Challenges**
    I have faced some other challenges during the development. Ubuntu 20 was installed in my pc. Some of the php 8.3 extensions were causing problems. Linux cli recommended me to upgrade the os. After the upgrading to 22 my whole system creashed. I reinstalled the full dual boot setup. Also my linux was not installed in SSD so my commands ran too slow which was extremely frustating. But I overcomed the challenges and built it anyway.

## Helpful Articles
- [Magento 2 DevDocs](https://developer.adobe.com/commerce/)
- [Get Product ID](https://magefan.com/blog/get-product-by-id-magento-2?srsltid=AfmBOorOhosp4zy07Wl_VfTlclhcoKEV-0o2LV52f6egTqZVEGjecmr0)
- [Add Custom Fields In the Product Edit Page](https://www.mageplaza.com/devdocs/how-to-add-custom-fields-in-product-edit-pages-in-magento-2.html)
- [Admin Menu Create](https://www.mageplaza.com/devdocs/create-admin-menu-magento-2.html)
- [Configure declarative schema](https://developer.adobe.com/commerce/php/development/components/declarative-schema/configuration/)

## Installation Steps

### 1. Download the Module

Download the module files from the [GitHub repository](https://github.com/alhussain50/ProductTags). You can either clone the repository or download the ZIP file and extract it.

### 2. Upload the Module to Your Magento Root Directory

Create the following directory structure in your Magento root: app/code/Strativ/ProductTags.</br>
Copy the downloaded module files into this directory. After copying, the directory structure should look like this:

app/code/Alhussain/ProductTags/</br>
├── etc/</br>
│   ├── module.xml</br>
│   └── ...</br>
├── view/</br>
│   └── ...</br>
└── registration.php</br>

### 3. Enable the Module and Run Magento Commands

Open your terminal or command prompt, navigate to your Magento root directory, and run the following commands in order:

```bash
php bin/magento module:enable Alhussain_ProductTags
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush