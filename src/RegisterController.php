<?php

namespace App;
require_once __DIR__ . '/BoardRepository.php';

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
        $db = Database::getConnection();

        // TRANSAKCJA, TWORZY UZYTKOWNIKA ORAZ TWORZY JEDNA TABLICE DLA NIEGO
        try {
            $db->beginTransaction();
            $db->exec("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ");

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user = new User(null, $email, $username, $hashedPassword);
            $result = $this->userRepository->save($user);
            if (!$result) {
                throw new \Exception("Nie udało się zapisać użytkownika.");
            }

            $boardRepo = new BoardRepository($db);

            $createdUser = $this->userRepository->findByEmail($email);
            if (!$createdUser) {
                throw new \Exception("Nie udało się pobrać info o nowo utworzonym użytkowniku.");
            }

            $board = new Board(null, 'Moja pierwsza tablica', $createdUser->getId());
            $savedBoard = $boardRepo->save($board);
            if (!$savedBoard) {
                throw new \Exception("Nie udało się stworzyć tablicy startowej dla użytkownika.");
            }

            $db->commit();
            return true;

        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}
