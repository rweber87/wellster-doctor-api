## Getting Started

To set up this project locally, follow the steps below:

### 1. Clone the Repository

Clone this repository to your local machine by running the following command:

```bash
git clone https://github.com/rweber87/wellster-doctor-api.git
```

2. Install Dependencies
   Navigate into the project directory and install the required PHP dependencies using Composer:

```bash
cd wellster-doctor-api
composer install
```

3. Set Up Environment Variables
   Copy the .env.example file to .env:

```bash
cp .env.example .env
```

Open the .env file and configure the database settings to match your local MySQL environment:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

4. Set Up MySQL Database
   Ensure that MySQL is installed on your local machine and create a new database:

```bash
mysql -u root -p
CREATE DATABASE your_database_name;
```

Replace your_database_name with the name of the database you want to use.

5. Run Migrations
   Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

6. Seed the Database
   (Optional) If you have seeders set up, you can populate the database with sample data:

```bash
php artisan db:seed
```

7. Serve the Application
   Start the Laravel development server:

```bash
php artisan serve
```

The application will be accessible at http://127.0.0.1:8000.

API Endpoints

GET `/api/doctors/{doctorId}` - Get details of a specific doctor.

GET `/api/doctors/{doctorId}/patients` - Get a list of patients assigned to the doctor, with optional sorting by last_name or appointment_date.

GET `/api/doctors/{doctorId}/matches` - Get unassigned patients matching the doctor's indications.

POST `/api/doctors/{doctorId}/assign-patient/{patientId}` - Assign a patient to a doctor if they match the doctor's indications.

GET `/api/unassigned-patients` - Get a list of all unassigned patients.

Contributing
Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the Laravel documentation.

Code of Conduct
In order to ensure that the Laravel community is welcoming to all, please review and abide by the Code of Conduct.

Security Vulnerabilities
If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via taylor@laravel.com. All security vulnerabilities will be promptly addressed.

License
The Laravel framework is open-sourced software licensed under the MIT license.
