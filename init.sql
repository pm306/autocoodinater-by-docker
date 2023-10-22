-- ユーザーテーブルの追加
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(16) UNIQUE NOT NULL,
    password VARCHAR(40) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL
);

-- ゲストユーザーの追加
INSERT INTO members (name, password, email) VALUES ('ゲスト', SHA1('password'), "guest");

-- 服テーブルの追加
CREATE TABLE clothes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    picture VARCHAR(255) NOT NULL,
    last_used_date DATE
);

-- 服の種類テーブルの追加
CREATE TABLE clothes_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('tops', 'bottoms') NOT NULL,
    code VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL
);

-- 毎日洗濯しない服のテーブルの追加
CREATE TABLE not_laundry_everyday (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clothes_type_id INT NOT NULL,
    FOREIGN KEY (clothes_type_id) REFERENCES clothes_types(id) ON DELETE CASCADE
);

-- 服の種類のデータを追加（トップス）
INSERT INTO clothes_types (category, code, name) VALUES
('tops', 'inner', 'インナー'),
('tops', 't_short', 'Tシャツ(半袖)'),
('tops', 't_long', 'Tシャツ(長袖)'),
('tops', 'poro', 'ポロシャツ'),
('tops', 'check', 'チェックシャツ(薄)'),
('tops', 'check_thick', 'チェックシャツ(厚)'),
('tops', 'parker', 'パーカー'),
('tops', 'trainer', 'トレーナー'),
('tops', 'seta', 'セーター'),
('tops', 'cardigan', 'カーディガン'),
('tops', 'cardigan_chack', 'カーディガン(ﾁｬｯｸ)'),
('tops', 'outer_thin', 'アウター(薄)'),
('tops', 'outer_thick', 'アウター(厚)'),
('tops', 'other1', 'その他[半袖]'),
('tops', 'other2', 'その他[長袖(薄)]'),
('tops', 'other3', 'その他[長袖(厚)]'),
('tops', 'other4', 'その他[アウター]');

-- 服の種類のデータを追加（ボトムス）
INSERT INTO clothes_types (category, code, name) VALUES
('bottoms', 'chino_thin', 'チノパン(薄)'),
('bottoms', 'chino_thick', 'チノパン(厚)'),
('bottoms', 'other_b', 'その他[ボトムス]');

-- 毎日洗濯しない服の種類を追加
INSERT INTO not_laundry_everyday (clothes_type_id) 
SELECT id FROM clothes_types WHERE code IN ('seta', 'outer_thin', 'outer_thick');
