# Projekt Tablicy Zadań (TaskPilot)

Aplikacja umożliwia tworzenie i zarządzanie tablicami zadań oraz kolumnami, w których znajdują się zadania. Dzięki temu można wygodnie organizować projekty i przypisywać zadania do użytkowników.

## Funkcjonalności

1. **Rejestracja i logowanie użytkownika**  
   - Po rejestracji (lub zalogowaniu) przechodzimy do ekranu głównego `home.php`, gdzie widzimy listę dostępnych tablic.  
   - Hasła są hashowane (przy użyciu `password_hash()` w PHP).

2. **Zarządzanie tablicami**  
   - Każdy użytkownik może tworzyć własne tablice (jest ich właścicielem – `owner`).  
   - Właściciel może zapraszać innych użytkowników do tablicy (z rolą `editor` lub `viewer`).  
   - Tablice można usuwać lub edytować ich nazwę.

3. **Zarządzanie kolumnami (grupami zadań)**  
   - W danej tablicy można tworzyć oraz edytować wiele kolumn (np. „Do zrobienia”, „W trakcie”, „Zrobione”).  
   - Można je edytować i usuwać.  

4. **Zarządzanie zadaniami**  
   - Każda kolumna zawiera wiele zadań.  
   - W każdej kolumnie można tworzyć zadania i edytować.  
   - Obsługiwane jest **drag & drop** do przenoszenia zadań między kolumnami.  

5. **Rola użytkownika**  
   - `owner` – pełne prawa do zarządzania tablicą, użytkownikami, usuwania itp.  
   - `editor` – może edytować zadania/kolumny, ale nie zarządza użytkownikami.  
   - `viewer` – tylko przegląd, bez możliwości edycji.

6. **Usuwanie konta**  
   - Użytkownik może usunąć konto. Dzięki `ON DELETE CASCADE` w bazie usuwane są także jego tablice, kolumny i zadania, by nie pozostawały nieużywane.

---

## Uruchamianie projektu w Dockerze

### 1. Klonuj repozytorium
```bash
git clone https://github.com/danylo-denysov/WdPAI
```

### 2. Pliki Docker
  - `docker-compose.yml` uruchamia dwa serwisy:
      - php – kontener z PHP 8.1 + Apache, który serwuje nasz kod z katalogu public/
      - db – kontener z PostgreSQL, z plikiem init.sql do inicjalizacji bazy.
  - `Dockerfile` wgrywa kod do /var/www/html i ustawia DocumentRoot na /var/www/html/public.

### 3. Jak uruchomić
```bash
docker-compose up --build
```
Jeśli w przeszłości baza była już utworzona i init.sql nie wykonuje się ponownie
```bash
docker-compose down -v
docker-compose up --build
```
Wejść w przeglądarce na `http://localhost:8080`

## Baza danych

### 1. Wizualizacja tabel (oraz dwa widoki) sie znajduje w pliku `Database_schema.png`

### 2. Krótki opis bazy
  - `users` - Przechowuje dane logowania (email, zahashowane hasło, username).
  - `boards` - Tablice (projekty), właściciel (owner_id) kaskadowo usuwa tablicę.
  - `board_users` - Zbiorcza tabela użytkowników tabel z rolami.
  - `task_groups` - Kolumny przypisane do tablicy.
  - `tasks` - Zadania przypisane do kolumn
Dodatkowo:
  - Widoki
     - `view_user_boards` - informacja o tablicach oraz ich właścicielach, pokazuje czas stworzenia
     - `view_tasks_info` - pełna informacja o zadaniach w tablicach
  - Wyzwalacz i funkcja
     - `tasks_created_at_trigger` - ustawia date utworzenia dla zadan za pomocą funkcji `set_task_created_at()`
   
  ---


# Wymagania do projektu

## Użyte technologie
  - HTML5
  - CSS
  - JavaScript (FetchAPI)
  - PHP
  - Baza danych PostgreSQL

## Design, FetchAPI
  - Użycie media quieries w plikach `.css`
  - Użycie JavaScript FetchAPI w `dragdrop.js` do przenoszenia zadań między grupami lub wewnątrz grup

## PHP
  - Logowanie `login.php`, rejestracja `sign.php`, wylogowanie `logut.php`, dodatkowo usunięcie konta `delete_account.php`
  - Zarządzanie użytkownikami oraz ich uprawnienia `manage_users.php`

## PostgreSQL
  - Utworzone dwa widoki `view_user_boards` oraz `view_tasks_info` 
  - Utworzona funkcja `set_task_created_at()`
  - Utworzony trigger `tasks_created_at_trigger`
  - Transakcję można wyszukać w kodzie "TRANSAKCJA", jest w pliku `RegisterController`, jest stosowana do utworzenia tablicy dla nowo zarejestrowanego użytkownika
  - Są wstawione przykładowe dane, konto istniejącego użytkownika: `email` - abc@mail.com, `hasło` - 123; lub można zarejestrować własnego
