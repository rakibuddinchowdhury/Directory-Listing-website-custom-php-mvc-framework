# 📍 BizFinder - Custom PHP Directory Listing Platform

A robust, fully functional **Local Business Directory & Listing Platform** built from scratch using **Native PHP (Object Oriented)** and **MySQL**. 

This project demonstrates a custom MVC-like architecture without relying on heavy frameworks (like Laravel), showcasing deep understanding of core web development concepts including authentication, database design, security, and role-based access control.

---

## 🚀 Key Features

### 🔹 For Users (Public)
* **Dynamic Search & Filtering:** Filter listings by Keyword, Category, and Location.
* **Listing Details:** View business info, location, and photos.
* **User Accounts:** Register, Login, and manage profiles.
* **Favorites System:** Save listings to a personal wishlist.
* **Contact Business:** Send direct messages/leads to vendors.

### 🔹 For Vendors (Business Owners)
* **Vendor Dashboard:** A dedicated panel to manage their business presence.
* **Listing Management:** Add, Edit, and Delete listings.
* **Image Uploads:** Drag-and-drop image uploading with live preview.
* **Inbox:** View messages/leads sent by customers.
* **Performance Stats:** View total views and engagement metrics.

### 🔹 For Admins
* **Master Dashboard:** Overview of total users, listings, and revenue.
* **Content Moderation:** Approve or Reject submitted listings.
* **User Management:** Ban users or manage roles.
* **Category & Location Manager:** Add dynamic categories (with icons) and cities.
* **Site Settings:** Update site name, footer text, and about content directly from the CMS.

---

## 🛠️ Tech Stack

* **Backend:** PHP (OOP, PDO for database connections).
* **Database:** MySQL (Relational Schema).
* **Frontend:** HTML5, CSS3, **Tailwind CSS** (CDN).
* **JavaScript:** Vanilla JS (AJAX, DOM Manipulation).
* **Architecture:** Custom MVC (Model-View-Controller) structure.

---

## 📂 Project Structure

```text
/admin          # Admin Panel pages (CMS, Settings, Users)
/assets         # CSS, JS, and Uploaded Images
/config         # Database Connection Class
/controllers    # Logic for Auth, Listings, Reviews
/dashboard      # Vendor Dashboard (Add Listing, Inbox)
/includes       # Reusable Header, Footer, Navbar
/user           # User Profile & Saved Listings
install.php     # Automatic Database Installer
dummy-data.php  # Script to populate site with test listings
index.php       # Homepage


⚡ Installation & Setup
This project comes with an Automated Installer to set up the database for you.

Clone the Repository
git clone [https://github.com/rakibuddinchowdhury/Directory-Listing-website-custom-php-mvc-framework.git](https://github.com/rakibuddinchowdhury/Directory-Listing-website-custom-php-mvc-framework.git)
Move to Server

Place the folder inside your htdocs (XAMPP) or www (WAMP) directory.
Run the Installer

Open your browser and visit: http://localhost/directory-app/install.php
Enter your Database credentials (usually User: root, Pass: ).

Click Run Installer. This creates the DB, Tables, and Admin User.
Populate Dummy Data (Optional)
Visit: http://localhost/directory-app/dummy-data.php
This will instantly create 25+ listings with real images.

🔐 Login Credentials
After running install.php, use these credentials to access the Admin Panel:

Email: admin@gmail.com
Password: 12345678
