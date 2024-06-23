<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // "user",
            // "users-list",
            // "update-user",
            // "delete-user",
            // "add-user",
            // "about",
            // "about-list",
            // "update-about",
            // "delete-about",
            // "add-about",
            // "role",
            // "roles-list",
            // "update-role",
            // "delete-role",
            // "add-role",
            // "apartment",
            // "apartment-list",
            // "update-apartment",
            // "delete-apartment",
            // "add-apartment",
            // "area",
            // "area-list",
            // "update-area",
            // "delete-area",
            // "add-area",
            // "city",
            // "city-list",
            // "update-city",
            // "delete-city",
            // "add-city",
            // "notifications",
            // "offer",
            // "offer-list",
            // "update-offer",
            // "delete-offer",
            // "add-offer",
            // "Payment",
            // "my-fatoorah-update",
            // "privacy",
            // "privacy-list",
            // "update-privacy",
            // "delete-privacy",
            // "add-privacy",
            // "all-payments",
            // "all-orders",
            // "setting",
            // "update-or-create-setting",
            // "term",
            // "term-list",
            // "update-term",
            // "delete-term",
            // "add-term",

            // "contact-us",
            // "control-notifications",
            // "control-notifications-list",
            // "control-notifications-edit-time",
            // "control-notifications-edit-description",
            // "notifications-show",
            // "notifications-delete",
            // "reports-reservation-requests",
            // "coupons",
            // "coupons-create",
            // "coupons-update",
            // "discounts",
            // "discounts-update",
            // "questions",
            // "questions-create",
            // "questions-update",
            // "questions-delete",
            // "admin-dashboard",
            // "coupons-delete",
            // "discounts-list",
            // "update-discounts",
            // "questions",


            "copy-apartment",
            "calendar-apartment",
            "coupons-delete",
            "notification-read-booking",
            "notification-markasread-booking",
            "notification-clear-booking",
            "notification-read-leaving",
            "notification-markasread-leaving",
            "notification-clear-leaving",
            "notification-read-workers",
            "notification-markasread-workers",
            "notification-clear-workers",
            "control-notifications-edit",
            "control-notifications-send-message",
            "invoice_layout_show",
            "invoice_layout_edit"


        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
