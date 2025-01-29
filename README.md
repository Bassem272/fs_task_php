
# Backend Project

This is the backend of the system, handling the server-side logic for managing the data and interacting with a database via GraphQL queries and mutations. The application uses PHP and GraphQL to manage models securely and efficiently.

## Features

- GraphQL API to interact with the data
- Modular design with separate GraphQL mutations, queries, and types
- Database protection with SQL injection mitigation and reusable model logic
- Abstraction and inheritance for clean code and reduced duplication
- Scalable and secure models that manage core entities like categories, products, and orders

## Folder Structure

```
├── index.php                    # Entry point for the application
├── App/
│   ├── schema.php               # Defines the GraphQL schema and operations
│   ├── Database/
│   │   ├── Connection.php       # Handles database connection
│   ├── GraphQL/
│   │   ├── Mutations/
│   │   │   ├── CreateOrderMutation.php   # Mutation to create orders
│   │   │   ├── MutationType.php          # Mutation type definition
│   │   ├── Queries/
│   │   │   ├── CategoriesQuery.php      # Fetch categories
│   │   │   ├── ProductQuery.php         # Fetch product by ID
│   │   │   ├── ProductsQuery.php       # List all products
│   │   │   ├── QueryType.php           # Query type definition
│   │   ├── Types/
│   │   │   ├── AttributeItemType.php   # Type for attributes in a product
│   │   │   ├── AttributeType.php       # Type for product attributes
│   │   │   ├── CategoryType.php        # Category type definition
│   │   │   ├── GalleryType.php         # Product gallery type
│   │   │   ├── OrderItemInputType.php # Type for input in order creation
│   │   │   ├── OrderType.php          # Type for order structure
│   │   │   ├── PriceType.php          # Product price details
│   │   │   ├── ProductType.php        # Product type definition
│   ├── Models/
│   │   ├── CategoryModel.php          # Model for categories
│   │   ├── OrderModel.php             # Model for orders
│   │   ├── ProductModel.php           # Model for products
│   │   ├── BaseModel.php              # Base model class for shared functionality
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd backend
   ```

3. Install PHP dependencies using Composer:
   ```
   composer install
   ```

4. Set up database configurations inside `Connection.php`.

5. Ensure your GraphQL server is properly set up to accept requests.

## Running the Application Locally

You can run the backend locally using PHP’s built-in server. First, navigate to the project directory, then run the following command to start a local server:

```
php -S localhost:8080
```

This will start a development server on `http://localhost:8080`, where you can send GraphQL queries and mutations.

To interact with the GraphQL server:
- `POST /graphql` for querying or mutating data.

### Available Queries:

- `CategoriesQuery` – List all product categories.
- `ProductsQuery` – List all products or a filtered list of products.
- `ProductQuery` – Get a single product by ID.

### Available Mutations:

- `CreateOrderMutation` – Create new orders.
  
## Models

The system uses secure, reusable models for core entities like categories, orders, and products:

- **CategoryModel**: Handles database interaction for categories.
- **OrderModel**: Handles database interaction for orders.
- **ProductModel**: Handles database interaction for products.
- **BaseModel**: A reusable abstract model providing basic CRUD operations with protection against SQL injection.

## GraphQL Schema

The schema defines how the data is fetched and interacted with. It includes types for `Order`, `Product`, `Category`, etc., along with various queries and mutations to handle the operations.

## Best Practices & Security

- **SQL Injection Protection**: Models use prepared statements with parameters, ensuring safe database queries.
- **Object-Oriented Design**: Models use inheritance and abstraction for code reusability and maintainability.
- **Modular GraphQL Operations**: Separate mutations, queries, and types for a cleaner, more scalable approach to building the API.
  
## Contributing

Feel free to contribute to the project by opening issues or submitting pull requests. Ensure that you follow the structure for extending models, mutations, and queries, and maintain security best practices when modifying the database.

For detailed information or questions, feel free to contact the backend development team.

 ## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

