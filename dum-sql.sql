CREATE TABLE task (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    priority INT DEFAULT NULL,
    is_completed BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id),
    INDEX (name)
);

use task;

INSERT INTO task(name, priority, is_completed) VALUES
('Acheter des legumes', 1, true),
('Finir le travail', 1, false),
('Aller a la maison', 1, false);