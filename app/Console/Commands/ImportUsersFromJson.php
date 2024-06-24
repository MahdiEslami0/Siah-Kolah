<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Webinar;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportUsersFromJson extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Import users from a JSON file';

    public function handle()
    {
        // Path to the JSON file
        $jsonFile = storage_path('app/users.json');

        // Check if the file exists
        if (!File::exists($jsonFile)) {
            $this->error("File not found: $jsonFile");
            return;
        }

        // Read and decode the JSON file
        $jsonData = File::get($jsonFile);
        $users = json_decode($jsonData, true);

        if (!$users) {
            $this->error("Invalid JSON data");
            return;
        }

        foreach ($users as $user) {
            $email = $user['Email'];
            $phone = $user['Phone'];
            $fullName = $user['Full name'];
            $slug = $user['slug'];

            $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
            $isPhoneValid = !empty($phone);

            if ($isEmailValid || $isPhoneValid) {
                try {
                    error_log('Processing user - Email: ' . ($isEmailValid ? $email : 'N/A') . ', Phone: ' . ($isPhoneValid ? $phone : 'N/A'));

                    // Check for existing user
                    $existingUser = User::where(function ($query) use ($email, $phone, $isEmailValid, $isPhoneValid) {
                        if ($isEmailValid) {
                            $query->where('email', $email);
                        }
                        if ($isPhoneValid) {
                            $query->orWhere('mobile', $phone);
                        }
                    })->first();

                    $dataToUpdate = [
                        'full_name' => $fullName,
                        'role_name' => 'user',
                        'role_id' => '1',
                        'updated_at' => time(),
                    ];

                    if ($isEmailValid) {
                        $dataToUpdate['email'] = $email;
                    }
                    if ($isPhoneValid) {
                        $dataToUpdate['mobile'] = $phone;
                    }

                    if ($existingUser) {
                        $existingUser->update($dataToUpdate);
                        $userId = $existingUser->id;
                    } else {
                        $dataToInsert = array_merge($dataToUpdate, [
                            'created_at' => time(),
                        ]);
                        $createdUser = User::create($dataToInsert);
                        $userId = $createdUser->id;
                    }
                    // if (isset($slug)) {
                    //     $webinar = Webinar::where('slug', $slug)->first();
                    //     if (isset($webinar)) {
                    //         Sale::create([
                    //             'buyer_id' => $userId,
                    //             'type' => 'webinar',
                    //             'webinar_id' => $webinar->id,
                    //             'amount' => $webinar->price,
                    //             'created_at' => time(),
                    //         ]);
                    //     } else {
                    //         error_log('Webinar not found - Slug: ' . $slug);
                    //         continue;
                    //     }
                    // }
                } catch (\Exception $e) {
                    error_log('Error updating/inserting user or creating sale: ' . $e->getMessage());
                }
            } else {
                error_log('Invalid data - Email: ' . $email . ', Phone: ' . $phone);
            }
        }


        $this->info('Users imported successfully.');
    }
}
