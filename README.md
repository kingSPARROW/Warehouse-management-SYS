# Warehouse Inventory System

Welcome to the Warehouse Inventory System project! This system aims to help manage and track various aspects of a warehouse's inventory, including products, gummies, packed boxes, and drying information. The system is built using PHP, MySQL, and other web technologies.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Database Structure](#database-structure)
- [Contributing](#contributing)
- [License](#license)

## Features

- Add, edit, and delete products in the warehouse.
- Store information about different types of gummies.
- Record details of packed boxes including products and employees.
- Track drying information for gummies with tray IDs and drying times.

## Installation

1. Clone this repository to your web server's root directory.
2. Create a MySQL database for the project and import the SQL file (`database.sql`) provided in the `sql` directory.
3. Configure the database connection by editing the `includes/config.php` file with your database credentials.
4. Make sure to install the required dependencies, such as the `Picqer\Barcode\BarcodeGeneratorPNG` library using Composer.

```bash
composer install
Usage
Access the system through your web browser by navigating to http://your-server-address/warehouse-inventory-system.

Log in using appropriate user credentials (various user levels are supported).

Use the navigation menu to access different sections of the system: Products, Gummies, Packed Boxes, and Drying.

Add, edit, or delete data as needed using the provided forms and interfaces.

Database Structure
The project's database includes the following tables:

products: Store information about different products.
gummies: Record details about different gummy products.
packed_box: Track packed boxes with employee, product, and quantity information.
drying: Store drying information for gummies, including tray IDs and drying times.
batches_gummies: Record batches associated with gummies, including batch numbers and sizes.
Contributing
Contributions to this project are welcome! If you find any issues or want to add new features, feel free to open an issue or submit a pull request.

License
This project is licensed under the MIT License.
