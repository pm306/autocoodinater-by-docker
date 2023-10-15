-- ユーザーテーブルの追加
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(16) UNIQUE NOT NULL,
    password VARCHAR(40) NOT NULL
);

-- ゲストユーザーの追加
INSERT INTO members (name, password) VALUES ('ゲスト', SHA1('password'));

-- 服テーブルの追加
CREATE TABLE clothes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    picture VARCHAR(255) NOT NULL,
    last_used_date DATE
);