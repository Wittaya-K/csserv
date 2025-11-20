https://csserv.dev.psu.ac.th/login
https://csserv.dev.psu.ac.th/logout


php artisan config:clear
php artisan route:clear
php artisan cache:clear


DELETE FROM service_request;
ALTER TABLE service_request AUTO_INCREMENT = 0;

DELETE FROM service_provider;
ALTER TABLE service_provider AUTO_INCREMENT = 0;

DELETE FROM service_request_note;
ALTER TABLE service_request_note AUTO_INCREMENT = 0;

DELETE FROM service_request_history;
ALTER TABLE service_request_history AUTO_INCREMENT = 0;