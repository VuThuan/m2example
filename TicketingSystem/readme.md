#  To Create Backend Admin Grid   In Magento 2

Magento 2 creation of admin grid is quite different from Magento 1. In this article we will see how we can create Magento 2 admin grid. Admin Grids. As you are aware Magento 2 Grids are kind of table which lists the items of your database table and provide you some common features like sort, filter, delete, update item, etc.
The below diagram show roughly the activities involved in creating admin grid. Further in the Article these all are described in detail.
For the purpose of this article we will create simple Ticketing system admin grid which shows the support tickets created by customers (The next article covers where logged in customer can raise a ticket, and also see his previous created tickets in List)

![](docs/adminMenu.png)
![](docs/ticketList.png)
![](docs/ticketEdit.png)
![](docs/ticketFrontend.png)

## Goal
- Create   Module
- Define Admin routes
- Create entries in menu.xml to Display your custom menus in Admin Panel
- Create Controller
- Define Database schema through Installer
- Create Model
- Use admin Grid Components to Create Grid UI.

## Create Module Step By Step Tutorials

- app/code/Bdcrops/TicketingSystem/registration.php
- app/code/Bdcrops/TicketingSystem/etc/module.xml
- app/code/Bdcrops/TicketingSystem/etc/adminhtml/routes.xml
- app/code/Bdcrops/TicketingSystem/etc/adminhtml/menu.xml
- app/code/Bdcrops/TicketingSystem/Controller/Adminhtml/Tickets/Index.php
- app/code/Bdcrops/TicketingSystem/Setup/InstallSchema.php
- app/code/Bdcrops/TicketingSystem/Model/Tickets.php
- app/code/Bdcrops/TicketingSystem/Model/ResourceModel/Tickets.php
- app/code/Bdcrops/TicketingSystem/Model/ResourceModel/Tickets/Collection.php
- app/code/Bdcrops/TicketingSystem/etc/di.xml
- app/code/Bdcrops/TicketingSystem/view/adminhtml/layout/ticketingsystem_tickets_index.xml
- app/code/Bdcrops/TicketingSystem/view/adminhtml/ui_component/ticketingsystem_tickets_listing.xml
-


- TicketingSystem/Block/Adminhtml/Module/Grid/Renderer/Action/UrlBuilder.php
 - TicketingSystem/Block/Adminhtml/Tickets/Add.php
 - TicketingSystem/Block/Adminhtml/Tickets/Edit/Form.php
 - TicketingSystem/Block/Create.php
 - TicketingSystem/Block/Manage.php
 - TicketingSystem/Controller/Adminhtml/Tickets/Add.php
 - TicketingSystem/Controller/Adminhtml/Tickets/Index.php
 - TicketingSystem/Controller/Adminhtml/Tickets/Save.php
 - TicketingSystem/Controller/Create/Index.php
 - TicketingSystem/Controller/Manage/Index.php
 - TicketingSystem/Model/ResourceModel/Tickets.php
 - TicketingSystem/Model/ResourceModel/Tickets/Collection.php
 - TicketingSystem/Model/Source/Category.php
 - TicketingSystem/Model/Source/Priority.php
 - TicketingSystem/Model/Source/Status.php
 - TicketingSystem/Model/Tickets.php
 - TicketingSystem/Setup/InstallSchema.php
 - TicketingSystem/Ui/Component/Listing/Column/Actions.php
 - TicketingSystem/Ui/Component/Listing/Column/Update.php
 - TicketingSystem/docs/adminMenu.png
 - TicketingSystem/docs/ticketEdit.png
 - TicketingSystem/docs/ticketFrontend.png
 - TicketingSystem/docs/ticketList.png
 - TicketingSystem/etc/adminhtml/menu.xml
 - TicketingSystem/etc/adminhtml/routes.xml
 - TicketingSystem/etc/di.xml
 - TicketingSystem/etc/frontend/routes.xml
 - TicketingSystem/view/adminhtml/layout/ticketingsystem_tickets_add.xml
 - TicketingSystem/view/adminhtml/layout/ticketingsystem_tickets_index.xml
 - TicketingSystem/view/adminhtml/ui_component/ticketingsystem_tickets_listing.xml




## Ref
 - [magentodeveloper](https://magentodeveloper.in/magento-2-admin-grid.html)
 - [KtreeOpenSource](https://github.com/KtreeOpenSource/Magento2Examples)
