use mysql;
CREATE USER 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;

CREATE DATABASE blog_db;

USE blog_db;
CREATE table data
(
    `product_id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `VALUE` BIGINT,
    `KEY` BIGINT
) Engine = InnoDB;


USE blog_db;
CREATE TABLE student ( student_id INT AUTO_INCREMENT PRIMARY KEY, first_name VARCHAR(50), last_name  VARCHAR(50) );
INSERT INTO student (first_name, last_name) VALUES ('John', 'Thompson');
INSERT INTO student (first_name, last_name) VALUES ('Greg', 'Smith');
INSERT INTO student (first_name, last_name) VALUES ('Ray', 'Brown');


CREATE table populateData
(
    `product_id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `VALUE` BIGINT,
    `KEY` BIGINT
) Engine = InnoDB;

DELIMITER $$
CREATE PROCEDURE InsertPopulateData(valor INT)
BEGIN
    INSERT INTO populateData(`VALUE`, `KEY`)
        VALUES(valor,valor);
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE CallInsertPopulateData(
   qtd INT
)
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= qtd DO
        CALL InsertPopulateData(counter);
        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;

CALL CallInsertPopulateData(10000);
