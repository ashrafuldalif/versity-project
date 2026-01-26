CREATE TABLE club_members (
    id INT AUTO_INCREMENT PRIMARY KEY,       
    img VARCHAR(255),          
    name VARCHAR(100) NOT NULL,
    department VARCHAR(50),    
    batch INT,                 
    mail VARCHAR(100) NOT NULL UNIQUE,      
    phone VARCHAR(20),                      
    pass VARCHAR(255) NOT NULL,             
    bloodGroup VARCHAR(5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP                   
);



CREATE TABLE clubs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE member_clubs (
    member_id INT NOT NULL,
    club_id INT NOT NULL,
    PRIMARY KEY(member_id, club_id),
    FOREIGN KEY(member_id) REFERENCES club_members(id) ON DELETE CASCADE,
    FOREIGN KEY(club_id) REFERENCES clubs(id) ON DELETE CASCADE
);



-- Images
CREATE TABLE gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    row_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--  Rows / Categories
CREATE TABLE gallery_rows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    row_header VARCHAR(255) NOT NULL,
    sub_header VARCHAR(255) DEFAULT NULL,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- 1. Positions Table (auto increment ID)
CREATE TABLE positions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(60) NOT NULL,
    short_form    VARCHAR(15) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert the 10 most common positions
INSERT INTO positions (position_name, short_form) VALUES
('President', 'Pres'),
('Vice President', 'VP'),
('General Secretary', 'GS'),
('Assistant General Secretary', 'AGS'),
('Treasurer', 'Treasurer'),
('Organizing Secretary', 'Org Sec'),
('Publication Secretary', 'Pub Sec'),
('IT Secretary', 'IT Sec'),
('Cultural Secretary', 'Cultural Sec'),
('Executive Member', 'Ex Mem');



CREATE TABLE executives (
    id INT UNSIGNED  PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position_id INT UNSIGNED NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    batch VARCHAR(20) NOT NULL,
    bio TEXT NULL,
    club_id INT  NULL,
    blood_group VARCHAR(5) NULL,
    approved BOOLEAN DEFAULT FALSE,
    active BOOLEAN DEFAULT TRUE,
    img VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add FKs AFTER table creation (safer)
ALTER TABLE executives 
ADD CONSTRAINT fk_position FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT fk_club FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Executive Socials Table (same ID as executives → 1:1 relationship)
CREATE TABLE executive_socials (
    executive_id INT UNSIGNED PRIMARY KEY,           -- same as executives.id
    facebook     VARCHAR(300) NULL,
    instagram    VARCHAR(300) NULL,
    linkedin     VARCHAR(300) NULL,
    x            VARCHAR(300) NULL,                  -- Twitter/X
    youtube      VARCHAR(300) NULL,

    FOREIGN KEY (executive_id) REFERENCES executives(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE upcomings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    heading     VARCHAR(255) NOT NULL,
    content     LONGTEXT NOT NULL,
    image       VARCHAR(512),
    image_side  ENUM('left', 'right') DEFAULT 'left',
    is_active   TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for better performance
CREATE INDEX idx_club_members_department ON club_members(department);
CREATE INDEX idx_club_members_batch ON club_members(batch);
CREATE INDEX idx_club_members_blood ON club_members(bloodGroup);
CREATE INDEX idx_gallery_images_row_id ON gallery_images(row_id);
CREATE INDEX idx_executives_club_id ON executives(club_id);
CREATE INDEX idx_executives_position_id ON executives(position_id);
CREATE INDEX idx_upcomings_active ON upcomings(is_active);

-- Add some basic clubs data
INSERT INTO clubs (name) VALUES 
('Music'),
('Sports'), 
('Cultural'),
('Art'),
('Drama'),
('Photography'),
('Programming'),
('Robotics'),
('Debate'),
('Volunteer')
ON DUPLICATE KEY UPDATE name = VALUES(name);