<?php

namespace App\Components;

use App\Core\Components\Component;
use App\Core\App;
use App\Models\User;
use App\Core\Service\SecurityManager;
use App\Core\Service\SessionManager;
use App\Core\Service\ValidationManager;
use App\Components\InformerComponent as Informer;
use Exception;

class UserComponent extends Component
{
    public array $validation_error = [];

    private array $validate_rules = [
        'login' => ['trim' => true, 'min_length' => 3, 'max_length' => 255],
        'password' => ['trim' => true, 'min_length' => 6, 'max_length' => 255]
    ];

    public function validate(string $field, string $value): bool
    {
        $validated = ValidationManager::validate($this->validate_rules[$field], $value);
        if (!$validated['result']) {
            $this->validation_error = $validated['errors'];
            return false;
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function checkUserExists(string $name): bool
    {
        $userModel = new User();
        $result = $userModel->where(
            [
                ['name', '=', $name]
            ]
        )->get();
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function checkUserByLoginAndPass(string $name, string $password): bool
    {
        $hashed_pass = SecurityManager::get_password_hash($password);
        $user = new User();
        $result = $user->where(
            [
                ['password', '=', $hashed_pass],
                ['name', '=', $name]]
        )->get();
        if (count($result) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @return array
     */
    public function getUserById($id): array
    {
        $user = new User();
        $foundUser = $user->find($id);
        return $foundUser ? $foundUser->get() : [];
    }

    /**
     * @param string $name
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function createUser(string $name, string $password): bool
    {
        $hashed_pass = SecurityManager::get_password_hash($password);
        App::DB()->insert('users', [
            'name' => $name,
            'password' => $hashed_pass,
            'created' => now_date(),
        ]);
        //Informer::adminNotifyEvent("create_user");
        return true;
    }

    public function logout(): void
    {
        SessionManager::remove('login');
    }

    public function login($login): void
    {
        SessionManager::set('login', $login);
    }

    /**
     * @throws Exception
     */
    public function getCurrentUser(): ?\App\Core\Database\Model
    {
        $login = SessionManager::get('login');
        $user = new User();
        return $user->where(
            [
                ['name', '=', $login]]
        )->first();
    }

}