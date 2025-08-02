# ProductTags


## Development Journey & Learning Approach
Previously I had some prior professional experience with Magento. Although that experience wasn't extensive in terms of mastering the full learning curve of the framework, it provided a useful foundation. In my opinion, developers primarily learn through the tasks they work on and the real-world challenges they face during development. The tasks I encountered while working on this module helped me revisit and reinforce my understanding of the Magento framework quite effectively. Below, I’ll briefly walk you through the full journey of the development process for this module:


- **Installation:** 
 Although I had previously created a custom script for Magento installation, this time I decided to follow the recommended Docker-based approach. During the setup, I encountered a 403 error when trying to access the frontend. After several attempts, I discovered that the issue was caused by an incorrect Nginx configuration path. With the help of Gemini 2.5, I identified the problem and resolved it by applying the following command:


  ```bash
  docker cp src/nginx.conf magento-app-1:/var/www/html/nginx.conf
  ```
- **Module Installation:**
  After completing the installation, I began developing the module by creating the core files: module.xml and registration.php. I was already familiar with module installation, having learned how to install modules from Mageplaza early in my Magento journey. However, this time I encountered an unusual issue — the module wasn’t getting listed. It was quite frustrating at first, but after some research and assistance from AI tools, I discovered that the PHP-FPM container wasn’t running. Restarting it resolved the issue. The root cause was that the entire module directory wasn’t being recognized, which prevented the module from being installed.


- **Schema Setup:**
   Previously, I mostly built database schemas using the InstallSchema approach. Although I was aware of db_schema.xml, I hadn’t used it in practice until working on this module. For this project, I decided to use db_schema.xml, referring to the Magento DevDocs for guidance and exploring a few additional resources to better understand the process. I also generated the db_schema_whitelist.json file using the whitelist command to help track and manage declarative schema changes. Overall, the process went smoothly, and I found this approach more efficient than using InstallSchema, as it eliminates the need for additional files like UpgradeSchema when handling schema updates.


- **Product Tag Form Field:**
   After completing the schema setup, my next goal was to work on the product edit form. I started by googling "how to add fields to product edit form in Magento 2" and came across a helpful Mageplaza article. Following the steps in that documentation, I implemented most of the required form modifications. During the process, I learned that the parent module for this functionality should be Magento_Catalog, since we're essentially extending the product_form. The rest of the field configuration followed standard Magento form syntax.</br>
Next came the important part: implementing the plugin and observer. I already had some prior experience with observers, so I used one to save the product tags after a product is saved. The observer listens to the catalog_product_save_after event. It performs a few key operations: first, it retrieves the tag data from the strativ_tags field, then it removes any existing tags for the product to prevent duplication, and finally, it inserts the updated tags. I also added custom validation and sanitization logic for the tag input to ensure data integrity.</br>
The plugin I developed was responsible for displaying the existing product tags when editing a product. It retrieves tag data from the database using the TagRepositoryInterface. However, while implementing the plugin, I ran into a blocker—I couldn't fetch the tag data. After debugging, I realized I had missed registering the plugin in di.xml. Once I fixed that, everything worked as expected. The tag data was saved correctly to the database and displayed properly in the product edit form.</br>


- **Show Tags in the Product Details Page:**
   In short, the layout file catalog_product_view.xml(extended from Magento Catalog) creates an instance of the Tags block. The block then fetches tags for that product ID. Finally it returns an array of tag names. I was familiar with the process and it was a pretty straightforward process. Still there were few docs I explored to learn the proper way.


- **Tag View Page:**
   After that, I made the tags clickable by generating dynamic URLs using $block->getUrl(), which leverages Magento's built-in URL generation system. I passed the route URL into this method, which includes the route name and the lowercase folder structure of the controller—something I recalled from my previous experience.</br>
The corresponding layout file needed to follow Magento’s naming conventions. In this case, it was named producttags_tag_view.xml, matching the route. This layout file connects the Tag\View block with the tag/view.phtml template. Within the block, I queried the database to fetch products whose entity_id matched the product_id associated with the tag. Later, I refactored this logic using the Repository Design Pattern, which I’ll explain later in this document.</br>
During this part of the implementation, I ran into a frustrating blocker caused by a simple mistake. I had matched the IDs correctly, and even verified the query using dd(), which returned the expected results. However, the products were still not showing up on the frontend. After spending hours debugging, I finally discovered that the issue was due to the product quantity being set to 0. The logic was correct—the data just wasn't meeting the visibility condition due to stock status.</br>




- **Admin Grid for Product Tags**
   First, I created a menu.xml file to add the menu item under Catalog → Inventory, linking it to the producttags/tag/index action. This route points to the Index controller inside the Adminhtml directory, which in turn loads the producttags_tag_index.xml layout file.</br>
Within this layout, the ui_component responsible for rendering the grid is linked. It’s crucial to follow Magento’s naming conventions for all XML files—any mismatch can result in the layout not loading properly. Additionally, the data providers used by the grid are configured and linked through di.xml.


- **Refactoring (Dependency Injection and RepositoryPattern)**
   This was my favorite part of the development process. At this stage, I refactored most of the business logic out of the blocks and into dedicated repository classes. I created segregated interfaces inside the Api directory and linked them to their corresponding repository implementations using di.xml. The blocks then consumed these interfaces to execute the necessary logic. This is actually the cleanest way I could build the functionalities. Because it serves the Repository design pattern. I have separated interfaces because they serve different purposes. This codebase maintains Open/Closed Principle, Interface Segregation and Dependency Inversion from SOLID principle. Also tried to use in-code documentation in most of the functions and kept the code as clean as possible.
 - **Validation and Tag Sanitization**
   Used some custom validation for the tag field and feedback for each case. Used MessageManager->addWarningMessage for the feedback.


- **Other Challenges**
   I faced a few other challenges during development as well. At the time, I was using Ubuntu 20 on my PC, and some PHP 8.3 extensions were causing compatibility issues. The Linux CLI even recommended upgrading the OS. However, after upgrading to Ubuntu 22, my entire system crashed. I ended up having to reinstall the full dual-boot setup from scratch. On top of that, my Linux installation wasn't on an SSD, so the magento commands were running painfully slow which was extremely frustrating. Despite all these setbacks, I pushed through and managed to build the module successfully.


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
php bin/magento module:enable Strativ_ProductTags
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush