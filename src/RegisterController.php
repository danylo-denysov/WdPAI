<?php

namespace App;

class RegisterController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(string $email, string $username, string $password, string $passwordRepeat): bool
    {
        if (empty($email) || empty($username) || empty($password) || empty($passwordRepeat)) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($password !== $passwordRepeat) {
            return false;
        }

        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            null,
            $email,
            $username,
            $hashedPassword
        );

        $result = $this->userRepository->save($user);

        return $result;
    }
}
