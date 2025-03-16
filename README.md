# Logs Management Module for Magento 2

## Overview

This Magento 2 module allows administrators to view system logs directly from the admin panel without needing to access the server manually. It is especially useful when you don’t have access to specialized tools for log monitoring and want to quickly troubleshoot issues.

The module lets you configure the number of log file lines to display, starting from the most recent entries, providing a simple and efficient way to review your system logs.

## Compatibility

This module is compatible with Magento 2.4.4 or later.

## Features

- **View system logs** directly from the Magento admin panel.
- Configure **number of lines** to display from the most recent log entries.
- No need to manually access the server or use specialized logging tools.
- Supports standard Magento logs (e.g., `system.log`, `exception.log`) or even custom log files.

## Installation

### Composer Installation

1. Navigate to the root directory of your Magento 2 installation.
2. Run the following command to install the module via Composer:

   ```bash
   composer require cloudflexdev/module-logs-management
   ```
3. After the installation, enable the module and clear caches:

   ```bash
   bin/magento module:enable Cloudflex_LogsManagement
   bin/magento setup:upgrade
   bin/magento cache:clean
   ```
   
## Configuration

To configure the module:

1. Log in to the Magento admin panel.
2. Navigate to Stores > Configuration > Advanced > System > Logs Management.
3. Set the number of lines to be displayed for each log file (default: 5000 lines).

![Zrzut ekranu 2025-01-17 203555](https://github.com/user-attachments/assets/3933a369-088c-4168-8849-50fb9c512ad5)

⚠️ **Important:** Do not set too high a number of lines, as this may cause performance problems.

## Usage

Once the module is installed and configured:

1. In the Magento admin panel, go to System > Tools > Log Management.
2. Choose the log file you wish to view from the available options (e.g., `system.log`, `exception.log`).
3. The module will display the most recent log entries based on the configured number of lines.

![Zrzut ekranu 2025-01-17 203518](https://github.com/user-attachments/assets/34f9abc4-5edb-4346-a33e-5c2c0d5e01f0)

## Support

If you encounter any issues or need help, please open an issue on the GitHub repository: [GitHub Issues](https://github.com/quba546/module-logs-management/issues).

## License

This module is licensed under the [MIT License](https://opensource.org/licenses/MIT).
