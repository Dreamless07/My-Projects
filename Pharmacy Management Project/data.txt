CREATE TABLE pharmacist (
  pharmacist_id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(200),
  lastname VARCHAR(200),
  email VARCHAR(200),
  password VARCHAR(255),
  gender VARCHAR(15),
  phone_number VARCHAR(20),
  address TEXT
);

CREATE TABLE medicine (
  pharmacist_id INT,
  medicine_id INT AUTO_INCREMENT PRIMARY KEY,
  medicine_name VARCHAR(255),
  expiry_date DATE,
  price_per_unit DECIMAL(10, 2),
  FOREIGN KEY (pharmacist_id) REFERENCES pharmacist(pharmacist_id)
);

CREATE TABLE customer (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(255),
  gender VARCHAR(15),
  phone_number VARCHAR(20),
  address TEXT,
  pharmacist_id INT,
  FOREIGN KEY (pharmacist_id) REFERENCES pharmacist(pharmacist_id)
);

CREATE TABLE transaction (
  transaction_id INT AUTO_INCREMENT PRIMARY KEY,
  medicine_id INT,
  customer_id INT,
  quantity INT,
  cost DECIMAL(10, 2),
  tax DECIMAL(5, 2),
  pharmacist_id INT,
  FOREIGN KEY (medicine_id) REFERENCES medicine(medicine_id),
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (pharmacist_id) REFERENCES pharmacist(pharmacist_id)
);

CREATE TABLE sales (
  sales_id INT AUTO_INCREMENT PRIMARY KEY,
  medicine_id INT,
  customer_id INT,
  transaction_id INT,
  no_of_units_sold INT,
  sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  pharmacist_id INT,
  FOREIGN KEY (medicine_id) REFERENCES medicine(medicine_id),
  FOREIGN KEY (customer_id) REFERENCES customer(customer_id),
  FOREIGN KEY (transaction_id) REFERENCES transaction(transaction_id),
  FOREIGN KEY (pharmacist_id) REFERENCES pharmacist(pharmacist_id)
);
