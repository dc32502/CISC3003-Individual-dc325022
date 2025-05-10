CREATE TABLE users (
  users_id INT(11) NOT NULL AUTO_INCREMENT,
  users_uid VARCHAR(128) NOT NULL,
  users_email VARCHAR(128) NOT NULL,
  users_pwd VARCHAR(128) NOT NULL,
  PRIMARY KEY (users_id)
);

CREATE TABLE profiles (
  profiles_id INT(11) NOT NULL AUTO_INCREMENT,
  users_id INT(11) NOT NULL,
  profiles_about TEXT,
  profiles_introtitle VARCHAR(255),
  profiles_introtext TEXT,
  PRIMARY KEY (profiles_id),
  FOREIGN KEY (users_id) REFERENCES users(users_id)
);