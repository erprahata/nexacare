# NexaCare Project Structure

Directory map for the NexaCare project.

Excluded from this map:

- `node_modules/`
- `vendor/`
- `.git/`
- `public/build/`
- `storage/framework/`
- `storage/logs/`

```text
nexacare/
|-- app/
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Auth/
|   |   |   |   |-- AuthenticatedSessionController.php
|   |   |   |   |-- ConfirmablePasswordController.php
|   |   |   |   |-- EmailVerificationNotificationController.php
|   |   |   |   |-- EmailVerificationPromptController.php
|   |   |   |   |-- NewPasswordController.php
|   |   |   |   |-- PasswordController.php
|   |   |   |   |-- PasswordResetLinkController.php
|   |   |   |   |-- RegisteredUserController.php
|   |   |   |   \-- VerifyEmailController.php
|   |   |   |-- AdmissionController.php
|   |   |   |-- CashierController.php
|   |   |   |-- Controller.php
|   |   |   |-- EmrController.php
|   |   |   |-- PharmacyController.php
|   |   |   \-- ProfileController.php
|   |   |-- Middleware/
|   |   |   |-- HandleInertiaRequests.php
|   |   |   \-- RoleMiddleware.php
|   |   \-- Requests/
|   |       |-- Auth/
|   |       |   \-- LoginRequest.php
|   |       \-- ProfileUpdateRequest.php
|   |-- Models/
|   |   |-- Appointment.php
|   |   |-- Clinic.php
|   |   |-- Invoice.php
|   |   |-- InvoiceItem.php
|   |   |-- MedicalRecord.php
|   |   |-- Medicine.php
|   |   |-- Patient.php
|   |   |-- Prescription.php
|   |   |-- PrescriptionItem.php
|   |   \-- User.php
|   \-- Providers/
|       \-- AppServiceProvider.php
|-- bootstrap/
|   |-- cache/
|   |   |-- .gitignore
|   |   |-- packages.php
|   |   \-- services.php
|   |-- app.php
|   \-- providers.php
|-- config/
|   |-- app.php
|   |-- auth.php
|   |-- cache.php
|   |-- database.php
|   |-- filesystems.php
|   |-- logging.php
|   |-- queue.php
|   |-- services.php
|   \-- session.php
|-- database/
|   |-- factories/
|   |   \-- UserFactory.php
|   |-- migrations/
|   |   |-- 0000_01_01_000000_create_clinics_table.php
|   |   |-- 0001_01_01_000000_create_users_table.php
|   |   |-- 0001_01_01_000001_create_cache_table.php
|   |   |-- 0001_01_01_000002_create_jobs_table.php
|   |   |-- 2026_08_19_180504_create_patients_table.php
|   |   |-- 2026_08_19_180505_create_appointments_table.php
|   |   |-- 2026_08_19_181704_create_medicines_table.php
|   |   |-- 2026_08_19_181705_create_medical_records_table.php
|   |   |-- 2026_08_19_181706_create_prescriptions_table.php
|   |   |-- 2026_08_19_181707_create_prescription_items_table.php
|   |   |-- 2026_08_19_181708_create_invoices_table.php
|   |   |-- 2026_08_19_181709_create_invoice_items_table.php
|   |   \-- 2026_08_20_185338_add_nik_to_patients_table.php
|   |-- seeders/
|   |   \-- DatabaseSeeder.php
|   |-- .gitignore
|   \-- database.sqlite
|-- public/
|   |-- .htaccess
|   |-- favicon.ico
|   |-- index.php
|   \-- robots.txt
|-- resources/
|   |-- css/
|   |   \-- app.css
|   |-- js/
|   |   |-- Components/
|   |   |   |-- ApplicationLogo.vue
|   |   |   |-- Checkbox.vue
|   |   |   |-- DangerButton.vue
|   |   |   |-- Dropdown.vue
|   |   |   |-- DropdownLink.vue
|   |   |   |-- GlassButton.vue
|   |   |   |-- GlassCheckbox.vue
|   |   |   |-- GlassDatePicker.vue
|   |   |   |-- GlassInput.vue
|   |   |   |-- GlassSelect.vue
|   |   |   |-- GlassTextarea.vue
|   |   |   |-- GlassToast.vue
|   |   |   |-- InputError.vue
|   |   |   |-- InputLabel.vue
|   |   |   |-- Modal.vue
|   |   |   |-- NavLink.vue
|   |   |   |-- PrimaryButton.vue
|   |   |   |-- ResponsiveNavLink.vue
|   |   |   |-- SecondaryButton.vue
|   |   |   \-- TextInput.vue
|   |   |-- Layouts/
|   |   |   |-- AuthenticatedLayout.vue
|   |   |   \-- GuestLayout.vue
|   |   |-- Pages/
|   |   |   |-- Auth/
|   |   |   |   |-- ConfirmPassword.vue
|   |   |   |   |-- ForgotPassword.vue
|   |   |   |   |-- Login.vue
|   |   |   |   |-- Register.vue
|   |   |   |   |-- ResetPassword.vue
|   |   |   |   \-- VerifyEmail.vue
|   |   |   |-- Cashier/
|   |   |   |   \-- Dashboard.vue
|   |   |   |-- Doctor/
|   |   |   |   \-- EmrForm.vue
|   |   |   |-- Frontdesk/
|   |   |   |   \-- Register.vue
|   |   |   |-- Profile/
|   |   |   |   |-- Partials/
|   |   |   |   |   |-- DeleteUserForm.vue
|   |   |   |   |   |-- UpdatePasswordForm.vue
|   |   |   |   |   \-- UpdateProfileInformationForm.vue
|   |   |   |   \-- Edit.vue
|   |   |   |-- Dashboard.vue
|   |   |   |-- Farmasi.vue
|   |   |   \-- Welcome.vue
|   |   |-- app.js
|   |   \-- bootstrap.js
|   \-- views/
|       \-- app.blade.php
|-- routes/
|   |-- auth.php
|   |-- console.php
|   \-- web.php
|-- storage/
|   \-- app/
|       |-- private/
|       |   \-- .gitignore
|       |-- public/
|       |   \-- .gitignore
|       \-- .gitignore
|-- tests/
|   |-- Feature/
|   |   |-- Auth/
|   |   |   |-- AuthenticationTest.php
|   |   |   |-- EmailVerificationTest.php
|   |   |   |-- PasswordConfirmationTest.php
|   |   |   |-- PasswordResetTest.php
|   |   |   |-- PasswordUpdateTest.php
|   |   |   \-- RegistrationTest.php
|   |   |-- ExampleTest.php
|   |   \-- ProfileTest.php
|   |-- Unit/
|   |   \-- ExampleTest.php
|   \-- TestCase.php
|-- .editorconfig
|-- .env
|-- .env.example
|-- .gitattributes
|-- .gitignore
|-- .npmrc
|-- .phpunit.result.cache
|-- artisan
|-- composer.json
|-- composer.lock
|-- jsconfig.json
|-- NexaCare_PRD_v2.md
|-- NexaCare_PRD_v2.pdf
|-- package.json
|-- package-lock.json
|-- phpunit.xml
|-- postcss.config.js
|-- README.md
|-- tailwind.config.js
\-- vite.config.js
```
