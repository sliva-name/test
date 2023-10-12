<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class ImportUserController extends Controller
{
    public function __invoke()
    {
        try {
            $users = Http::get('https://randomuser.me/api/?results=5000')->json();
        } catch (ConnectException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        if (!isset($users['results'])) {
            return response()->json($users['error']);
        }

        $usersCount = User::count();

        DB::beginTransaction();

        try {
            foreach ($users['results'] as $user) {
                $userData = [
                    'first_name' => $user['name']['first'],
                    'last_name' => $user['name']['last'],
                    'age' => $user['dob']['age'],
                    'email' => $user['email'],
                ];

                $conditions = [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                ];

                User::updateOrInsert($conditions, $userData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }

        $usersCount2 = User::count();
        $addedUsers = $usersCount2 - $usersCount;
        $updatedCount = 5000 - $addedUsers;


        return response()->json(['updated_count' => $updatedCount, 'count' => $usersCount2, 'added_users' => $addedUsers]);
    }
}
