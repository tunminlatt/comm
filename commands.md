# Database
php artisan make:model Models/Sample -m
php artisan make:migration create_samples_table
php artisan make:migration add_ages_to_samples_table --table=samples
php artisan make:seeder SamplesTableSeeder
php artisan make:factory SampleFactory

# Panel CRUD
php artisan make:admincontroller Admin/SampleController -sliI
php artisan make:request Admin/StoreSampleRequest
php artisan make:request Admin/UpdateSampleRequest
php artisan make:repository SampleRepository -tm
php artisan make:resource Admin/SampleDatatableResource
php artisan make:view admin/samples -sliI

# API CRUD
php artisan make:apicontroller API/v1/SampleController -ascud
php artisan make:request API/v1/StoreSampleRequest
php artisan make:repository SampleRepository -tm
php artisan make:resource API/SampleResource
php artisan make:resource API/SampleCollection

# Job, Event
php artisan make:job Sample
php artisan make:event SampleEvent

# Cron
php artisan make:command SendEmails

# Rare
php artisan make:rule Sample
php artisan make:service SampleService