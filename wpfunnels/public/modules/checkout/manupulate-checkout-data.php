<?php
namespace WPFunnels\Modules\Frontend\Checkout;

class CheckoutManipulator {
    // Use a constant for the customer threshold
    private const CUSTOMER_THRESHOLD = 2;

    // ** Customer storage function **
    public function store_customer_data($order) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpfnl_customer_storage';

        // Create the table if it doesn't exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $this->create_customer_table();
        }

        // Get customer details
        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $email = $order->get_billing_email();
        $order_id = $order->get_id();

        // Get the list of products from the order and concatenate their names
        $products = $order->get_items();
        $products_name = implode(', ', array_map(function($product_detail) {
            return $product_detail->get_name();
        }, $products));

        // Insert customer data into the custom table
        $inserted = $wpdb->insert($table_name, array(
            'name' => $name,
            'email' => $email,
            'order_id' => $order_id,
            'products' => $products_name,
            'store_name' => get_bloginfo('name')
        ));

        // Check if the data was inserted successfully
        if ($inserted === false) {
            error_log('Failed to insert customer data into the database.');
            return;
        }

        // Check if the threshold is reached and trigger emails
        $customer_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($customer_count >= self::CUSTOMER_THRESHOLD) {
            $this->send_thank_you_emails();
        }
    }

    // ** Function to create the custom table **
    public function create_customer_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpfnl_customer_storage';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email text NOT NULL,
            order_id bigint(20) NOT NULL,
            products text NOT NULL,
            store_name text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // ** Email sending function **
    public function send_thank_you_emails() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpfnl_customer_storage';

        // Fetch the customers based on the threshold
        $customers = $wpdb->get_results("SELECT * FROM $table_name LIMIT " . self::CUSTOMER_THRESHOLD);

        // Check if customers are found before attempting to send emails
        if (empty($customers)) {
            return;
        }

        foreach ($customers as $customer) {
            $to = $customer->email;
            $subject = "Thank You for Your Purchase!";
            $message = "Thank you for your recent purchase from " . $customer->store_name . "! We’re excited to have you as a customer and we’re confident you’ll love your new " . $customer->products . ".";
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // Send the email and log an error if the email fails
            if (!wp_mail($to, $subject, $message, $headers)) {
                error_log("Failed to send email to {$to}");
            }
        }

        // Delete the customers from the table after sending emails
        $deleted = $wpdb->query("DELETE FROM $table_name LIMIT " . self::CUSTOMER_THRESHOLD);

        // Log if deletion fails
        if ($deleted === false) {
            error_log('Failed to delete customers after sending emails.');
        }
    }
}
