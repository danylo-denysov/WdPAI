CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE IF NOT EXISTS boards (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    owner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

ALTER TABLE boards
    ADD CONSTRAINT fk_boards_owner
        FOREIGN KEY (owner_id)
            REFERENCES users (id)
            ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS board_users (
    board_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'viewer',
    PRIMARY KEY (board_id, user_id)
    );

ALTER TABLE board_users
    ADD CONSTRAINT fk_board_users_board
        FOREIGN KEY (board_id)
            REFERENCES boards (id)
            ON DELETE CASCADE;

ALTER TABLE board_users
    ADD CONSTRAINT fk_board_users_user
        FOREIGN KEY (user_id)
            REFERENCES users (id)
            ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS task_groups (
    id SERIAL PRIMARY KEY,
    board_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

ALTER TABLE task_groups
    ADD CONSTRAINT fk_task_groups_board
        FOREIGN KEY (board_id)
            REFERENCES boards (id)
            ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    group_id INT NOT NULL,
    assigned_user_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP,
    deadline TIMESTAMP,
    is_done BOOLEAN DEFAULT false
    );

ALTER TABLE tasks
    ADD CONSTRAINT fk_tasks_group
        FOREIGN KEY (group_id)
            REFERENCES task_groups (id)
            ON DELETE CASCADE;

ALTER TABLE tasks
    ADD CONSTRAINT fk_tasks_assigned_user
        FOREIGN KEY (assigned_user_id)
            REFERENCES users (id)
            ON DELETE SET NULL;


/*WIDOKI*/
CREATE OR REPLACE VIEW view_user_boards AS
SELECT
    b.id AS board_id,
    b.title AS board_title,
    b.owner_id AS owner_id,
    u.username AS owner_username,
    u.email AS owner_email,
    b.created_at AS board_created_at
FROM boards b
         JOIN users u ON b.owner_id = u.id
ORDER BY b.created_at DESC;

CREATE OR REPLACE VIEW view_tasks_info AS
SELECT
    t.id            AS task_id,
    t.title         AS task_title,
    t.description   AS task_description,
    t.position      AS task_position,
    tg.title        AS group_title,
    b.title         AS board_title,
    t.created_at    AS task_created_at
FROM tasks t
         JOIN task_groups tg ON t.group_id = tg.id
         JOIN boards b ON tg.board_id = b.id
ORDER BY b.id, tg.id, t.position;

/*FUNKCJA, TRIGGER*/
CREATE OR REPLACE FUNCTION set_task_created_at()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.created_at IS NULL THEN
        NEW.created_at := NOW();
END IF;
RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tasks_created_at_trigger
    BEFORE INSERT ON tasks
    FOR EACH ROW
    EXECUTE PROCEDURE set_task_created_at();


/*PRZYKLADOWE DANE*/
INSERT INTO users (email, username, password)
VALUES ('abc@mail.com', 'abc', '$2y$10$3xlcwGp9wkZu6EEpBNVko.DYV9SR5rTxfuYM6S6w0Zd5oYPGei8eu');

INSERT INTO boards (title, owner_id)
SELECT 'Tablica testowa', u.id
FROM users u
WHERE u.email = 'abc@mail.com'
    LIMIT 1;

INSERT INTO task_groups (board_id, title, position)
SELECT b.id, 'Grupa test', 0
FROM boards b
         JOIN users u ON b.owner_id = u.id
WHERE b.title = 'Tablica testowa'
  AND u.email = 'abc@mail.com'
    LIMIT 1;

INSERT INTO tasks (group_id, title, description, position)
SELECT tg.id, 'Zadanie startowe', 'Przykładowy opis', 0
FROM task_groups tg
         JOIN boards b ON tg.board_id = b.id
         JOIN users u ON b.owner_id = u.id
WHERE b.title = 'Tablica testowa'
  AND tg.title = 'Grupa test'
  AND u.email = 'abc@mail.com'
    LIMIT 1;