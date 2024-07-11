<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/olivierluethy/Prio">
    <img src="images/favicon.ico" alt="Logo" width="200" height="200">
  </a>

  <h3 align="center">Prio</h3>

  <p align="center">
    This document provides an overview of the Prio project, detailing its purpose and installation steps.
    <br />
    <a href="https://github.com/olivierluethy/Prio/blob/main/README.md"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/olivierluethy/Prio">View Demo</a>
    ·
    <a href="https://github.com/olivierluethy/Prio/issues">Report Bug</a>
    ·
    <a href="https://github.com/olivierluethy/Prio/issues">Request Feature</a>
  </p>
</p>

<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li><a href="#about-the-project">About The Project</a></li>
    <li><a href="#installation-guide">Installation Guide</a></li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#possible-adjustments">Possible Adjustments</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## About The Project

This project was originally a task for the third Mini-PA (MPA). However, it will continue to be developed in this repository.

This project is specifically about actually doing the things you set out to do. For example, you often set a lot of new goals for the new year or the new age. The only problem is that you don't describe them in a meaningful way, what exactly you want to achieve, how much you want to achieve, so that they are as realistic and achievable as possible. This app is designed to help you solve this problem in the best possible way.

In this app, you can add tasks or goals that you want to complete or achieve. You define a title and describe what you want to accomplish and the motivation behind it. Why do you want to accomplish it and what are the benefits?


The project also has a time tracker where you can click the clock once you start working on it. You can also stop it at the end and briefly describe exactly what you did. In the time overview, all tasks are clearly displayed with all times and added up at the end.

If you have any other wishes, please let us know!

<!-- INSTALLATION -->
## Installation Guide

### Prerequisites

1. **Install Git**
   - Download Git from the [official website](https://git-scm.com/downloads).
   - Follow the installation instructions for your operating system.

2. **Install Composer**
   - Download Composer from the [official website](https://getcomposer.org/download/).
   - Follow the installation instructions provided for your operating system.

### Steps

1. **Clone the Repository**
   ```sh
   git clone https://github.com/olivierluethy/Prio
   ```

2. **Navigate to the Project Directory**
   ```sh
   cd /path/to/your/project
   ```

3. **Install Project Dependencies**
   ```sh
   composer require vlucas/phpdotenv
   ```

4. **Configure Environment Variables**
   - Create a `.env` file in the root directory:
     ```env
     APP_ENV=local
     APP_DEBUG=true
     DATABASE_URL=mysql://user:password@localhost/database
     ```

   - Load the environment variables in your PHP script:
     ```php
     <?php
     require __DIR__ . '/vendor/autoload.php';

     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
     $dotenv->load();

     $appEnv = getenv('APP_ENV');
     $appDebug = getenv('APP_DEBUG');
     $databaseUrl = getenv('DATABASE_URL');
     ```

## Usage

- After installation, run the application to manage and track your tasks and goals.
- Refer to the documentation for detailed usage instructions.

<!-- POSSIBLE ADJUSTMENTS -->
## Possible Adjustments

This project utilizes CKEditor, which transforms your text area into a rich text editor with various styling options. To customize the toolbar, visit the [CKEditor documentation](https://ckeditor.com/docs/ckeditor4/latest/features/toolbar.html).

If you want to download it again separately, check out this [website](https://ckeditor.com/docs/ckeditor4/latest/guide/dev_installation.html)!

<!-- CONTRIBUTING -->
## Contributing

We welcome contributions to the project. To contribute:

1. Fork the repository.
2. Create a feature branch (`git checkout -b feature/AmazingFeature`).
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the branch (`git push origin feature/AmazingFeature`).
5. Open a pull request.

Please ensure that your contributions adhere to the [Coding Conventions](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

<!-- LICENSE -->
## License

This project is licensed under the [MIT License](LICENSE).

<!-- CONTACT -->
## Contact

For further information, contact the project maintainer [Olivier Luethy](https://github.com/olivierluethy).