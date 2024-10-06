# Assessment-for-Software-Engineer-Level-II
# WPFunnels Custom Functions

This file, [`manipulate-checkout-data.php`](https://github.com/HasanJahidul/Assessment-for-Software-Engineer-Level-II/blob/main/wpfunnels/public/modules/checkout/manupulate-checkout-data.php), contains custom functions to handle checkout data in WPFunnels. The three main functions included are:

1. **`store_customer_data()`**
2. **`create_customer_table()`**
3. **`check_customer_count()`**

## Function Overview

### 1. `store_customer_data()`
- This function is responsible for persisting customer order information in a custom database table called `wp_wpfnl_customer_storage`.
- It stores data such as:
  - `name`
  - `email`
  - `order_id`
  - `products`
  - `store_name`
  
This data is used when sending a "thank you" email after the purchase.

### 2. `create_customer_table()`
- Before storing the data, this function checks whether the custom table (`wp_wpfnl_customer_storage`) exists in the database.
- If the table doesn’t exist, this function creates it, ensuring the order information can be stored properly.

### 3. `check_customer_count()`
- After storing customer data, a query checks if the customer count has reached 100.
- If the count reaches 100, the function triggers `send_thank_you_emails()`, which sends a thank you email to all 100 customers.
- After the emails are sent, the customer data is deleted from the database to keep the table clean.

## Integration
The function `store_customer_data()` is called within the `save_checkout_fields()` function, which is triggered during the checkout process. This ensures that the custom logic for handling customer data and sending thank you emails is seamlessly integrated into the checkout workflow.
