<?php

namespace App;

class LoginController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): bool
    {
        if (empty($email) || empty($password)) {
            return false;
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        session_start();
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['username'] = $user->getUsername();

        return true;
    }
}
