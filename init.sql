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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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