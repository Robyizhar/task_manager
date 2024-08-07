Setup database: 
- Open SQL editor on database tools like Navicat, Dbeaver, etc
- Copy the content of task_manager.sql inside the folder database into SQL editor.
- Change database settings such as username and password in the config.php file according to the settings on your computer.

Run project locally: 
1. Using Valet with NGINX (Mac Only)
     - Install Laravel Valet here (https://laravel.com/docs/11.x/valet)
     - Open the terminal and locate to project directory
     - Run command 'valet link'
     - Open the browser and type 'folder_name.test'
2. Using XAMPP/Laragon with Apache (Windows or Mac)
     - Download XAMPP or Laragon on their respective official websites.
     - Install the App and turn on the app
     - Placed the project inside www folder for Laragon or htdoc folder for     XAMPP
     - Open the browser and type 'localhost/folder_project_name'