# SUPPLY4ME - Enterprise B2B Distribution ERP System
## Complete Architecture Specification

---

## 1. SYSTEM OVERVIEW

SUPPLY4ME is a production-ready Enterprise B2B Distribution ERP System designed for scalability, maintainability, and extensibility.

### Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.4 |
| Database | MySQL 8, Redis |
| Queue | Laravel Queues (Redis driver) |
| Frontend | InertiaJS, Vue 3, Pinia, TailwindCSS |
| Auth | Laravel Sanctum, Spatie Permission |
| Audit | Spatie Activity Log |
| Files | Laravel Media Library |
| Export | Laravel Excel |
| Testing | Pest PHP |
| PWA | Service Workers, Manifest |

### Scalability Targets
- 100,000+ customers
- Millions of orders
- Multiple warehouses, branches, and companies
- Future mobile application support

---

## 2. DOMAIN MODULES (Bounded Contexts)

### Module List

| # | Module | Bounded Context | Aggregate Root |
|---|--------|----------------|----------------|
| 1 | Core | Authentication, Users, Roles, Permissions | User |
| 2 | Companies | Multi-tenant company management | Company |
| 3 | Branches | Branch/location management | Branch |
| 4 | Customers | B2B customer management | Customer |
| 5 | Suppliers | Supplier/vendor management | Supplier |
| 6 | Products | Product catalog & categories | Product |
| 7 | Catalog | Price lists, promotions, discounts | PriceList |
| 8 | Inventory | Stock management & movements | StockItem |
| 9 | Warehouses | Warehouse management | Warehouse |
| 10 | Orders | Sales order management | Order |
| 11 | PurchaseOrders | Purchase order management | PurchaseOrder |
| 12 | Payments | Payment processing | Payment |
| 13 | Invoicing | Invoice generation & management | Invoice |
| 14 | Receiving | Goods receiving & putaway | GoodsReceivedNote |
| 15 | PickingPacking | Pick list & packing management | PickList |
| 16 | Shipping | Shipment management | Shipment |
| 17 | Delivery | Delivery & route management | Delivery |
| 18 | Drivers | Driver management | Driver |
| 19 | Reporting | Analytics & reports | Report |
| 20 | Settings | System configuration | Setting |
| 21 | Notifications | Notification management | Notification |
| 22 | Media | Document & file management | Media |

### Module Dependency Graph

```
Shared Kernel (Auth, Core, Notifications, Settings)
    │
    ├── Companies ──▶ Branches ──▶ Warehouses
    │                   │              │
    │                   │              ├──▶ Warehouse Zones
    │                   │              └──▶ Warehouse Bins
    │                   │
    ├── Users ──▶ User Branches
    │
    ├── Customers ──▶ Customer Contacts
    │       │     ──▶ Customer Shipping Addresses
    │       │     ──▶ Customer Price Lists
    │       │     ──▶ Customer Notes
    │       │
    ├── Suppliers ──▶ Supplier Products
    │
    ├── Products ──▶ Product Categories
    │       │     ──▶ Product Brands
    │       │     ──▶ Product Units
    │       │     ──▶ Product Variants
    │       │     ──▶ Product Images
    │
    ├── Catalog ──▶ Price Lists
    │           ──▶ Price List Items
    │           ──▶ Promotions
    │
    ├── Inventory ──▶ Stock Items
    │            ──▶ Stock Movements
    │            ──▶ Stock Adjustments
    │            ──▶ Stock Transfers
    │
    ├── Orders ──▶ Order Items
    │         ──▶ Order Status History
    │
    ├── PurchaseOrders ──▶ Purchase Order Items
    │
    ├── Payments ──▶ Payment Allocations
    │          ──▶ Payment Receipts
    │
    ├── Invoicing ──▶ Invoice Items
    │           ──▶ Invoice Status History
    │
    ├── Receiving ──▶ Goods Received Notes
    │           ──▶ GRN Items
    │
    ├── PickingPacking ──▶ Pick Lists
    │               ──▶ Pick List Items
    │               ──▶ Packing Lists
    │               ──▶ Packing List Items
    │
    ├── Shipping ──▶ Shipments
    │          ──▶ Shipment Items
    │          ──▶ Shipping Carriers
    │
    ├── Delivery ──▶ Deliveries
    │          ──▶ Delivery Items
    │          ──▶ Delivery Routes
    │          ──▶ Delivery Route Stops
    │          ──▶ Drivers
    │
    └── Media ──▶ Document History
```

---

## 3. DIRECTORY STRUCTURE

```
supply4me/
├── app/
│   ├── Actions/                    # Action classes (single-responsibility operations)
│   │   ├── Core/
│   │   │   ├── LoginAction.php
│   │   │   ├── RegisterAction.php
│   │   │   └── ResetPasswordAction.php
│   │   ├── Companies/
│   │   │   ├── CreateCompanyAction.php
│   │   │   ├── UpdateCompanyAction.php
│   │   │   └── DeactivateCompanyAction.php
│   │   ├── Customers/
│   │   │   ├── CreateCustomerAction.php
│   │   │   ├── UpdateCustomerAction.php
│   │   │   ├── UpdateCreditStatusAction.php
│   │   │   └── AssignSalesRepAction.php
│   │   ├── Orders/
│   │   │   ├── CreateOrderAction.php
│   │   │   ├── ConfirmOrderAction.php
│   │   │   ├── CancelOrderAction.php
│   │   │   ├── AddOrderItemAction.php
│   │   │   ├── UpdateOrderItemAction.php
│   │   │   ├── RemoveOrderItemAction.php
│   │   │   └── PlaceOrderAction.php
│   │   ├── Payments/
│   │   │   ├── CreatePaymentAction.php
│   │   │   ├── ApprovePaymentAction.php
│   │   │   ├── RejectPaymentAction.php
│   │   │   ├── AllocatePaymentAction.php
│   │   │   └── RefundPaymentAction.php
│   │   ├── Invoicing/
│   │   │   ├── GenerateInvoiceAction.php
│   │   │   ├── SendInvoiceAction.php
│   │   │   └── VoidInvoiceAction.php
│   │   ├── Inventory/
│   │   │   ├── ReserveStockAction.php
│   │   │   ├── ReleaseStockAction.php
│   │   │   ├── AdjustStockAction.php
│   │   │   ├── TransferStockAction.php
│   │   │   └── CountStockAction.php
│   │   ├── Receiving/
│   │   │   ├── CreateGRNAction.php
│   │   │   ├── ReceiveGoodsAction.php
│   │   │   └── CompleteReceivingAction.php
│   │   ├── PickingPacking/
│   │   │   ├── GeneratePickListAction.php
│   │   │   ├── PickItemAction.php
│   │   │   ├── PackOrderAction.php
│   │   │   └── VerifyPackingAction.php
│   │   ├── Shipping/
│   │   │   ├── CreateShipmentAction.php
│   │   │   ├── TrackShipmentAction.php
│   │   │   └── MarkDeliveredAction.php
│   │   ├── Delivery/
│   │   │   ├── AssignDriverAction.php
│   │   │   ├── StartDeliveryAction.php
│   │   │   ├── CompleteDeliveryAction.php
│   │   │   ├── RecordFailedAttemptAction.php
│   │   │   └── CreateDeliveryRouteAction.php
│   │   └── Reports/
│   │       ├── GenerateSalesReportAction.php
│   │       ├── GenerateInventoryReportAction.php
│   │       └── GenerateFinancialReportAction.php
│   │
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerateInvoiceNumbersCommand.php
│   │       ├── CheckOverduePaymentsCommand.php
│   │       ├── SyncStockLevelsCommand.php
│   │       ├── GenerateDailyReportsCommand.php
│   │       └── CleanupExpiredSessionsCommand.php
│   │
│   ├── DTOs/                       # Data Transfer Objects
│   │   ├── Core/
│   │   │   ├── LoginDTO.php
│   │   │   └── UserDTO.php
│   │   ├── Customers/
│   │   │   ├── CreateCustomerDTO.php
│   │   │   └── CustomerFilterDTO.php
│   │   ├── Orders/
│   │   │   ├── CreateOrderDTO.php
│   │   │   ├── OrderItemDTO.php
│   │   │   └── OrderFilterDTO.php
│   │   ├── Payments/
│   │   │   ├── CreatePaymentDTO.php
│   │   │   └── PaymentFilterDTO.php
│   │   └── Reports/
│   │       ├── SalesReportDTO.php
│   │       └── InventoryReportDTO.php
│   │
│   ├── Enums/                      # Enums for all statuses and types
│   │   ├── Core/
│   │   │   ├── UserRole.php
│   │   │   └── UserStatus.php
│   │   ├── Companies/
│   │   │   ├── CompanyStatus.php
│   │   │   └── CompanyType.php
│   │   ├── Customers/
│   │   │   ├── CustomerType.php
│   │   │   ├── CustomerStatus.php
│   │   │   └── CreditStatus.php
│   │   ├── Orders/
│   │   │   ├── OrderStatus.php
│   │   │   ├── OrderType.php
│   │   │   ├── PaymentStatus.php
│   │   │   ├── FulfillmentStatus.php
│   │   │   └── OrderPriority.php
│   │   ├── Payments/
│   │   │   ├── PaymentStatus.php
│   │   │   ├── PaymentMethod.php
│   │   │   └── PaymentType.php
│   │   ├── Invoicing/
│   │   │   ├── InvoiceStatus.php
│   │   │   └── InvoiceType.php
│   │   ├── Inventory/
│   │   │   ├── StockStatus.php
│   │   │   ├── MovementType.php
│   │   │   ├── AdjustmentType.php
│   │   │   └── TransferStatus.php
│   │   ├── Receiving/
│   │   │   ├── GRNStatus.php
│   │   │   └── ItemCondition.php
│   │   ├── PickingPacking/
│   │   │   ├── PickListStatus.php
│   │   │   ├── PickItemStatus.php
│   │   │   └── PackingStatus.php
│   │   ├── Shipping/
│   │   │   ├── ShipmentStatus.php
│   │   │   └── CarrierStatus.php
│   │   ├── Delivery/
│   │   │   ├── DeliveryStatus.php
│   │   │   ├── DriverStatus.php
│   │   │   ├── RouteStatus.php
│   │   │   └── DeliveryCondition.php
│   │   └── Products/
│   │       ├── ProductType.php
│   │       ├── ProductStatus.php
│   │       └── UnitType.php
│   │
│   ├── Events/                     # Domain Events
│   │   ├── Core/
│   │   │   ├── UserCreated.php
│   │   │   ├── UserUpdated.php
│   │   │   └── UserDeactivated.php
│   │   ├── Companies/
│   │   │   ├── CompanyCreated.php
│   │   │   └── CompanyUpdated.php
│   │   ├── Customers/
│   │   │   ├── CustomerCreated.php
│   │   │   ├── CustomerUpdated.php
│   │   │   └── CreditStatusChanged.php
│   │   ├── Orders/
│   │   │   ├── OrderCreated.php
│   │   │   ├── OrderConfirmed.php
│   │   │   ├── OrderCancelled.php
│   │   │   ├── OrderStatusChanged.php
│   │   │   ├── OrderItemAdded.php
│   │   │   ├── OrderItemRemoved.php
│   │   │   └── OrderReadyForPickup.php
│   │   ├── Payments/
│   │   │   ├── PaymentCreated.php
│   │   │   ├── PaymentUploaded.php
│   │   │   ├── PaymentApproved.php
│   │   │   ├── PaymentRejected.php
│   │   │   ├── PaymentCompleted.php
│   │   │   └── PaymentRefunded.php
│   │   ├── Invoicing/
│   │   │   ├── InvoiceGenerated.php
│   │   │   ├── InvoiceSent.php
│   │   │   ├── InvoicePaid.php
│   │   │   ├── InvoiceOverdue.php
│   │   │   └── InvoiceVoided.php
│   │   ├── Inventory/
│   │   │   ├── StockReserved.php
│   │   │   ├── StockReleased.php
│   │   │   ├── StockAdjusted.php
│   │   │   ├── StockTransferred.php
│   │   │   ├── StockLow.php
│   │   │   └── StockOut.php
│   │   ├── Receiving/
│   │   │   ├── GRNCreated.php
│   │   │   ├── GoodsReceived.php
│   │   │   └── GRNCompleted.php
│   │   ├── PickingPacking/
│   │   │   ├── PickListGenerated.php
│   │   │   ├── PickListCompleted.php
│   │   │   ├── OrderPacked.php
│   │   │   └── PackingVerified.php
│   │   ├── Shipping/
│   │   │   ├── ShipmentCreated.php
│   │   │   ├── ShipmentShipped.php
│   │   │   ├── ShipmentDelivered.php
│   │   │   └── ShipmentException.php
│   │   └── Delivery/
│   │       ├── DriverAssigned.php
│   │       ├── DeliveryStarted.php
│   │       ├── DeliveryCompleted.php
│   │       ├── DeliveryFailed.php
│   │       └── DeliveryRescheduled.php
│   │
│   ├── Exceptions/                 # Custom Exceptions
│   │   ├── DomainException.php
│   │   ├── InvalidStateException.php
│   │   ├── InsufficientStockException.php
│   │   ├── PaymentFailedException.php
│   │   ├── UnauthorizedException.php
│   │   └── ValidationException.php
│   │
│   ├── Http/
│   │   ├── Controllers/            # Thin controllers only
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── Auth/
│   │   │   │   │   │   ├── LoginController.php
│   │   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   │   └── ForgotPasswordController.php
│   │   │   │   │   ├── Companies/
│   │   │   │   │   │   └── CompanyController.php
│   │   │   │   │   ├── Customers/
│   │   │   │   │   │   ├── CustomerController.php
│   │   │   │   │   │   ├── CustomerContactController.php
│   │   │   │   │   │   └── CustomerAddressController.php
│   │   │   │   │   ├── Suppliers/
│   │   │   │   │   │   └── SupplierController.php
│   │   │   │   │   ├── Products/
│   │   │   │   │   │   ├── ProductController.php
│   │   │   │   │   │   ├── ProductCategoryController.php
│   │   │   │   │   │   └── ProductBrandController.php
│   │   │   │   │   ├── Orders/
│   │   │   │   │   │   ├── OrderController.php
│   │   │   │   │   │   └── OrderItemController.php
│   │   │   │   │   ├── Payments/
│   │   │   │   │   │   └── PaymentController.php
│   │   │   │   │   ├── Invoices/
│   │   │   │   │   │   └── InvoiceController.php
│   │   │   │   │   ├── Inventory/
│   │   │   │   │   │   ├── StockController.php
│   │   │   │   │   │   ├── StockAdjustmentController.php
│   │   │   │   │   │   └── StockTransferController.php
│   │   │   │   │   ├── Receiving/
│   │   │   │   │   │   └── GRNController.php
│   │   │   │   │   ├── PickingPacking/
│   │   │   │   │   │   ├── PickListController.php
│   │   │   │   │   │   └── PackingListController.php
│   │   │   │   │   ├── Shipping/
│   │   │   │   │   │   └── ShipmentController.php
│   │   │   │   │   ├── Delivery/
│   │   │   │   │   │   ├── DeliveryController.php
│   │   │   │   │   │   ├── DriverController.php
│   │   │   │   │   │   └── DeliveryRouteController.php
│   │   │   │   │   ├── Reports/
│   │   │   │   │   │   └── ReportController.php
│   │   │   │   │   └── Settings/
│   │   │   │   │       └── SettingController.php
│   │   │   │   └── Traits/
│   │   │   │       └── ApiResponser.php
│   │   │   └── Inertia/
│   │   │       ├── DashboardController.php
│   │   │       ├── ProfileController.php
│   │   │       └── SettingsController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureCompanyIsActive.php
│   │   │   ├── SetCurrentBranch.php
│   │   │   ├── CheckPermission.php
│   │   │   └── LogActivity.php
│   │   │
│   │   └── Requests/               # Form Request Validation
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php
│   │       │   └── RegisterRequest.php
│   │       ├── Customers/
│   │       │   ├── StoreCustomerRequest.php
│   │       │   ├── UpdateCustomerRequest.php
│   │       │   └── CustomerFilterRequest.php
│   │       ├── Orders/
│   │       │   ├── StoreOrderRequest.php
│   │       │   ├── UpdateOrderRequest.php
│   │       │   ├── AddOrderItemRequest.php
│   │       │   └── OrderFilterRequest.php
│   │       ├── Payments/
│   │       │   ├── StorePaymentRequest.php
│   │       │   └── PaymentFilterRequest.php
│   │       ├── Products/
│   │       │   ├── StoreProductRequest.php
│   │       │   └── UpdateProductRequest.php
│   │       └── Reports/
│   │           ├── SalesReportRequest.php
│   │           └── InventoryReportRequest.php
│   │
│   ├── Listeners/                  # Event Listeners
│   │   ├── Core/
│   │   │   ├── SendWelcomeEmail.php
│   │   │   └── LogUserActivity.php
│   │   ├── Orders/
│   │   │   ├── ReserveStockForOrder.php
│   │   │   ├── SendOrderConfirmation.php
│   │   │   ├── NotifyWarehouseOfOrder.php
│   │   │   └── GenerateOrderInvoice.php
│   │   ├── Payments/
│   │   │   ├── UpdateOrderPaymentStatus.php
│   │   │   ├── UpdateInvoicePaymentStatus.php
│   │   │   ├── SendPaymentConfirmation.php
│   │   │   └── UpdateCustomerCredit.php
│   │   ├── Invoicing/
│   │   │   ├── SendInvoiceEmail.php
│   │   │   ├── LogInvoiceActivity.php
│   │   │   └── CheckOverdueInvoices.php
│   │   ├── Inventory/
│   │   │   ├── LogStockMovement.php
│   │   │   ├── CheckReorderLevel.php
│   │   │   └── NotifyLowStock.php
│   │   ├── Delivery/
│   │   │   ├── NotifyCustomerOfDelivery.php
│   │   │   ├── NotifyDriverOfAssignment.php
│   │   │   └── UpdateOrderDeliveryStatus.php
│   │   └── Notifications/
│   │       ├── SendDatabaseNotification.php
│   │       ├── SendEmailNotification.php
│   │       └── SendSMSNotification.php
│   │
│   ├── Models/                     # Eloquent Models
│   │   ├── Core/
│   │   │   ├── User.php
│   │   │   └── Role.php
│   │   ├── Companies/
│   │   │   └── Company.php
│   │   ├── Branches/
│   │   │   └── Branch.php
│   │   ├── Customers/
│   │   │   ├── Customer.php
│   │   │   ├── CustomerContact.php
│   │   │   ├── CustomerShippingAddress.php
│   │   │   └── CustomerNote.php
│   │   ├── Suppliers/
│   │   │   ├── Supplier.php
│   │   │   └── SupplierProduct.php
│   │   ├── Products/
│   │   │   ├── Product.php
│   │   │   ├── ProductCategory.php
│   │   │   ├── ProductBrand.php
│   │   │   ├── ProductUnit.php
│   │   │   ├── ProductVariant.php
│   │   │   └── ProductImage.php
│   │   ├── Catalog/
│   │   │   ├── PriceList.php
│   │   │   ├── PriceListItem.php
│   │   │   ├── Promotion.php
│   │   │   └── PromotionProduct.php
│   │   ├── Inventory/
│   │   │   ├── Warehouse.php
│   │   │   ├── WarehouseZone.php
│   │   │   ├── WarehouseBin.php
│   │   │   ├── StockItem.php
│   │   │   ├── StockMovement.php
│   │   │   ├── StockAdjustment.php
│   │   │   ├── StockAdjustmentItem.php
│   │   │   ├── StockTransfer.php
│   │   │   └── StockTransferItem.php
│   │   ├── Orders/
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   └── OrderStatusHistory.php
│   │   ├── PurchaseOrders/
│   │   │   ├── PurchaseOrder.php
│   │   │   └── PurchaseOrderItem.php
│   │   ├── Payments/
│   │   │   ├── Payment.php
│   │   │   ├── PaymentAllocation.php
│   │   │   └── PaymentReceipt.php
│   │   ├── Invoicing/
│   │   │   ├── Invoice.php
│   │   │   ├── InvoiceItem.php
│   │   │   └── InvoiceStatusHistory.php
│   │   ├── Receiving/
│   │   │   ├── GoodsReceivedNote.php
│   │   │   └── GoodsReceivedNoteItem.php
│   │   ├── PickingPacking/
│   │   │   ├── PickList.php
│   │   │   ├── PickListItem.php
│   │   │   ├── PackingList.php
│   │   │   └── PackingListItem.php
│   │   ├── Shipping/
│   │   │   ├── Shipment.php
│   │   │   ├── ShipmentItem.php
│   │   │   └── ShippingCarrier.php
│   │   ├── Delivery/
│   │   │   ├── Delivery.php
│   │   │   ├── DeliveryItem.php
│   │   │   ├── DeliveryRoute.php
│   │   │   ├── DeliveryRouteStop.php
│   │   │   └── Driver.php
│   │   ├── Media/
│   │   │   └── DocumentHistory.php
│   │   ├── Settings/
│   │   │   └── Setting.php
│   │   └── Traits/
│   │       ├── HasUuid.php
│   │       ├── HasCompany.php
│   │       ├── HasBranch.php
│   │       ├── HasStatus.php
│   │       ├── HasNumber.php
│   │       ├── Auditable.php
│   │       ├── Filterable.php
│   │       └── SoftDeletes.php
│   │
│   ├── Observers/                  # Model Observers
│   │   ├── UserObserver.php
│   │   ├── CompanyObserver.php
│   │   ├── CustomerObserver.php
│   │   ├── OrderObserver.php
│   │   ├── PaymentObserver.php
│   │   ├── InvoiceObserver.php
│   │   ├── StockItemObserver.php
│   │   └── DeliveryObserver.php
│   │
│   ├── Policies/                   # Authorization Policies
│   │   ├── CompanyPolicy.php
│   │   ├── BranchPolicy.php
│   │   ├── UserPolicy.php
│   │   ├── CustomerPolicy.php
│   │   ├── SupplierPolicy.php
│   │   ├── ProductPolicy.php
│   │   ├── OrderPolicy.php
│   │   ├── PaymentPolicy.php
│   │   ├── InvoicePolicy.php
│   │   ├── StockItemPolicy.php
│   │   ├── StockAdjustmentPolicy.php
│   │   ├── DeliveryPolicy.php
│   │   └── ReportPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   │
│   ├── Repositories/               # Repository Pattern
│   │   ├── Contracts/              # Repository Interfaces
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── CompanyRepositoryInterface.php
│   │   │   ├── CustomerRepositoryInterface.php
│   │   │   ├── SupplierRepositoryInterface.php
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   ├── OrderRepositoryInterface.php
│   │   │   ├── PaymentRepositoryInterface.php
│   │   │   ├── InvoiceRepositoryInterface.php
│   │   │   ├── StockItemRepositoryInterface.php
│   │   │   ├── DeliveryRepositoryInterface.php
│   │   │   └── ReportRepositoryInterface.php
│   │   └── Eloquent/               # Eloquent Implementations
│   │       ├── EloquentUserRepository.php
│   │       ├── EloquentCompanyRepository.php
│   │       ├── EloquentCustomerRepository.php
│   │       ├── EloquentSupplierRepository.php
│   │       ├── EloquentProductRepository.php
│   │       ├── EloquentOrderRepository.php
│   │       ├── EloquentPaymentRepository.php
│   │       ├── EloquentInvoiceRepository.php
│   │       ├── EloquentStockItemRepository.php
│   │       ├── EloquentDeliveryRepository.php
│   │       └── EloquentReportRepository.php
│   │
│   ├── Resources/                  # API Resources
│   │   ├── Core/
│   │   │   ├── UserResource.php
│   │   │   ├── UserCollection.php
│   │   │   └── AuthResource.php
│   │   ├── Companies/
│   │   │   ├── CompanyResource.php
│   │   │   └── CompanyCollection.php
│   │   ├── Customers/
│   │   │   ├── CustomerResource.php
│   │   │   ├── CustomerCollection.php
│   │   │   ├── CustomerContactResource.php
│   │   │   └── CustomerAddressResource.php
│   │   ├── Suppliers/
│   │   │   ├── SupplierResource.php
│   │   │   └── SupplierCollection.php
│   │   ├── Products/
│   │   │   ├── ProductResource.php
│   │   │   ├── ProductCollection.php
│   │   │   ├── ProductCategoryResource.php
│   │   │   └── ProductBrandResource.php
│   │   ├── Orders/
│   │   │   ├── OrderResource.php
│   │   │   ├── OrderCollection.php
│   │   │   ├── OrderItemResource.php
│   │   │   └── OrderStatusHistoryResource.php
│   │   ├── Payments/
│   │   │   ├── PaymentResource.php
│   │   │   ├── PaymentCollection.php
│   │   │   └── PaymentAllocationResource.php
│   │   ├── Invoicing/
│   │   │   ├── InvoiceResource.php
│   │   │   ├── InvoiceCollection.php
│   │   │   └── InvoiceItemResource.php
│   │   ├── Inventory/
│   │   │   ├── StockItemResource.php
│   │   │   ├── StockMovementResource.php
│   │   │   └── StockAdjustmentResource.php
│   │   ├── Delivery/
│   │   │   ├── DeliveryResource.php
│   │   │   ├── DriverResource.php
│   │   │   └── DeliveryRouteResource.php
│   │   └── Reports/
│   │       ├── SalesReportResource.php
│   │       └── InventoryReportResource.php
│   │
│   ├── Services/                   # Service Classes (complex business logic)
│   │   ├── Pricing/
│   │   │   ├── PricingService.php
│   │   │   ├── DiscountService.php
│   │   │   └── TaxService.php
│   │   ├── Inventory/
│   │   │   ├── StockReservationService.php
│   │   │   ├── StockMovementService.php
│   │   │   └── ReorderService.php
│   │   ├── Order/
│   │   │   ├── OrderCalculationService.php
│   │   │   └── OrderFulfillmentService.php
│   │   ├── Payment/
│   │   │   ├── PaymentProcessingService.php
│   │   │   └── PaymentAllocationService.php
│   │   ├── Invoice/
│   │   │   ├── InvoiceGenerationService.php
│   │   │   └── InvoiceCalculationService.php
│   │   ├── Delivery/
│   │   │   ├── RouteOptimizationService.php
│   │   │   └── DeliveryTrackingService.php
│   │   ├── Reporting/
│   │   │   ├── SalesReportService.php
│   │   │   ├── InventoryReportService.php
│   │   │   └── FinancialReportService.php
│   │   └── Notification/
│   │       ├── NotificationService.php
│   │       └── EmailService.php
│   │
│   ├── StateMachines/              # State Machine Implementations
│   │   ├── Order/
│   │   │   ├── OrderState.php
│   │   │   ├── OrderStateMachine.php
│   │   │   └── States/
│   │   │       ├── DraftState.php
│   │   │       ├── PendingState.php
│   │   │       ├── ConfirmedState.php
│   │   │       ├── ProcessingState.php
│   │   │       ├── PickingState.php
│   │   │       ├── PackingState.php
│   │   │       ├── ReadyToShipState.php
│   │   │       ├── ShippedState.php
│   │   │       ├── DeliveredState.php
│   │   │       ├── CompletedState.php
│   │   │       ├── OnHoldState.php
│   │   │       └── CancelledState.php
│   │   ├── Payment/
│   │   │   ├── PaymentState.php
│   │   │   └── PaymentStateMachine.php
│   │   ├── Invoice/
│   │   │   ├── InvoiceState.php
│   │   │   └── InvoiceStateMachine.php
│   │   ├── Stock/
│   │   │   ├── StockState.php
│   │   │   └── StockStateMachine.php
│   │   └── Delivery/
│   │       ├── DeliveryState.php
│   │       └── DeliveryStateMachine.php
│   │
│   ├── ValueObjects/               # Value Objects
│   │   ├── Money.php
│   │   ├── Address.php
│   │   ├── PhoneNumber.php
│   │   ├── Email.php
│   │   ├── Quantity.php
│   │   ├── Sku.php
│   │   ├── OrderNumber.php
│   │   ├── InvoiceNumber.php
│   │   └── PaymentReference.php
│   │
│   └── Support/                    # Support/Trait Classes
│       ├── Helpers/
│       │   ├── NumberHelper.php
│       │   ├── DateHelper.php
│       │   ├── StringHelper.php
│       │   └── ArrayHelper.php
│       ├── Exceptions/
│       │   ├── Handler.php
│       │   └── ApiException.php
│       └── Traits/
│           ├── HasUuid.php
│           ├── HasCompanyScope.php
│           ├── HasBranchScope.php
│           ├── IsFilterable.php
│           ├── IsSearchable.php
│           ├── Auditable.php
│           ├── HasNumberGenerator.php
│           └── Dispatchable.php
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── hashing.php
│   ├── logging.php
│   ├── mail.php
│   ├── permission.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── session.php
│   └── broadcasting.php
│
├── database/
│   ├── factories/                  # Model Factories
│   │   ├── Core/
│   │   │   └── UserFactory.php
│   │   ├── Companies/
│   │   │   └── CompanyFactory.php
│   │   ├── Branches/
│   │   │   └── BranchFactory.php
│   │   ├── Customers/
│   │   │   ├── CustomerFactory.php
│   │   │   ├── CustomerContactFactory.php
│   │   │   └── CustomerShippingAddressFactory.php
│   │   ├── Suppliers/
│   │   │   └── SupplierFactory.php
│   │   ├── Products/
│   │   │   ├── ProductFactory.php
│   │   │   ├── ProductCategoryFactory.php
│   │   │   └── ProductBrandFactory.php
│   │   ├── Orders/
│   │   │   ├── OrderFactory.php
│   │   │   └── OrderItemFactory.php
│   │   ├── Payments/
│   │   │   └── PaymentFactory.php
│   │   ├── Invoicing/
│   │   │   ├── InvoiceFactory.php
│   │   │   └── InvoiceItemFactory.php
│   │   ├── Inventory/
│   │   │   ├── StockItemFactory.php
│   │   │   └── StockMovementFactory.php
│   │   └── Delivery/
│   │       ├── DeliveryFactory.php
│   │       └── DriverFactory.php
│   │
│   ├── migrations/                 # Database Migrations
│   │   ├── 0001_01_01_000000_create_companies_table.php
│   │   ├── 0001_01_01_000001_create_branches_table.php
│   │   ├── 0001_01_01_000002_create_users_table.php
│   │   ├── 0001_01_01_000003_create_user_branches_table.php
│   │   ├── 0001_01_01_000004_create_customers_table.php
│   │   ├── 0001_01_01_000005_create_suppliers_table.php
│   │   ├── 0001_01_01_000006_create_products_table.php
│   │   ├── 0001_01_01_000007_create_price_lists_table.php
│   │   ├── 0001_01_01_000008_create_warehouses_table.php
│   │   ├── 0001_01_01_000009_create_stock_items_table.php
│   │   ├── 0001_01_01_000010_create_orders_table.php
│   │   ├── 0001_01_01_000011_create_payments_table.php
│   │   ├── 0001_01_01_000012_create_invoices_table.php
│   │   ├── 0001_01_01_000013_create_goods_received_notes_table.php
│   │   ├── 0001_01_01_000014_create_pick_lists_table.php
│   │   ├── 0001_01_01_000015_create_shipments_table.php
│   │   ├── 0001_01_01_000016_create_deliveries_table.php
│   │   ├── 0001_01_01_000017_create_drivers_table.php
│   │   ├── 0001_01_01_000018_create_media_table.php
│   │   ├── 0001_01_01_000019_create_settings_table.php
│   │   └── 0001_01_01_000020_create_activity_log_table.php
│   │
│   ├── seeders/                    # Database Seeders
│   │   ├── DatabaseSeeder.php
│   │   ├── Core/
│   │   │   ├── RoleSeeder.php
│   │   │   ├── PermissionSeeder.php
│   │   │   └── UserSeeder.php
│   │   ├── Companies/
│   │   │   └── CompanySeeder.php
│   │   ├── Branches/
│   │   │   └── BranchSeeder.php
│   │   ├── Products/
│   │   │   ├── ProductUnitSeeder.php
│   │   │   ├── ProductCategorySeeder.php
│   │   │   └── ProductBrandSeeder.php
│   │   └── Settings/
│   │       └── SettingSeeder.php
│   │
│   └── scenarios/                  # Test Scenarios
│       ├── 01_create_company_with_branches.php
│       ├── 02_create_products_with_categories.php
│       ├── 03_create_customers_with_contacts.php
│       ├── 04_create_order_with_items.php
│       └── 05_process_payment_and_invoice.php
│
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   ├── ssr.js
│   │   ├── types/
│   │   │   ├── index.d.ts
│   │   │   ├── models/
│   │   │   │   ├── user.d.ts
│   │   │   │   ├── company.d.ts
│   │   │   │   ├── customer.d.ts
│   │   │   │   ├── order.d.ts
│   │   │   │   ├── product.d.ts
│   │   │   │   ├── payment.d.ts
│   │   │   │   └── invoice.d.ts
│   │   │   ├── api/
│   │   │   │   ├── responses.d.ts
│   │   │   │   └── requests.d.ts
│   │   │   └── store/
│   │   │       └── index.d.ts
│   │   ├── stores/                 # Pinia Stores
│   │   │   ├── auth.js
│   │   │   ├── company.js
│   │   │   ├── customer.js
│   │   │   ├── order.js
│   │   │   ├── product.js
│   │   │   ├── payment.js
│   │   │   ├── invoice.js
│   │   │   ├── inventory.js
│   │   │   └── ui.js
│   │   ├── composables/            # Vue Composables
│   │   │   ├── useApi.js
│   │   │   ├── useAuth.js
│   │   │   ├── usePagination.js
│   │   │   ├── useFilter.js
│   │   │   ├── useNotifications.js
│   │   │   ├── useModal.js
│   │   │   ├── useConfirm.js
│   │   │   ├── useExport.js
│   │   │   └── useForm.js
│   │   ├── Components/
│   │   │   ├── Layout/
│   │   │   │   ├── AppLayout.vue
│   │   │   │   ├── Sidebar.vue
│   │   │   │   ├── Header.vue
│   │   │   │   ├── Footer.vue
│   │   │   │   └── Breadcrumb.vue
│   │   │   ├── UI/                 # Reusable UI Components
│   │   │   │   ├── Button.vue
│   │   │   │   ├── Input.vue
│   │   │   │   ├── Select.vue
│   │   │   │   ├── Textarea.vue
│   │   │   │   ├── Checkbox.vue
│   │   │   │   ├── Radio.vue
│   │   │   │   ├── Toggle.vue
│   │   │   │   ├── DatePicker.vue
│   │   │   │   ├── TimePicker.vue
│   │   │   │   ├── DateTimePicker.vue
│   │   │   │   ├── FileUpload.vue
│   │   │   │   ├── SearchInput.vue
│   │   │   │   └── ColorPicker.vue
│   │   │   ├── Data/               # Data Display Components
│   │   │   │   ├── DataTable.vue
│   │   │   │   ├── DataTableColumn.vue
│   │   │   │   ├── DataTablePagination.vue
│   │   │   │   ├── DataTableFilter.vue
│   │   │   │   ├── DataTableSearch.vue
│   │   │   │   ├── DataTableBulkActions.vue
│   │   │   │   ├── DataCard.vue
│   │   │   │   ├── DataList.vue
│   │   │   │   ├── StatCard.vue
│   │   │   │   └── EmptyState.vue
│   │   │   ├── Feedback/           # Feedback Components
│   │   │   │   ├── Alert.vue
│   │   │   │   ├── Toast.vue
│   │   │   │   ├── Modal.vue
│   │   │   │   ├── ConfirmDialog.vue
│   │   │   │   ├── LoadingSpinner.vue
│   │   │   │   ├── ProgressBar.vue
│   │   │   │   └── Skeleton.vue
│   │   │   ├── Navigation/         # Navigation Components
│   │   │   │   ├── Tabs.vue
│   │   │   │   ├── Pagination.vue
│   │   │   │   ├── Dropdown.vue
│   │   │   │   ├── Stepper.vue
│   │   │   │   └── Breadcrumbs.vue
│   │   │   └── Form/               # Form Components
│   │   │       ├── FormField.vue
│   │   │       ├── FormGroup.vue
│   │   │       ├── FormError.vue
│   │   │       └── FormActions.vue
│   │   └── Pages/
│   │       ├── Auth/
│   │       │   ├── Login.vue
│   │       │   ├── Register.vue
│   │       │   ├── ForgotPassword.vue
│   │       │   └── ResetPassword.vue
│   │       ├── Dashboard/
│   │       │   └── Index.vue
│   │       ├── Companies/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Branches/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Customers/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Suppliers/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Products/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Orders/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Edit.vue
│   │       ├── Payments/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Create.vue
│   │       │   └── Approve.vue
│   │       ├── Invoices/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   └── Print.vue
│   │       ├── Inventory/
│   │       │   ├── Index.vue
│   │       │   ├── StockLevels.vue
│   │       │   ├── Adjustments.vue
│   │       │   └── Transfers.vue
│   │       ├── Receiving/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   └── Create.vue
│   │       ├── PickingPacking/
│   │       │   ├── PickLists.vue
│   │       │   ├── PackingLists.vue
│   │       │   └── Show.vue
│   │       ├── Shipping/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   └── Track.vue
│   │       ├── Delivery/
│   │       │   ├── Index.vue
│   │       │   ├── Show.vue
│   │       │   ├── Routes.vue
│   │       │   └── Drivers.vue
│   │       ├── Reports/
│   │       │   ├── Index.vue
│   │       │   ├── Sales.vue
│   │       │   ├── Inventory.vue
│   │       │   └── Financial.vue
│   │       └── Settings/
│   │           ├── Index.vue
│   │           ├── Profile.vue
│   │           └── Company.vue
│   │
│   └── views/                      # Blade Templates
│       └── emails/
│           ├── order-confirmation.blade.php
│           ├── invoice.blade.php
│           ├── payment-receipt.blade.php
│           └── delivery-notification.blade.php
│
├── routes/
│   ├── api.php                     # API Routes
│   ├── web.php                     # Web Routes (Inertia)
│   ├── channels.php
│   └── console.php
│
├── tests/
│   ├── Pest.php                    # Pest Configuration
│   ├── TestCase.php
│   ├── Feature/
│   │   ├── Auth/
│   │   │   ├── LoginTest.php
│   │   │   ├── RegisterTest.php
│   │   │   └── PasswordResetTest.php
│   │   ├── Companies/
│   │   │   ├── CompanyTest.php
│   │   │   └── CompanyPolicyTest.php
│   │   ├── Customers/
│   │   │   ├── CustomerTest.php
│   │   │   ├── CustomerContactTest.php
│   │   │   └── CustomerAddressTest.php
│   │   ├── Suppliers/
│   │   │   └── SupplierTest.php
│   │   ├── Products/
│   │   │   ├── ProductTest.php
│   │   │   ├── ProductCategoryTest.php
│   │   │   └── ProductBrandTest.php
│   │   ├── Orders/
│   │   │   ├── OrderCreationTest.php
│   │   │   ├── OrderStatusTest.php
│   │   │   ├── OrderCancellationTest.php
│   │   │   └── OrderItemTest.php
│   │   ├── Payments/
│   │   │   ├── PaymentCreationTest.php
│   │   │   ├── PaymentApprovalTest.php
│   │   │   ├── PaymentAllocationTest.php
│   │   │   └── PaymentRefundTest.php
│   │   ├── Invoicing/
│   │   │   ├── InvoiceGenerationTest.php
│   │   │   ├── InvoiceStatusTest.php
│   │   │   └── InvoicePaymentTest.php
│   │   ├── Inventory/
│   │   │   ├── StockReservationTest.php
│   │   │   ├── StockAdjustmentTest.php
│   │   │   ├── StockTransferTest.php
│   │   │   └── StockMovementTest.php
│   │   ├── Receiving/
│   │   │   └── GRNTest.php
│   │   ├── PickingPacking/
│   │   │   ├── PickListTest.php
│   │   │   └── PackingListTest.php
│   │   ├── Shipping/
│   │   │   └── ShipmentTest.php
│   │   ├── Delivery/
│   │   │   ├── DeliveryTest.php
│   │   │   ├── DriverAssignmentTest.php
│   │   │   └── DeliveryRouteTest.php
│   │   └── Reports/
│   │       ├── SalesReportTest.php
│   │       └── InventoryReportTest.php
│   │
│   └── Unit/
│       ├── Actions/
│       │   ├── CreateOrderActionTest.php
│       │   ├── ApprovePaymentActionTest.php
│       │   ├── ReserveStockActionTest.php
│       │   └── GenerateInvoiceActionTest.php
│       ├── Services/
│       │   ├── PricingServiceTest.php
│       │   ├── StockReservationServiceTest.php
│       │   ├── OrderCalculationServiceTest.php
│       │   └── InvoiceCalculationServiceTest.php
│       ├── StateMachines/
│       │   ├── OrderStateMachineTest.php
│       │   ├── PaymentStateMachineTest.php
│       │   └── InvoiceStateMachineTest.php
│       ├── ValueObjects/
│       │   ├── MoneyTest.php
│       │   ├── AddressTest.php
│       │   └── QuantityTest.php
│       ├── Enums/
│       │   └── AllEnumsTest.php
│       └── Helpers/
│           ├── NumberHelperTest.php
│           └── DateHelperTest.php
│
├── .env.example
├── composer.json
├── package.json
├── vite.config.js
├── tsconfig.json
├── tailwind.config.js
├── postcss.config.js
├── phpunit.xml
├── pest.php
├── rector.php
└── pint.json
```

---

## 4. STATE MACHINES

### 4.1 Order State Machine

```
Draft ──▶ Pending ──▶ Confirmed ──▶ Processing ──▶ Picking ──▶ Packing ──▶ Ready to Ship ──▶ Shipped ──▶ Delivered ──▶ Completed
                                                                          │
                                                                   On Hold (from Processing, Pending)
Any state ──▶ Cancelled (with valid transition check)
```

**Valid Transitions:**

| Current State | Allowed Next States | Guard Conditions |
|--------------|---------------------|------------------|
| Draft | Pending, Cancelled | User has order.create permission |
| Pending | Confirmed, Cancelled, On Hold | User has order.confirm permission |
| Confirmed | Processing, Cancelled | Stock available |
| Processing | Picking, Cancelled, On Hold | Order not yet picked |
| On Hold | Processing, Cancelled | Hold reason provided |
| Picking | Packing | All items picked |
| Packing | Ready to Ship | All items packed |
| Ready to Ship | Shipped | Carrier assigned |
| Shipped | Delivered | POD received |
| Delivered | Completed | No returns pending |
| Completed | - | Terminal state |
| Cancelled | - | Terminal state |

### 4.2 Payment State Machine

```
Pending ──▶ Processing ──▶ Completed
    │            │
    │      Failed
    │
Cancelled

Processing ──▶ Refunded (with approval)
```

**Valid Transitions:**

| Current State | Allowed Next States | Guard Conditions |
|--------------|---------------------|------------------|
| Pending | Processing, Cancelled | Payment initiated |
| Processing | Completed, Failed | Payment gateway response |
| Completed | Refunded | Refund approved by admin |
| Failed | Pending, Cancelled | Retry or cancel |
| Cancelled | - | Terminal state |
| Refunded | - | Terminal state |

### 4.3 Stock State Machine

```
Available ──▶ Reserved ──▶ Allocated ──▶ Shipped
                  │
            Released
```

**Valid Transitions:**

| Current State | Allowed Next States | Guard Conditions |
|--------------|---------------------|------------------|
| Available | Reserved | Sufficient quantity_available |
| Reserved | Allocated, Released | Order confirmed / Order cancelled |
| Allocated | Shipped | Items picked and packed |
| Released | Available | Stock restored |
| Shipped | - | Terminal state |

### 4.4 Delivery State Machine

```
Pending ──▶ Assigned ──▶ Out for Delivery ──▶ Delivered
                                  │
                           Failed Attempt
                                  │
                            Rescheduled
```

**Valid Transitions:**

| Current State | Allowed Next States | Guard Conditions |
|--------------|---------------------|------------------|
| Pending | Assigned, Cancelled | Driver available |
| Assigned | Out for Delivery, Cancelled | Driver started route |
| Out for Delivery | Delivered, Failed Attempt | Delivery attempted |
| Failed Attempt | Assigned, Cancelled | attempt_count < max_attempts |
| Delivered | Returned | Customer initiated return |
| Returned | - | Terminal state |
| Cancelled | - | Terminal state |

### 4.5 Invoice State Machine

```
Draft ──▶ Pending ──▶ Sent ──▶ Viewed ──▶ Paid
                      │          │
                 Overdue    Partial
                      │
                 Cancelled/Void
```

**Valid Transitions:**

| Current State | Allowed Next States | Guard Conditions |
|--------------|---------------------|------------------|
| Draft | Pending, Cancelled | Invoice created |
| Pending | Sent, Cancelled | Invoice sent to customer |
| Sent | Viewed, Overdue | Customer viewed / Payment overdue |
| Viewed | Paid, Partial, Overdue | Payment received |
| Partial | Paid, Overdue | Additional payment received |
| Overdue | Paid, Cancelled | Payment received / Invoice voided |
| Paid | - | Terminal state |
| Cancelled | - | Terminal state |
| Void | - | Terminal state |

---

## 5. EVENT ARCHITECTURE

### 5.1 Event-Listener Mapping

| Event | Listeners | Notification |
|-------|-----------|--------------|
| UserCreated | SendWelcomeEmail, LogUserActivity | Email welcome |
| CompanyCreated | LogCompanyActivity | - |
| CustomerCreated | LogCustomerActivity | - |
| CreditStatusChanged | NotifyCreditTeam, UpdateCustomerCredit | Email alert |
| OrderCreated | ReserveStockForOrder, LogOrderActivity | - |
| OrderConfirmed | NotifyWarehouseOfOrder, SendOrderConfirmation | Email to customer |
| OrderCancelled | ReleaseStock, LogOrderCancellation | Email to customer |
| OrderStatusChanged | LogOrderStatusChange | - |
| OrderReadyForPickup | GeneratePickList, NotifyWarehouse | - |
| PaymentCreated | LogPaymentActivity | - |
| PaymentUploaded | NotifyAccountTeam | Email notification |
| PaymentApproved | UpdateOrderPaymentStatus, UpdateInvoicePaymentStatus | Email to customer |
| PaymentRejected | NotifyPaymentRejected | Email to customer |
| PaymentCompleted | UpdateCustomerCredit, SendPaymentConfirmation | Email receipt |
| PaymentRefunded | UpdateOrderPaymentStatus, SendRefundConfirmation | Email to customer |
| InvoiceGenerated | LogInvoiceActivity | - |
| InvoiceSent | SendInvoiceEmail | Email with PDF |
| InvoicePaid | LogInvoicePayment | - |
| InvoiceOverdue | SendOverdueReminder | Email reminder |
| InvoiceVoided | LogInvoiceVoid | - |
| StockReserved | LogStockReservation | - |
| StockReleased | LogStockRelease | - |
| StockAdjusted | LogStockAdjustment | - |
| StockTransferred | LogStockTransfer | - |
| StockLow | NotifyLowStock | Email/SMS alert |
| StockOut | NotifyStockOut | Email/SMS alert |
| GRNCreated | LogReceivingActivity | - |
| GoodsReceived | UpdateStockLevels, NotifyPurchasing | - |
| GRNCompleted | GenerateGRNReport | - |
| PickListGenerated | NotifyPicker | - |
| PickListCompleted | NotifyPackingTeam | - |
| OrderPacked | NotifyShippingTeam | - |
| ShipmentCreated | LogShipmentActivity | - |
| ShipmentShipped | NotifyCustomerOfShipment | Email with tracking |
| ShipmentDelivered | UpdateOrderStatus | - |
| ShipmentException | NotifyShippingException | Email alert |
| DriverAssigned | NotifyDriverOfAssignment | SMS/Push |
| DeliveryStarted | NotifyCustomerOfDelivery | Email/SMS |
| DeliveryCompleted | UpdateOrderDeliveryStatus, SendDeliveryConfirmation | Email receipt |
| DeliveryFailed | NotifyDeliveryException | Email alert |
| DeliveryRescheduled | NotifyCustomerOfReschedule | Email/SMS |

### 5.2 Event Class Structure

```php
// Example Event Class
class OrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly User $createdBy,
        public readonly array $metadata = []
    ) {}
}

// Example Listener Class
class ReserveStockForOrder
{
    public function __construct(
        private StockReservationService $stockService
    ) {}

    public function handle(OrderCreated $event): void
    {
        $this->stockService->reserveForOrder($event->order);
    }
}
```

---

## 6. API STRUCTURE

### 6.1 Authentication Routes

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
GET    /api/v1/auth/user
PUT    /api/v1/auth/profile
PUT    /api/v1/auth/password
```

### 6.2 Companies Routes

```
GET    /api/v1/companies
POST   /api/v1/companies
GET    /api/v1/companies/{id}
PUT    /api/v1/companies/{id}
DELETE /api/v1/companies/{id}
GET    /api/v1/companies/{id}/branches
GET    /api/v1/companies/{id}/settings
PUT    /api/v1/companies/{id}/settings
```

### 6.3 Branches Routes

```
GET    /api/v1/branches
POST   /api/v1/branches
GET    /api/v1/branches/{id}
PUT    /api/v1/branches/{id}
DELETE /api/v1/branches/{id}
GET    /api/v1/branches/{id}/users
GET    /api/v1/branches/{id}/warehouses
```

### 6.4 Customers Routes

```
GET    /api/v1/customers
POST   /api/v1/customers
GET    /api/v1/customers/{id}
PUT    /api/v1/customers/{id}
DELETE /api/v1/customers/{id}
GET    /api/v1/customers/{id}/contacts
POST   /api/v1/customers/{id}/contacts
PUT    /api/v1/customers/{id}/contacts/{contactId}
DELETE /api/v1/customers/{id}/contacts/{contactId}
GET    /api/v1/customers/{id}/addresses
POST   /api/v1/customers/{id}/addresses
PUT    /api/v1/customers/{id}/addresses/{addressId}
DELETE /api/v1/customers/{id}/addresses/{addressId}
GET    /api/v1/customers/{id}/orders
GET    /api/v1/customers/{id}/invoices
GET    /api/v1/customers/{id}/payments
GET    /api/v1/customers/{id}/notes
POST   /api/v1/customers/{id}/notes
PUT    /api/v1/customers/{id}/credit-status
```

### 6.5 Suppliers Routes

```
GET    /api/v1/suppliers
POST   /api/v1/suppliers
GET    /api/v1/suppliers/{id}
PUT    /api/v1/suppliers/{id}
DELETE /api/v1/suppliers/{id}
GET    /api/v1/suppliers/{id}/products
POST   /api/v1/suppliers/{id}/products
PUT    /api/v1/suppliers/{id}/products/{productId}
DELETE /api/v1/suppliers/{id}/products/{productId}
GET    /api/v1/suppliers/{id}/purchase-orders
```

### 6.6 Products Routes

```
GET    /api/v1/products
POST   /api/v1/products
GET    /api/v1/products/{id}
PUT    /api/v1/products/{id}
DELETE /api/v1/products/{id}
GET    /api/v1/products/{id}/variants
POST   /api/v1/products/{id}/variants
PUT    /api/v1/products/{id}/variants/{variantId}
DELETE /api/v1/products/{id}/variants/{variantId}
GET    /api/v1/products/{id}/images
POST   /api/v1/products/{id}/images
DELETE /api/v1/products/{id}/images/{imageId}
GET    /api/v1/products/{id}/stock
GET    /api/v1/products/{id}/price-history

GET    /api/v1/product-categories
POST   /api/v1/product-categories
GET    /api/v1/product-categories/{id}
PUT    /api/v1/product-categories/{id}
DELETE /api/v1/product-categories/{id}

GET    /api/v1/product-brands
POST   /api/v1/product-brands
GET    /api/v1/product-brands/{id}
PUT    /api/v1/product-brands/{id}
DELETE /api/v1/product-brands/{id}
```

### 6.7 Orders Routes

```
GET    /api/v1/orders
POST   /api/v1/orders
GET    /api/v1/orders/{id}
PUT    /api/v1/orders/{id}
DELETE /api/v1/orders/{id}
POST   /api/v1/orders/{id}/confirm
POST   /api/v1/orders/{id}/cancel
POST   /api/v1/orders/{id}/hold
POST   /api/v1/orders/{id}/release
POST   /api/v1/orders/{id}/ship
POST   /api/v1/orders/{id}/deliver
POST   /api/v1/orders/{id}/complete
GET    /api/v1/orders/{id}/items
POST   /api/v1/orders/{id}/items
PUT    /api/v1/orders/{id}/items/{itemId}
DELETE /api/v1/orders/{id}/items/{itemId}
GET    /api/v1/orders/{id}/status-history
GET    /api/v1/orders/{id}/payments
GET    /api/v1/orders/{id}/invoices
GET    /api/v1/orders/{id}/shipments
GET    /api/v1/orders/{id}/deliveries
```

### 6.8 Purchase Orders Routes

```
GET    /api/v1/purchase-orders
POST   /api/v1/purchase-orders
GET    /api/v1/purchase-orders/{id}
PUT    /api/v1/purchase-orders/{id}
DELETE /api/v1/purchase-orders/{id}
POST   /api/v1/purchase-orders/{id}/approve
POST   /api/v1/purchase-orders/{id}/cancel
GET    /api/v1/purchase-orders/{id}/items
POST   /api/v1/purchase-orders/{id}/items
PUT    /api/v1/purchase-orders/{id}/items/{itemId}
DELETE /api/v1/purchase-orders/{id}/items/{itemId}
```

### 6.9 Payments Routes

```
GET    /api/v1/payments
POST   /api/v1/payments
GET    /api/v1/payments/{id}
PUT    /api/v1/payments/{id}
DELETE /api/v1/payments/{id}
POST   /api/v1/payments/{id}/approve
POST   /api/v1/payments/{id}/reject
POST   /api/v1/payments/{id}/complete
POST   /api/v1/payments/{id}/refund
GET    /api/v1/payments/{id}/allocations
POST   /api/v1/payments/{id}/allocations
DELETE /api/v1/payments/{id}/allocations/{allocationId}
GET    /api/v1/payments/{id}/receipt
```

### 6.10 Invoices Routes

```
GET    /api/v1/invoices
POST   /api/v1/invoices
GET    /api/v1/invoices/{id}
PUT    /api/v1/invoices/{id}
DELETE /api/v1/invoices/{id}
POST   /api/v1/invoices/{id}/send
POST   /api/v1/invoices/{id}/void
GET    /api/v1/invoices/{id}/items
GET    /api/v1/invoices/{id}/payments
GET    /api/v1/invoices/{id}/pdf
GET    /api/v1/invoices/{id}/status-history
```

### 6.11 Inventory Routes

```
GET    /api/v1/stock-items
POST   /api/v1/stock-items
GET    /api/v1/stock-items/{id}
PUT    /api/v1/stock-items/{id}
GET    /api/v1/stock-items/{id}/movements

GET    /api/v1/stock-adjustments
POST   /api/v1/stock-adjustments
GET    /api/v1/stock-adjustments/{id}
POST   /api/v1/stock-adjustments/{id}/approve
POST   /api/v1/stock-adjustments/{id}/reject
POST   /api/v1/stock-adjustments/{id}/complete

GET    /api/v1/stock-transfers
POST   /api/v1/stock-transfers
GET    /api/v1/stock-transfers/{id}
POST   /api/v1/stock-transfers/{id}/approve
POST   /api/v1/stock-transfers/{id}/ship
POST   /api/v1/stock-transfers/{id}/receive
POST   /api/v1/stock-transfers/{id}/cancel
```

### 6.12 Receiving Routes

```
GET    /api/v1/grn
POST   /api/v1/grn
GET    /api/v1/grn/{id}
PUT    /api/v1/grn/{id}
DELETE /api/v1/grn/{id}
POST   /api/v1/grn/{id}/receive
POST   /api/v1/grn/{id}/complete
GET    /api/v1/grn/{id}/items
POST   /api/v1/grn/{id}/items
PUT    /api/v1/grn/{id}/items/{itemId}
```

### 6.13 Picking & Packing Routes

```
GET    /api/v1/pick-lists
POST   /api/v1/pick-lists
GET    /api/v1/pick-lists/{id}
PUT    /api/v1/pick-lists/{id}
POST   /api/v1/pick-lists/{id}/start
POST   /api/v1/pick-lists/{id}/complete
GET    /api/v1/pick-lists/{id}/items
POST   /api/v1/pick-lists/{id}/items/{itemId}/pick

GET    /api/v1/packing-lists
POST   /api/v1/packing-lists
GET    /api/v1/packing-lists/{id}
POST   /api/v1/packing-lists/{id}/pack
POST   /api/v1/packing-lists/{id}/verify
```

### 6.14 Shipping Routes

```
GET    /api/v1/shipments
POST   /api/v1/shipments
GET    /api/v1/shipments/{id}
PUT    /api/v1/shipments/{id}
POST   /api/v1/shipments/{id}/ship
POST   /api/v1/shipments/{id}/deliver
GET    /api/v1/shipments/{id}/track

GET    /api/v1/shipping-carriers
POST   /api/v1/shipping-carriers
GET    /api/v1/shipping-carriers/{id}
PUT    /api/v1/shipping-carriers/{id}
DELETE /api/v1/shipping-carriers/{id}
```

### 6.15 Delivery Routes

```
GET    /api/v1/deliveries
POST   /api/v1/deliveries
GET    /api/v1/deliveries/{id}
PUT    /api/v1/deliveries/{id}
POST   /api/v1/deliveries/{id}/assign-driver
POST   /api/v1/deliveries/{id}/start
POST   /api/v1/deliveries/{id}/complete
POST   /api/v1/deliveries/{id}/fail
POST   /api/v1/deliveries/{id}/reschedule
GET    /api/v1/deliveries/{id}/items

GET    /api/v1/drivers
POST   /api/v1/drivers
GET    /api/v1/drivers/{id}
PUT    /api/v1/drivers/{id}
DELETE /api/v1/drivers/{id}
GET    /api/v1/drivers/{id}/location
PUT    /api/v1/drivers/{id}/location
GET    /api/v1/drivers/{id}/deliveries

GET    /api/v1/delivery-routes
POST   /api/v1/delivery-routes
GET    /api/v1/delivery-routes/{id}
PUT    /api/v1/delivery-routes/{id}
POST   /api/v1/delivery-routes/{id}/start
POST   /api/v1/delivery-routes/{id}/complete
GET    /api/v1/delivery-routes/{id}/stops
POST   /api/v1/delivery-routes/{id}/stops/{stopId}/arrive
POST   /api/v1/delivery-routes/{id}/stops/{stopId}/complete
```

### 6.16 Reports Routes

```
GET    /api/v1/reports/sales
GET    /api/v1/reports/sales/summary
GET    /api/v1/reports/sales/by-customer
GET    /api/v1/reports/sales/by-product
GET    /api/v1/reports/sales/by-period

GET    /api/v1/reports/inventory
GET    /api/v1/reports/inventory/levels
GET    /api/v1/reports/inventory/movements
GET    /api/v1/reports/inventory/valuation
GET    /api/v1/reports/inventory/aging

GET    /api/v1/reports/financial
GET    /api/v1/reports/financial/revenue
GET    /api/v1/reports/financial/payments
GET    /api/v1/reports/financial/outstanding
GET    /api/v1/reports/financial/aging

GET    /api/v1/reports/delivery
GET    /api/v1/reports/delivery/performance
GET    /api/v1/reports/delivery/on-time-rate
```

### 6.17 Settings Routes

```
GET    /api/v1/settings
PUT    /api/v1/settings
GET    /api/v1/settings/{group}
PUT    /api/v1/settings/{group}
```

---

## 7. KEY ENUMS

### 7.1 Core Enums

```php
// UserStatus.php
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}

// UserRole.php
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case SalesRep = 'sales_rep';
    case WarehouseStaff = 'warehouse_staff';
    case Accountant = 'accountant';
    case Driver = 'driver';
    case Viewer = 'viewer';
}
```

### 7.2 Order Enums

```php
// OrderStatus.php
enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Picking = 'picking';
    case Packing = 'packing';
    case ReadyToShip = 'ready_to_ship';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Picking => 'Picking',
            self::Packing => 'Packing',
            self::ReadyToShip => 'Ready to Ship',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Completed => 'Completed',
            self::OnHold => 'On Hold',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft => 'gray',
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Processing => 'indigo',
            self::Picking => 'purple',
            self::Packing => 'pink',
            self::ReadyToShip => 'cyan',
            self::Shipped => 'orange',
            self::Delivered => 'green',
            self::Completed => 'emerald',
            self::OnHold => 'red',
            self::Cancelled => 'red',
        };
    }
}

// OrderType.php
enum OrderType: string
{
    case Standard = 'standard';
    case Repeat = 'repeat';
    case Standing = 'standing';
    case Sample = 'sample';
    case Exchange = 'exchange';
}

// PaymentStatus.php
enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case Overpaid = 'overpaid';
}

// FulfillmentStatus.php
enum FulfillmentStatus: string
{
    case Unfulfilled = 'unfulfilled';
    case Partial = 'partial';
    case Fulfilled = 'fulfilled';
    case Returned = 'returned';
}

// OrderPriority.php
enum OrderPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';
}
```

### 7.3 Payment Enums

```php
// PaymentStatus.php
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}

// PaymentMethod.php
enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case MobileMoney = 'mobile_money';
    case Other = 'other';
}

// PaymentType.php
enum PaymentType: string
{
    case CustomerPayment = 'customer_payment';
    case SupplierPayment = 'supplier_payment';
    case Refund = 'refund';
    case Adjustment = 'adjustment';
}
```

### 7.4 Inventory Enums

```php
// MovementType.php
enum MovementType: string
{
    case Receipt = 'receipt';
    case Sale = 'sale';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Damage = 'damage';
    case Count = 'count';
    case Reservation = 'reservation';
    case Release = 'release';
}

// StockStatus.php
enum StockStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Quarantine = 'quarantine';
}

// AdjustmentType.php
enum AdjustmentType: string
{
    case CycleCount = 'cycle_count';
    case PhysicalCount = 'physical_count';
    case Damage = 'damage';
    case Expiry = 'expiry';
    case Shrinkage = 'shrinkage';
    case Other = 'other';
}
```

### 7.5 Delivery Enums

```php
// DeliveryStatus.php
enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case PartialDelivery = 'partial_delivery';
    case FailedAttempt = 'failed_attempt';
    case Returned = 'returned';
    case Cancelled = 'cancelled';
}

// DriverStatus.php
enum DriverStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case OnLeave = 'on_leave';
    case Suspended = 'suspended';
}

// RouteStatus.php
enum RouteStatus: string
{
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

---

## 8. KEY DESIGN PATTERNS

### 8.1 Action Pattern
Single-responsibility classes that encapsulate business operations.

```php
class CreateOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private PricingService $pricingService,
        private StockReservationService $stockService,
    ) {}

    public function execute(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            $order = $this->orderRepo->create($dto->toArray());
            $this->pricingService->calculateOrderTotal($order);
            $this->stockService->reserveForOrder($order);

            OrderCreated::dispatch($order, auth()->user());

            return $order;
        });
    }
}
```

### 8.2 Repository Pattern
Abstracts data access layer.

```php
interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function findById(string $id): ?Order;
    public function findByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function update(string $id, array $data): Order;
    public function delete(string $id): bool;
}

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected Order $model
    ) {}

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }
    // ...
}
```

### 8.3 Service Pattern
Complex business logic that spans multiple entities.

```php
class StockReservationService
{
    public function __construct(
        private StockItemRepositoryInterface $stockRepo,
        private StockMovementService $movementService,
    ) {}

    public function reserveForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            $stockItem = $this->stockRepo->findByProductAndWarehouse(
                $item->product_id,
                $order->warehouse_id
            );

            if ($stockItem->quantity_available < $item->quantity) {
                throw new InsufficientStockException(
                    "Insufficient stock for {$item->name}"
                );
            }

            $stockItem->decrement('quantity_reserved', $item->quantity);
            $stockItem->increment('version');

            $this->movementService->recordReservation($stockItem, $item->quantity);
        }
    }
}
```

### 8.4 State Machine Pattern
Manages state transitions with guards.

```php
class OrderStateMachine
{
    private array $states = [];
    private array $transitions = [];

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public function canTransition(Order $order, OrderStatus $to): bool
    {
        $from = $order->status;
        return isset($this->transitions[$from][$to])
            && $this->evaluateGuards($order, $from, $to);
    }

    public function transition(Order $order, OrderStatus $to): void
    {
        if (!$this->canTransition($order, $to)) {
            throw new InvalidStateException(
                "Cannot transition from {$order->status->value} to {$to->value}"
            );
        }

        $previousStatus = $order->status;
        $order->update(['status' => $to]);

        OrderStatusChanged::dispatch($order, $previousStatus, $to);
    }
}
```

### 8.5 DTO Pattern
Immutable data transfer objects.

```php
readonly class CreateOrderDTO
{
    public function __construct(
        public string $customerId,
        public string $branchId,
        public string $warehouseId,
        public string $orderType,
        public string $priority,
        public ?string $priceListId,
        public ?string $requestedDeliveryDate,
        public ?string $notes,
        public ?string $poNumber,
        public array $items,
    ) {}

    public static function fromRequest(StoreOrderRequest $request): self
    {
        return new self(
            customerId: $request->customer_id,
            branchId: $request->branch_id,
            warehouseId: $request->warehouse_id,
            orderType: $request->order_type,
            priority: $request->priority,
            priceListId: $request->price_list_id,
            requestedDeliveryDate: $request->requested_delivery_date,
            notes: $request->notes,
            poNumber: $request->po_number,
            items: $request->items,
        );
    }
}
```

### 8.6 Value Object Pattern
Immutable objects with equality by value.

```php
readonly class Money
{
    public function __construct(
        public float $amount,
        public string $currency = 'USD'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add different currencies');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot subtract different currencies');
        }
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function format(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
```

### 8.7 Observer Pattern
Auto-fires on model events.

```php
class OrderObserver
{
    public function created(Order $order): void
    {
        activity()->performedOn($order)
            ->withProperties(['status' => $order->status])
            ->log('Order created');
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            activity()->performedOn($order)
                ->withProperties([
                    'old_status' => $order->getOriginal('status'),
                    'new_status' => $order->status,
                ])
                ->log('Order status changed');
        }
    }
}
```

---

## 9. KEY TRAITS

### 9.1 HasUuid Trait

```php
trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
```

### 9.2 HasCompany Trait

```php
trait HasCompany
{
    public static function bootHasCompany(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->company_id) && auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
```

### 9.3 HasNumber Generator Trait

```php
trait HasNumberGenerator
{
    abstract protected static function getNumberPrefix(): string;

    abstract protected static function getNumberColumn(): string;

    public static function bootHasNumberGenerator(): void
    {
        static::creating(function (Model $model) {
            $prefix = static::getNumberPrefix();
            $column = static::getNumberColumn();

            if (is_null($model->{$column})) {
                $model->{$column} = static::generateNumber($model->company_id, $prefix, $column);
            }
        });
    }

    protected static function generateNumber(string $companyId, string $prefix, string $column): string
    {
        $lastNumber = static::where('company_id', $companyId)
            ->orderByDesc($column)
            ->value($column);

        if ($lastNumber) {
            $number = (int) str_replace($prefix . '-', '', $lastNumber);
            return $prefix . '-' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '-000001';
    }
}
```

### 9.4 Auditable Trait

```php
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            activity()
                ->performedOn($model)
                ->withProperties($model->toArray())
                ->event('created')
                ->log("{$model->getMorphClass()} created");
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            activity()
                ->performedOn($model)
                ->withProperties([
                    'old' => $model->getOriginal(),
                    'new' => $changes,
                ])
                ->event('updated')
                ->log("{$model->getMorphClass()} updated");
        });

        static::deleted(function (Model $model) {
            activity()
                ->performedOn($model)
                ->event('deleted')
                ->log("{$model->getMorphClass()} deleted");
        });
    }
}
```

---

## 10. SCHEDULED TASKS

```php
// Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Check overdue invoices daily
    $schedule->command('invoices:check-overdue')
        ->dailyAt('08:00')
        ->withoutOverlapping();

    // Check low stock levels every 4 hours
    $schedule->command('inventory:check-low-stock')
        ->everyFourHours()
        ->withoutOverlapping();

    // Generate daily sales report
    $schedule->command('reports:daily-sales')
        ->dailyAt('23:00')
        ->withoutOverlapping();

    // Clean up expired sessions
    $schedule->command('auth:clear-resets')
        ->daily();

    // Sync stock levels
    $schedule->command('inventory:sync-levels')
        ->hourly()
        ->withoutOverlapping();
}
```

---

## 11. TESTING STRATEGY

### 11.1 Test Structure

```php
// tests/Pest.php
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
```

### 11.2 Example Tests

```php
// tests/Feature/Orders/OrderCreationTest.php
it('can create an order with items', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);
    $product = Product::factory()->create(['company_id' => $user->company_id]);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'branch_id' => $user->branches->first()->id,
            'warehouse_id' => $user->company->warehouses->first()->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_price' => $product->selling_price,
                ],
            ],
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'order_number', 'status', 'total_amount'],
        ]);

    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'status' => 'draft',
    ]);
});

it('cannot create order with insufficient stock', function () {
    // Test stock validation
});

it('validates order status transitions', function () {
    // Test state machine
});
```

### 11.3 Test Coverage Target

| Module | Target Coverage |
|--------|----------------|
| Core/Auth | 95% |
| Customers | 90% |
| Orders | 95% |
| Payments | 95% |
| Invoicing | 90% |
| Inventory | 90% |
| Delivery | 85% |
| Reports | 80% |
| **Overall** | **85%+** |

---

## 12. PERFORMANCE CONSIDERATIONS

### 12.1 Database Indexing Strategy
- All foreign keys indexed
- Composite indexes for common queries
- Partial indexes for filtered queries
- Full-text search for product names, customer names

### 12.2 Caching Strategy
- Redis for session, cache, and queue
- Query result caching for frequently accessed data
- Stock level caching with TTL
- Permission caching

### 12.3 Queue Strategy
- Redis queue driver
- Job batching for bulk operations
- Rate limiting for external API calls
- Retry logic with exponential backoff

### 12.4 Database Partitioning (Future)
- Order table partitioning by date
- Stock movement partitioning by month
- Activity log partitioning by month

---

## 13. SECURITY CONSIDERATIONS

- Laravel Sanctum for API authentication
- Spatie Permission for role-based access control
- CSRF protection for web routes
- XSS prevention through proper escaping
- SQL injection prevention through Eloquent
- Rate limiting on all API endpoints
- Input validation on all requests
- File upload validation and scanning
- Audit logging for all sensitive operations
- Encrypted sensitive data at rest

---

## 14. DEPLOYMENT ARCHITECTURE

```
┌─────────────────────────────────────────────┐
│                 Load Balancer                │
│              (AWS ALB / Nginx)              │
└──────────────────┬──────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
   ┌────▼────┐          ┌────▼────┐
   │ App 1   │          │ App 2   │
   │ (PHP-FPM)│         │ (PHP-FPM)│
   └────┬────┘          └────┬────┘
        │                     │
        └──────────┬──────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
   ┌────▼────┐          ┌────▼────┐
   │ MySQL   │          │ Redis   │
   │ Primary │          │ Cluster │
   └────┬────┘          └─────────┘
        │
   ┌────▼────┐
   │ MySQL   │
   │ Replica │
   └─────────┘
```

---

*This architecture document serves as the complete blueprint for the SUPPLY4ME Enterprise B2B Distribution ERP System. All modules, patterns, and structures are designed to support enterprise-scale operations with maintainability and extensibility as core principles.*