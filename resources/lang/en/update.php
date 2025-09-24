<?php

return [
    'title' => 'Update Wizard',
    'welcome_title' => 'Welcome to Update',
    'welcome_message' => 'Welcome to the Laravel Application update wizard. This wizard will help you update your application to the latest version.',
    'welcome_description' => 'The update process includes the following steps:',
    'current_version' => 'Current Version',
    'new_version' => 'New Version',
    'update_available' => 'Update Available',
    'no_update_required' => 'No update required. You are running the latest version.',

    // Steps
    'steps' => [
        'welcome' => 'Welcome',
        'requirements' => 'Requirements',
        'license' => 'License',
        'update' => 'Update',
        'success' => 'Success',
    ],

    // Step descriptions
    'step_descriptions' => [
        'welcome' => 'Welcome and version info',
        'requirements' => 'Check system requirements',
        'license' => 'Verify license key',
        'update' => 'Perform update',
        'success' => 'Update completed',
    ],

    // Requirements (reuse from install)
    'requirements_title' => 'System Requirements',
    'requirements_description' => 'Please ensure your system meets the following requirements for the update:',
    'requirements_check' => 'Check Requirements',
    'requirements_recheck' => 'Re-check Requirements',
    'requirements_all_met' => 'All requirements are met! You can proceed with the update.',
    'requirements_some_failed' => 'Some requirements are not met. Please fix them before continuing.',

    // License (reuse from install)
    'license_title' => 'License Verification',
    'license_description' => 'Please verify your license key to proceed with the update.',
    'license_key' => 'License Key',
    'license_key_placeholder' => 'Enter your license key',
    'verify_license' => 'Verify License',
    'license_valid' => 'License is valid!',
    'license_invalid' => 'Invalid license key. Please check and try again.',
    'license_required' => 'A valid license key is required to proceed.',

    // Update process
    'update_title' => 'Update Process',
    'update_description' => 'Select the update options and start the update process.',
    'update_options' => 'Update Options',
    'run_migrations' => 'Run Database Migrations',
    'import_dump' => 'Import Database Dump',
    'clear_cache' => 'Clear Application Cache',
    'cache_config' => 'Cache Configuration',
    'start_update' => 'Start Update',
    'update_in_progress' => 'Update in progress...',
    'update_completed' => 'Update completed successfully!',
    'update_failed' => 'Update failed. Please check the logs and try again.',

    // Success
    'success_title' => 'Update Complete!',
    'success_message' => 'Your application has been successfully updated.',
    'success_description' => 'You are now running the latest version.',
    'new_version_installed' => 'New version installed',
    'go_to_application' => 'Go to Application',
    'view_changelog' => 'View Changelog',

    // Common messages
    'processing' => 'Processing...',
    'updating' => 'Updating...',
    'please_wait_updating' => 'Please wait while we update your application.',
    'backup_recommended' => 'It is recommended to backup your application before updating.',
    'before_you_update' => 'Before You Update',
    'update_process_steps' => 'Update Process Steps',
];
